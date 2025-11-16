<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Channel\ChannelId;

readonly class Message
{
    public function __construct(
        private MessageId $id,
        private ChannelId $channelId,
        private DiscordMessageId $discordMessageId,
    ) {
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getChannelId(): ChannelId
    {
        return $this->channelId;
    }

    public function getDiscordMessageId(): DiscordMessageId
    {
        return $this->discordMessageId;
    }
}
