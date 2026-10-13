<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Channel\ChannelId;
use Domain\Common\CreatedAt;

interface MessageRepository
{
    public function create(ChannelId $channelId, DiscordMessageId $discordMessageId): void;
    public function createHistoryMessageList(ChannelId $channelId, HistoryMessageList $historyMessageList): void;
    public function getTrackedDiscordMessageIdList(DiscordMessageIdList $discordMessageIdList): DiscordMessageIdList;
    public function oneDayHasPassMessageList(CreatedAt $createdAt): MessageList;
    public function deleteByDiscordMessageIdList(DiscordMessageIdList $discordMessageIdList): void;
}
