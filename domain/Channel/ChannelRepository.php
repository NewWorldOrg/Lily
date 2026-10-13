<?php

declare(strict_types=1);

namespace Domain\Channel;

interface ChannelRepository
{
    public function get(ChannelId $channelId): Channel;
    public function getChannels(): ChannelList;
    public function getByDiscordChannelId(DiscordChannelId $discordChannelId): Channel;
    public function existsByDiscordChannelId(DiscordChannelId $discordChannelId): bool;
    public function create(DiscordChannelId $discordChannelId): Channel;
    public function deleteByDiscordChannelId(DiscordChannelId $discordChannelId): void;
}
