<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Base\BaseListValue;

class DiscordMessageIdList extends BaseListValue
{
    public function has(DiscordMessageId $discordMessageId): bool
    {
        /** @var DiscordMessageId $value */
        foreach ($this->value as $value) {
            if ($value->isEqual($discordMessageId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getRawValueList(): array
    {
        $rawValueList = [];

        /** @var DiscordMessageId $value */
        foreach ($this->value as $value) {
            $rawValueList[] = $value->getRawValue();
        }

        return $rawValueList;
    }
}
