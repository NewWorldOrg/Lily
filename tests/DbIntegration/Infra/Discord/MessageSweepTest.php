<?php

declare(strict_types=1);

namespace Tests\DbIntegration\Infra\Discord;

use Carbon\CarbonInterface;
use Discord\Discord;
use Discord\Http\Exceptions\NoPermissionsException;
use Discord\Http\Exceptions\NotFoundException;
use Discord\Http\Exceptions\RequestFailedException;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message;
use Discord\Repository\Channel\MessageRepository as DiscordMessageRepository;
use Domain\Message\MessageRepository;
use Domain\Message\MessageSweepSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Infra\Discord\MessageSweep;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Tests\DbIntegration\DbIntegrationTestCase;

class MessageSweepTest extends DbIntegrationTestCase
{
    private Discord&Stub $discord;

    /**
     * @var array<string, Channel>
     */
    private array $discordChannels = [];

    /**
     * @var array<string, Message>
     */
    private array $discordMessages = [];

    /**
     * @var array<string, array<string, \Throwable>>
     */
    private array $failures = [];

    /**
     * @var list<\Closure(): void>
     */
    private array $waitingResponses = [];

    /**
     * @var list<string>
     */
    private array $requests = [];

    /**
     * @var list<string>
     */
    private array $channelLookups = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->freezeTime();
        Log::spy();

        $this->discord = $this->createStub(Discord::class);
        $this->discord->method('getChannel')->willReturnCallback(function (string $id): ?Channel {
            $this->channelLookups[] = $id;

            return $this->discordChannels[$id] ?? null;
        });
    }

    public function testRemovesRecordOnlyAfterDiscordDeletedTheMessage(): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->recordMessage($channel, '1001', now()->subDays(2));
        $sweep = $this->app->make(MessageSweep::class);

        $sweep->run($this->discord);
        $this->respondToNextRequest();

        $this->assertSame(['fetch 1001', 'delete 1001'], $this->requests);
        $this->assertSame(['1001'], $this->recordedIds());

        $this->respondToNextRequest();

        $this->assertArrayNotHasKey('1001', $this->discordMessages);
        $this->assertSame([], $this->recordedIds());
        Log::shouldNotHaveReceived('error');
        Log::shouldNotHaveReceived('warning');
    }

    #[DataProvider('provideFailures')]
    public function testKeepsRecordWhenDiscordFailsAndWaitsForTheCooldown(string $stage, \Throwable $failure): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->recordMessage($channel, '1001', now()->subDays(2));
        $this->failures[$stage]['1001'] = $failure;
        $sweep = $this->app->make(MessageSweep::class);

        $sweep->run($this->discord);
        $this->respondToAllRequests();
        $requestsAfterFailure = $this->requests;

        $this->assertSame(['1001'], $this->recordedIds());

        $sweep->run($this->discord);
        $this->travel(MessageSweepSchedule::COOLDOWN_SECONDS - 1)->seconds();
        $sweep->run($this->discord);
        $this->respondToAllRequests();

        $this->assertSame($requestsAfterFailure, $this->requests);
        Log::shouldHaveReceived('error')->once();

        $this->travel(1)->seconds();
        $sweep->run($this->discord);

        $this->assertSame('fetch 1001', $this->requests[count($requestsAfterFailure)] ?? null);
    }

    public static function provideFailures(): array
    {
        return [
            'no access on fetch' => ['fetch', new NoPermissionsException('Forbidden - Missing Access', 50001)],
            'missing permissions on delete' => [
                'delete',
                new NoPermissionsException('Forbidden - Missing Permissions', 50013),
            ],
            'server error on delete' => ['delete', new RequestFailedException('Internal Server Error', 500)],
            'unknown channel on fetch' => ['fetch', new NotFoundException('Not Found - Unknown Channel', 10003)],
            'fetch throws instead of rejecting' => ['fetch-throws', new \RuntimeException('Unexpected failure')],
        ];
    }

    public function testRemovesRecordWhenDiscordSaysTheMessageIsUnknownOnFetch(): void
    {
        $channel = $this->registerChannel('100');
        $this->givenDiscordChannel('100');
        $this->recordMessage($channel, '1001', now()->subDays(2));

        $this->app->make(MessageSweep::class)->run($this->discord);
        $this->respondToAllRequests();

        $this->assertSame(['fetch 1001'], $this->requests);
        $this->assertSame([], $this->recordedIds());
        Log::shouldNotHaveReceived('error');
    }

    public function testRemovesRecordWhenDiscordSaysTheMessageIsUnknownOnDelete(): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->recordMessage($channel, '1001', now()->subDays(2));

        $this->app->make(MessageSweep::class)->run($this->discord);
        $this->respondToNextRequest();
        unset($this->discordMessages['1001']);
        $this->respondToNextRequest();

        $this->assertSame(['fetch 1001', 'delete 1001'], $this->requests);
        $this->assertSame([], $this->recordedIds());
        Log::shouldNotHaveReceived('error');
    }

    public function testRemovesRecordOfMessageTypeThatCannotBeDeletedWithWarning(): void
    {
        $channel = $this->registerChannel('100');
        $this->givenDiscordChannel('100');
        $this->discordMessages['1001'] = new Message(
            $this->discord,
            ['id' => '1001', 'type' => Message::TYPE_POLL_RESULT],
            true,
        );
        $this->recordMessage($channel, '1001', now()->subDays(2));

        $this->app->make(MessageSweep::class)->run($this->discord);
        $this->respondToAllRequests();

        $this->assertSame(['fetch 1001'], $this->requests);
        $this->assertSame([], $this->recordedIds());
        Log::shouldHaveReceived('warning')->once();
        Log::shouldNotHaveReceived('error');
    }

    public function testDoesNotRequestMessageAgainWhileItsRequestIsPending(): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->recordMessage($channel, '1001', now()->subDays(2));
        $sweep = $this->app->make(MessageSweep::class);

        $sweep->run($this->discord);
        $this->travel(1)->seconds();
        $sweep->run($this->discord);
        $this->respondToNextRequest();
        $this->travel(1)->seconds();
        $sweep->run($this->discord);

        $this->assertSame(['fetch 1001', 'delete 1001'], $this->requests);

        $this->respondToAllRequests();
        $sweep->run($this->discord);

        $this->assertSame(['fetch 1001', 'delete 1001'], $this->requests);
        $this->assertSame([], $this->recordedIds());
    }

    public function testKeepsRecordsOfChannelTheBotCannotResolveAndWaitsForTheCooldown(): void
    {
        $missing = $this->registerChannel('100');
        $available = $this->registerChannel('200');
        $this->postOnDiscord('200', '2001');
        $this->recordMessage($missing, '1001', now()->subDays(2));
        $this->recordMessage($missing, '1002', now()->subDays(2));
        $this->recordMessage($available, '2001', now()->subDays(2));
        $sweep = $this->app->make(MessageSweep::class);

        $sweep->run($this->discord);
        $this->respondToAllRequests();

        $this->assertSame(['1001', '1002'], $this->recordedIds());
        $this->assertSame(['fetch 2001', 'delete 2001'], $this->requests);
        Log::shouldHaveReceived('warning')->once();

        $this->travel(MessageSweepSchedule::COOLDOWN_SECONDS - 1)->seconds();
        $sweep->run($this->discord);

        $this->assertSame(['100', '200'], $this->channelLookups);

        $this->travel(1)->seconds();
        $sweep->run($this->discord);

        $this->assertSame(['100', '200', '100'], $this->channelLookups);
        $this->assertSame(['1001', '1002'], $this->recordedIds());
    }

    public function testLeavesMessagesYoungerThanOneDayAlone(): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->postOnDiscord('100', '1002');
        $this->recordMessage($channel, '1001', now()->subDay()->subSecond());
        $this->recordMessage($channel, '1002', now()->subDay()->addSecond());

        $this->app->make(MessageSweep::class)->run($this->discord);
        $this->respondToAllRequests();

        $this->assertSame(['fetch 1001', 'delete 1001'], $this->requests);
        $this->assertSame(['1002'], $this->recordedIds());
        $this->assertArrayHasKey('1002', $this->discordMessages);
    }

    public function testTwoSweepsOnTheSameRecordsRemoveThemWithoutErrors(): void
    {
        $channel = $this->registerChannel('100');
        $this->postOnDiscord('100', '1001');
        $this->postOnDiscord('100', '1002');
        $this->recordMessage($channel, '1001', now()->subDays(2));
        $this->recordMessage($channel, '1002', now()->subDays(2));
        $first = $this->app->make(MessageSweep::class);
        $second = $this->app->make(MessageSweep::class);

        $first->run($this->discord);
        $second->run($this->discord);
        $this->respondToAllRequests();
        $first->run($this->discord);
        $second->run($this->discord);

        $this->assertSame(
            [
                'fetch 1001', 'fetch 1002', 'fetch 1001', 'fetch 1002',
                'delete 1001', 'delete 1002', 'delete 1001', 'delete 1002',
            ],
            $this->requests,
        );
        $this->assertSame([], $this->discordMessages);
        $this->assertSame([], $this->recordedIds());
        Log::shouldNotHaveReceived('error');
        Log::shouldNotHaveReceived('warning');
    }

    public function testFailureToLoadRecordsIsLoggedAndNotThrown(): void
    {
        $messageRepository = $this->createStub(MessageRepository::class);
        $messageRepository->method('oneDayHasPassMessageList')->willThrowException(
            new \RuntimeException('Connection refused')
        );
        $this->app->instance(MessageRepository::class, $messageRepository);

        $this->app->make(MessageSweep::class)->run($this->discord);

        $this->assertSame([], $this->requests);
        Log::shouldHaveReceived('error')->once();
    }

    private function registerChannel(string $discordChannelId): int
    {
        return DB::table('channels')->insertGetId([
            'discord_channel_id' => $discordChannelId,
            'created_at' => '2026-10-01 12:00:00',
            'updated_at' => '2026-10-01 12:00:00',
        ]);
    }

    private function recordMessage(int $channel, string $discordMessageId, CarbonInterface $postedAt): void
    {
        DB::table('messages')->insert([
            'channel_id' => $channel,
            'discord_message_id' => $discordMessageId,
            'created_at' => $postedAt->toDateTimeString(),
            'updated_at' => $postedAt->toDateTimeString(),
        ]);
    }

    private function givenDiscordChannel(string $discordChannelId): void
    {
        if (isset($this->discordChannels[$discordChannelId])) {
            return;
        }

        $messages = $this->createStub(DiscordMessageRepository::class);
        $messages->method('fetch')->willReturnCallback(fn (string $id): PromiseInterface => $this->fetchOnDiscord($id));

        $channel = $this->createStub(Channel::class);
        $channel->method('__get')->willReturnCallback(
            fn (string $key) => $key === 'messages' ? $messages : null
        );

        $this->discordChannels[$discordChannelId] = $channel;
    }

    private function postOnDiscord(string $discordChannelId, string $id): void
    {
        $this->givenDiscordChannel($discordChannelId);

        $message = $this->createStub(Message::class);
        $message->method('isDeletable')->willReturn(true);
        $message->method('delete')->willReturnCallback(fn (): PromiseInterface => $this->deleteOnDiscord($id));

        $this->discordMessages[$id] = $message;
    }

    private function fetchOnDiscord(string $id): PromiseInterface
    {
        $this->requests[] = "fetch {$id}";

        if (isset($this->failures['fetch-throws'][$id])) {
            throw $this->failures['fetch-throws'][$id];
        }

        return $this->waitForResponse(function (Deferred $deferred) use ($id): void {
            match (true) {
                isset($this->failures['fetch'][$id]) => $deferred->reject($this->failures['fetch'][$id]),
                isset($this->discordMessages[$id]) => $deferred->resolve($this->discordMessages[$id]),
                default => $deferred->reject(self::unknownMessage()),
            };
        });
    }

    private function deleteOnDiscord(string $id): PromiseInterface
    {
        $this->requests[] = "delete {$id}";

        return $this->waitForResponse(function (Deferred $deferred) use ($id): void {
            if (isset($this->failures['delete'][$id])) {
                $deferred->reject($this->failures['delete'][$id]);
                return;
            }

            if (!isset($this->discordMessages[$id])) {
                $deferred->reject(self::unknownMessage());
                return;
            }

            unset($this->discordMessages[$id]);
            $deferred->resolve(null);
        });
    }

    /**
     * @param \Closure(Deferred): void $respond
     */
    private function waitForResponse(\Closure $respond): PromiseInterface
    {
        $deferred = new Deferred();
        $this->waitingResponses[] = fn () => $respond($deferred);

        return $deferred->promise();
    }

    private function respondToNextRequest(): void
    {
        $respond = array_shift($this->waitingResponses) ?? $this->fail('No request is waiting for a response.');
        $respond();
    }

    private function respondToAllRequests(): void
    {
        while ($this->waitingResponses !== []) {
            $this->respondToNextRequest();
        }
    }

    private static function unknownMessage(): NotFoundException
    {
        return new NotFoundException('Not Found - {"message": "Unknown Message", "code": 10008}', 10008);
    }

    /**
     * @return list<string>
     */
    private function recordedIds(): array
    {
        return DB::table('messages')->orderBy('discord_message_id')->pluck('discord_message_id')->all();
    }
}
