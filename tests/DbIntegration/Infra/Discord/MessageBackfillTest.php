<?php

declare(strict_types=1);

namespace Tests\DbIntegration\Infra\Discord;

use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Domain\Common\CreatedAt;
use Domain\Message\MessageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Infra\Discord\MessageBackfill;
use PHPUnit\Framework\MockObject\MockObject;
use React\Promise\PromiseInterface;
use Tests\DbIntegration\DbIntegrationTestCase;

use function React\Promise\reject;
use function React\Promise\resolve;

class MessageBackfillTest extends DbIntegrationTestCase
{
    private Discord&MockObject $discord;

    /**
     * @var array<string, Channel>
     */
    private array $discordChannels = [];

    /**
     * @var array<string, list<array<string, int|string>>>
     */
    private array $requestedOptions = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->discord = $this->createMock(Discord::class);
        $this->discord->method('getChannel')->willReturnCallback(
            fn (string $id) => $this->discordChannels[$id] ?? null
        );
    }

    public function testRegistersMessagesPostedAfterRegistrationPageByPage(): void
    {
        $this->registerChannel('100', '2026-10-13 12:00:00');
        $this->givenHistory('100', [
            [
                $this->discordMessage('1500', '2026-10-13T04:30:00+00:00'),
                $this->discordMessage('1400', '2026-10-13T04:00:00+00:00'),
            ],
            [
                $this->discordMessage('1300', '2026-10-13T03:30:00+00:00'),
                $this->discordMessage('1200', '2026-10-13T03:00:00+00:00'),
                $this->discordMessage('1100', '2026-10-13T02:00:00+00:00'),
            ],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $this->assertSame(
            [
                '1300' => '2026-10-13 12:30:00',
                '1400' => '2026-10-13 13:00:00',
                '1500' => '2026-10-13 13:30:00',
            ],
            $this->recordedMessages(),
        );
        $this->assertSame(
            [['limit' => 100], ['limit' => 100, 'before' => '1400']],
            $this->requestedOptions['100'],
        );
    }

    public function testStopsAtEndOfHistory(): void
    {
        $this->registerChannel('100', '2026-10-13 12:00:00');
        $this->givenHistory('100', [
            [$this->discordMessage('1500', '2026-10-13T04:30:00+00:00')],
            [],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $this->assertSame(['1500' => '2026-10-13 13:30:00'], $this->recordedMessages());
        $this->assertCount(2, $this->requestedOptions['100']);
    }

    public function testKeepsMessagesAlreadyTracked(): void
    {
        $channel = $this->registerChannel('100', '2026-10-13 12:00:00');
        DB::table('messages')->insert([
            'channel_id' => $channel,
            'discord_message_id' => '1400',
            'created_at' => '2026-10-13 13:00:05',
            'updated_at' => '2026-10-13 13:00:05',
        ]);
        $this->givenHistory('100', [
            [
                $this->discordMessage('1500', '2026-10-13T04:30:00+00:00'),
                $this->discordMessage('1400', '2026-10-13T04:00:00+00:00'),
                $this->discordMessage('1100', '2026-10-13T02:00:00+00:00'),
            ],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $this->assertSame(
            [
                '1400' => '2026-10-13 13:00:05',
                '1500' => '2026-10-13 13:30:00',
            ],
            $this->recordedMessages(),
        );
    }

    public function testFailureInOneChannelDoesNotStopTheOthers(): void
    {
        Log::spy();

        $this->registerChannel('100', '2026-10-13 12:00:00');
        $this->registerChannel('200', '2026-10-13 12:00:00');
        $this->registerChannel('300', '2026-10-13 12:00:00');
        $this->givenHistory('200', [
            [$this->discordMessage('2500', '2026-10-13T04:30:00+00:00')],
            new \RuntimeException('Missing Access'),
        ]);
        $this->givenHistory('300', [
            [$this->discordMessage('3500', '2026-10-13T04:30:00+00:00')],
            [],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $this->assertSame(
            [
                '2500' => '2026-10-13 13:30:00',
                '3500' => '2026-10-13 13:30:00',
            ],
            $this->recordedMessages(),
        );
        Log::shouldHaveReceived('error')->twice();
    }

    public function testSkipsChannelWithoutRegistrationTime(): void
    {
        Log::spy();

        DB::table('channels')->insert(['discord_channel_id' => '100', 'created_at' => null, 'updated_at' => null]);
        $this->givenHistory('100', [
            [$this->discordMessage('1500', '2026-10-13T04:30:00+00:00')],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $this->assertSame([], $this->recordedMessages());
        $this->assertArrayNotHasKey('100', $this->requestedOptions);
        Log::shouldHaveReceived('warning')->once();
    }

    public function testRegisteredMessagesAreSweptByPostedTime(): void
    {
        $this->registerChannel('100', '2026-10-10 12:00:00');
        $this->givenHistory('100', [
            [
                $this->discordMessage('1500', now()->subHour()->toIso8601String()),
                $this->discordMessage('1400', now()->subDays(2)->toIso8601String()),
            ],
            [],
        ]);

        $this->app->make(MessageBackfill::class)->run($this->discord);

        $due = $this->app->make(MessageRepository::class)->oneDayHasPassMessageList(
            CreatedAt::now()->subDay(1)
        );

        $this->assertSame(['1400'], $due->getDiscordMessageIdList()->getRawValueList());
    }

    private function registerChannel(string $discordChannelId, string $registeredAt): int
    {
        return DB::table('channels')->insertGetId([
            'discord_channel_id' => $discordChannelId,
            'created_at' => $registeredAt,
            'updated_at' => $registeredAt,
        ]);
    }

    /**
     * @param list<list<Message>|\Throwable> $pages
     */
    private function givenHistory(string $discordChannelId, array $pages): void
    {
        $channel = $this->createMock(Channel::class);
        $channel->method('getMessageHistory')->willReturnCallback(
            function (array $options) use ($discordChannelId, &$pages): PromiseInterface {
                $this->requestedOptions[$discordChannelId][] = $options;
                $page = array_shift($pages) ?? [];

                return $page instanceof \Throwable
                    ? reject($page)
                    : resolve(new Collection($page, 'id', Message::class));
            }
        );

        $this->discordChannels[$discordChannelId] = $channel;
    }

    private function discordMessage(string $id, string $timestamp): Message
    {
        return new Message($this->discord, ['id' => $id, 'timestamp' => $timestamp], true);
    }

    /**
     * @return array<string, string>
     */
    private function recordedMessages(): array
    {
        return DB::table('messages')
            ->orderBy('discord_message_id')
            ->pluck('created_at', 'discord_message_id')
            ->all();
    }
}
