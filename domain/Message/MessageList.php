<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Base\BaseListValue;
use Domain\Channel\ChannelId;
use Domain\Channel\ChannelIdList;

class MessageList extends BaseListValue
{
    public function getChannelIdList(): ChannelIdList
    {
        $channelIdList = new ChannelIdList(
            $this->map(fn (Message $message) => $message->getChannelId())->toArray()
        );

        $channelIdList->unique();

        return $channelIdList;
    }

    public function getDiscordMessageIdList(): DiscordMessageIdList
    {
        return new DiscordMessageIdList(
            $this->map(fn (Message $message) => $message->getDiscordMessageId())->toArray()
        );
    }

    public function getByChannelId(ChannelId $channelId): self
    {
        return $this->filter(fn (Message $message) => $message->getChannelId()->isEqual($channelId));
    }
}
