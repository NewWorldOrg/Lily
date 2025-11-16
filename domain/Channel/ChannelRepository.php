<?php

declare(strict_types=1);

namespace Domain\Channel;

Interface ChannelRepository
{
    public function get(ChannelId $channelId): Channel;
    public function getByDiscordChannelId(DiscordChannelId $discordChannelId): Channel;
    public function existsByDiscordChannelId(DiscordChannelId $discordChannelId): bool;
    public function create(DiscordChannelId $discordChannelId): Channel;
    public function deleteByDiscordChannelId(DiscordChannelId $discordChannelId): void;
}
