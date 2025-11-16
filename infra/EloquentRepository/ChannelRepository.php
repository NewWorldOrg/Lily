<?php

declare(strict_types=1);

namespace Infra\EloquentRepository;

use Domain\Channel\Channel;
use Domain\Channel\ChannelId;
use Domain\Channel\ChannelRepository as ChannelRepositoryInterface;
use Domain\Channel\DiscordChannelId;
use Domain\Exception\NotFoundException;
use Infra\EloquentModels\Channel as ChannelModel;

class ChannelRepository implements ChannelRepositoryInterface
{
    public function get(ChannelId $channelId): Channel
    {
        $model = ChannelModel::find($channelId->getRawValue());

        if (is_null($model)) {
            throw new NotFoundException();
        }

        return $model->toDomain();
    }

    public function getByDiscordChannelId(DiscordChannelId $discordChannelId): Channel
    {
        $model = ChannelModel::where(['discord_channel_id' => $discordChannelId->getRawValue()])->first();

        if (is_null($model)) {
            throw new NotFoundException();
        }

        return $model->toDomain();
    }

    public function existsByDiscordChannelId(DiscordChannelId $discordChannelId): bool
    {
        return ChannelModel::where(['discord_channel_id' => $discordChannelId->getRawValue()])->exists();
    }

    public function create(DiscordChannelId $discordChannelId): Channel
    {
        $model = new ChannelModel();

        $model->discord_channel_id = $discordChannelId->getRawValue();

        $model->save();

        return $model->toDomain();
    }

    public function deleteByDiscordChannelId(DiscordChannelId $discordChannelId): void
    {
        ChannelModel::where(['discord_channel_id' => $discordChannelId->getRawValue()])->delete();
    }
}
