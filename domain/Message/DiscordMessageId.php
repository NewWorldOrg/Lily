<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Base\BaseValue;

class DiscordMessageId extends BaseValue
{
    public function isOlderThan(self $discordMessageId): bool
    {
        $other = $discordMessageId->getRawValue();

        return (strlen($this->value) <=> strlen($other) ?: strcmp($this->value, $other)) < 0;
    }
}
