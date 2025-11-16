<?php

declare(strict_types=1);

namespace Infra\EloquentRepository;

use Domain\Channel\ChannelId;
use Domain\Common\CreatedAt;
use Domain\Message\DiscordMessageId;
use Domain\Message\DiscordMessageIdList;
use Domain\Message\MessageList;
use Domain\Message\MessageRepository as MessageRepositoryInterface;
use Infra\EloquentModels\Message;

class MessageRepository implements MessageRepositoryInterface
{
    public function create(
        ChannelId $channelId,
        DiscordMessageId $discordMessageId
    ): void {
        $model = new Message();

        $model->channel_id = $channelId->getRawValue();
        $model->discord_message_id = $discordMessageId->getRawValue();

        $model->save();
    }

    public function oneDayHasPassMessageList(CreatedAt $createdAt): MessageList
    {
        $collection = Message::where('created_at', '<=', $createdAt->getSqlTimeStamp())->get();

        return new MessageList(
            $collection->map(fn (Message $model) => $model->toDomain())->toArray(),
        );
    }

    public function deleteByDiscordMessageIdList(DiscordMessageIdList $discordMessageIdList): void
    {
        Message::whereIn(
            'discord_message_id',
            $discordMessageIdList->map(fn (DiscordMessageId $discordMessageId) => $discordMessageId->getRawValue())->toArray(),
        )->delete();
    }
}
