<?php

declare(strict_types=1);

namespace Tests\DbIntegration\Domain\Channel;

use Domain\Channel\Channel;
use Domain\Channel\ChannelRepository;
use Domain\Channel\DiscordChannelId;
use Domain\Common\CreatedAt;
use Illuminate\Support\Facades\DB;
use Tests\DbIntegration\DbIntegrationTestCase;

class ChannelRepositoryTest extends DbIntegrationTestCase
{
    private ChannelRepository $channelRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->channelRepository = $this->app->make(ChannelRepository::class);
    }

    public function testGetChannelsReturnsRegistrationTime(): void
    {
        DB::table('channels')->insert([
            ['discord_channel_id' => '100', 'created_at' => '2026-10-01 09:00:00', 'updated_at' => '2026-10-01 09:00:00'],
            ['discord_channel_id' => '200', 'created_at' => '2026-10-02 21:30:15', 'updated_at' => '2026-10-02 21:30:15'],
        ]);

        $channels = [];

        /** @var Channel $channel */
        foreach ($this->channelRepository->getChannels() as $channel) {
            $channels[$channel->getDiscordChanelId()->getRawValue()] = $channel->getCreatedAt()?->getSqlTimeStamp();
        }

        $this->assertSame(
            ['100' => '2026-10-01 09:00:00', '200' => '2026-10-02 21:30:15'],
            $channels,
        );
    }

    public function testGetChannelsLeavesRegistrationTimeEmptyWhenUnknown(): void
    {
        DB::table('channels')->insert(['discord_channel_id' => '100', 'created_at' => null, 'updated_at' => null]);

        $channels = $this->channelRepository->getChannels();

        $this->assertCount(1, $channels);
        $this->assertNull($channels[0]->getCreatedAt());
    }

    public function testCreateRecordsRegistrationTime(): void
    {
        $before = CreatedAt::now();

        $channel = $this->channelRepository->create(new DiscordChannelId('100'));

        $this->assertNotNull($channel->getCreatedAt());
        $this->assertTrue($channel->getCreatedAt()->isGreaterOrEqualThan($before));
    }
}
