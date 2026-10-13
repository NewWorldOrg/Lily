<?php

declare(strict_types=1);

namespace Tests\DbIntegration\Domain\Message;

use Domain\Channel\ChannelId;
use Domain\Channel\ChannelRepository;
use Domain\Channel\DiscordChannelId;
use Domain\Common\CreatedAt;
use Domain\Message\DiscordMessageId;
use Domain\Message\DiscordMessageIdList;
use Domain\Message\HistoryMessage;
use Domain\Message\HistoryMessageList;
use Domain\Message\MessageRepository;
use Illuminate\Support\Facades\DB;
use Tests\DbIntegration\DbIntegrationTestCase;

class MessageRepositoryTest extends DbIntegrationTestCase
{
    private MessageRepository $messageRepository;
    private ChannelId $channelId;

    public function setUp(): void
    {
        parent::setUp();

        $this->messageRepository = $this->app->make(MessageRepository::class);
        $this->channelId = $this->app->make(ChannelRepository::class)->create(new DiscordChannelId('100'))->getId();
    }

    public function testGetTrackedDiscordMessageIdListReturnsOnlyRecordedIds(): void
    {
        $this->messageRepository->create($this->channelId, new DiscordMessageId('1001'));
        $this->messageRepository->create($this->channelId, new DiscordMessageId('1003'));

        $tracked = $this->messageRepository->getTrackedDiscordMessageIdList(new DiscordMessageIdList([
            new DiscordMessageId('1001'),
            new DiscordMessageId('1002'),
            new DiscordMessageId('1003'),
        ]));

        $rawValueList = $tracked->getRawValueList();
        sort($rawValueList);

        $this->assertSame(['1001', '1003'], $rawValueList);
    }

    public function testGetTrackedDiscordMessageIdListOfEmptyListIsEmpty(): void
    {
        $this->messageRepository->create($this->channelId, new DiscordMessageId('1001'));

        $this->assertTrue(
            $this->messageRepository->getTrackedDiscordMessageIdList(new DiscordMessageIdList([]))->isEmpty()
        );
    }

    public function testCreateHistoryMessageListRecordsPostedTime(): void
    {
        $this->messageRepository->createHistoryMessageList($this->channelId, new HistoryMessageList([
            new HistoryMessage(new DiscordMessageId('1001'), CreatedAt::forStringTime('2026-10-12 08:15:30')),
            new HistoryMessage(new DiscordMessageId('1002'), CreatedAt::forStringTime('2026-10-13 10:00:00')),
        ]));

        $this->assertSame(
            [
                ['1001', $this->channelId->getRawValue(), '2026-10-12 08:15:30'],
                ['1002', $this->channelId->getRawValue(), '2026-10-13 10:00:00'],
            ],
            DB::table('messages')->orderBy('discord_message_id')->get()->map(fn (object $row) => [
                $row->discord_message_id,
                $row->channel_id,
                $row->created_at,
            ])->all(),
        );
    }

    public function testCreatedHistoryMessageBecomesDueOneDayAfterPosting(): void
    {
        $this->messageRepository->createHistoryMessageList($this->channelId, new HistoryMessageList([
            new HistoryMessage(new DiscordMessageId('1001'), CreatedAt::now()->subDay(2)),
            new HistoryMessage(new DiscordMessageId('1002'), CreatedAt::now()->subHour(1)),
        ]));

        $due = $this->messageRepository->oneDayHasPassMessageList(CreatedAt::now()->subDay(1));

        $this->assertSame(['1001'], $due->getDiscordMessageIdList()->getRawValueList());
    }
}
