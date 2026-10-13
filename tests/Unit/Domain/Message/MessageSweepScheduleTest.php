<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Message;

use Domain\Channel\ChannelId;
use Domain\Common\CommonTime;
use Domain\Message\DiscordMessageId;
use Domain\Message\Message;
use Domain\Message\MessageId;
use Domain\Message\MessageList;
use Domain\Message\MessageSweepSchedule;
use Tests\TestCase;

class MessageSweepScheduleTest extends TestCase
{
    private const int NOW = 1_700_000_000;

    public function testEveryMessageIsRequestableAtFirst(): void
    {
        $schedule = new MessageSweepSchedule();

        $this->assertSame(
            ['1001', '1002', '2001'],
            $this->requestableAt($schedule, self::NOW),
        );
    }

    public function testPendingMessageIsNotRequestableUntilReleased(): void
    {
        $schedule = new MessageSweepSchedule();
        $schedule->markPending($this->message(1, 1, '1001'));

        $this->assertSame(['1002', '2001'], $this->requestableAt($schedule, self::NOW));
        $this->assertSame(['1002', '2001'], $this->requestableAt($schedule, self::NOW + 3600));

        $schedule->release($this->message(1, 1, '1001'));

        $this->assertSame(['1001', '1002', '2001'], $this->requestableAt($schedule, self::NOW));
    }

    public function testPostponedMessageWaitsForTheCooldown(): void
    {
        $schedule = new MessageSweepSchedule();
        $schedule->postpone($this->message(1, 1, '1001'), new CommonTime(self::NOW));

        $this->assertSame(
            ['1002', '2001'],
            $this->requestableAt($schedule, self::NOW + MessageSweepSchedule::COOLDOWN_SECONDS - 1),
        );
        $this->assertSame(
            ['1001', '1002', '2001'],
            $this->requestableAt($schedule, self::NOW + MessageSweepSchedule::COOLDOWN_SECONDS),
        );
    }

    public function testPostponedChannelHoldsEveryMessageOfThatChannelForTheCooldown(): void
    {
        $schedule = new MessageSweepSchedule();
        $schedule->postponeChannel(new ChannelId(1), new CommonTime(self::NOW));

        $this->assertSame(
            ['2001'],
            $this->requestableAt($schedule, self::NOW + MessageSweepSchedule::COOLDOWN_SECONDS - 1),
        );
        $this->assertSame(
            ['1001', '1002', '2001'],
            $this->requestableAt($schedule, self::NOW + MessageSweepSchedule::COOLDOWN_SECONDS),
        );
    }

    public function testPostponingAgainRestartsTheCooldown(): void
    {
        $secondFailure = self::NOW + MessageSweepSchedule::COOLDOWN_SECONDS;
        $schedule = new MessageSweepSchedule();
        $schedule->postpone($this->message(1, 1, '1001'), new CommonTime(self::NOW));
        $schedule->postpone($this->message(1, 1, '1001'), new CommonTime($secondFailure));

        $this->assertSame(
            ['1002', '2001'],
            $this->requestableAt($schedule, $secondFailure + MessageSweepSchedule::COOLDOWN_SECONDS - 1),
        );
        $this->assertSame(
            ['1001', '1002', '2001'],
            $this->requestableAt($schedule, $secondFailure + MessageSweepSchedule::COOLDOWN_SECONDS),
        );
    }

    private function messageList(): MessageList
    {
        return new MessageList([
            $this->message(1, 1, '1001'),
            $this->message(2, 1, '1002'),
            $this->message(3, 2, '2001'),
        ]);
    }

    private function message(int $id, int $channelId, string $discordMessageId): Message
    {
        return new Message(
            new MessageId($id),
            new ChannelId($channelId),
            new DiscordMessageId($discordMessageId),
        );
    }

    /**
     * @return string[]
     */
    private function requestableAt(MessageSweepSchedule $schedule, int $now): array
    {
        return $schedule->selectRequestable($this->messageList(), new CommonTime($now))
            ->getDiscordMessageIdList()
            ->getRawValueList();
    }
}
