<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Common\CreatedAt;

readonly class HistoryMessage
{
    public function __construct(
        private DiscordMessageId $discordMessageId,
        private CreatedAt $createdAt,
    ) {
    }

    public function getDiscordMessageId(): DiscordMessageId
    {
        return $this->discordMessageId;
    }

    public function getCreatedAt(): CreatedAt
    {
        return $this->createdAt;
    }

    public function isPostedAfter(CreatedAt $createdAt): bool
    {
        return $this->createdAt->isGreaterThan($createdAt);
    }
}
