<?php

declare(strict_types=1);

namespace Domain\Channel;

readonly class Channel
{
    public function __construct(
        private ChannelId $id,
        private DiscordChannelId $discordChannelId,
    ) {
    }

    public function getId(): ChannelId
    {
        return $this->id;
    }

    public function getDiscordChanelId(): DiscordChannelId
    {
        return $this->discordChannelId;
    }
}
