<?php

declare(strict_types=1);

namespace Domain\Channel;

use Domain\Common\CreatedAt;

readonly class Channel
{
    public function __construct(
        private ChannelId $id,
        private DiscordChannelId $discordChannelId,
        private ?CreatedAt $createdAt,
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

    public function getCreatedAt(): ?CreatedAt
    {
        return $this->createdAt;
    }
}
