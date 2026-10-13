<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Message;

use Domain\Channel\ChannelId;
use Domain\Message\DiscordMessageId;
use Domain\Message\Message;
use Domain\Message\MessageId;
use Domain\Message\MessageList;
use Tests\TestCase;

class MessageListTest extends TestCase
{
    public function testGetChannelIdListHasEachChannelOnce(): void
    {
        $messageList = new MessageList([
            $this->message(1, 1, '1001'),
            $this->message(2, 2, '2001'),
            $this->message(3, 1, '1002'),
            $this->message(4, 1, '1003'),
        ]);

        $this->assertSame(
            [1, 2],
            $messageList->getChannelIdList()->map(fn (ChannelId $channelId) => $channelId->getRawValue())->toArray(),
        );
    }

    public function testGetByChannelIdReturnsOnlyThatChannelsMessages(): void
    {
        $messageList = new MessageList([
            $this->message(1, 1, '1001'),
            $this->message(2, 2, '2001'),
            $this->message(3, 1, '1002'),
        ]);

        $this->assertSame(
            ['1001', '1002'],
            $messageList->getByChannelId(new ChannelId(1))->getDiscordMessageIdList()->map(
                fn (DiscordMessageId $discordMessageId) => $discordMessageId->getRawValue()
            )->toArray(),
        );
    }

    public function testGetByChannelIdReturnsEmptyListForChannelWithoutMessages(): void
    {
        $messageList = new MessageList([
            $this->message(1, 1, '1001'),
        ]);

        $this->assertTrue($messageList->getByChannelId(new ChannelId(2))->isEmpty());
    }

    private function message(int $id, int $channelId, string $discordMessageId): Message
    {
        return new Message(
            new MessageId($id),
            new ChannelId($channelId),
            new DiscordMessageId($discordMessageId),
        );
    }
}
