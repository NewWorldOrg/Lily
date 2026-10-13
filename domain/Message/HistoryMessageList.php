<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Base\BaseListValue;
use Domain\Common\CreatedAt;
use Domain\Exception\LogicException;

class HistoryMessageList extends BaseListValue
{
    public function selectBackfillTargets(CreatedAt $registeredAt, DiscordMessageIdList $trackedIdList): self
    {
        $targets = [];

        /** @var HistoryMessage $historyMessage */
        foreach ($this->value as $historyMessage) {
            if (
                $historyMessage->isPostedAfter($registeredAt)
                && !$trackedIdList->has($historyMessage->getDiscordMessageId())
            ) {
                $targets[] = $historyMessage;
            }
        }

        return new self($targets);
    }

    public function needsOlderPage(CreatedAt $registeredAt): bool
    {
        if ($this->isEmpty()) {
            return false;
        }

        /** @var HistoryMessage $historyMessage */
        foreach ($this->value as $historyMessage) {
            if (!$historyMessage->isPostedAfter($registeredAt)) {
                return false;
            }
        }

        return true;
    }

    public function getOldestDiscordMessageId(): DiscordMessageId
    {
        $oldest = null;

        /** @var HistoryMessage $historyMessage */
        foreach ($this->value as $historyMessage) {
            $discordMessageId = $historyMessage->getDiscordMessageId();

            if (is_null($oldest) || $discordMessageId->isOlderThan($oldest)) {
                $oldest = $discordMessageId;
            }
        }

        return $oldest ?? throw new LogicException('History message list is empty.');
    }

    public function getDiscordMessageIdList(): DiscordMessageIdList
    {
        $discordMessageIdList = [];

        /** @var HistoryMessage $historyMessage */
        foreach ($this->value as $historyMessage) {
            $discordMessageIdList[] = $historyMessage->getDiscordMessageId();
        }

        return new DiscordMessageIdList($discordMessageIdList);
    }
}
