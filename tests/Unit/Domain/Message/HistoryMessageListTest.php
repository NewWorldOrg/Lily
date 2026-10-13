<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Message;

use Domain\Common\CreatedAt;
use Domain\Exception\LogicException;
use Domain\Message\DiscordMessageId;
use Domain\Message\DiscordMessageIdList;
use Domain\Message\HistoryMessage;
use Domain\Message\HistoryMessageList;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HistoryMessageListTest extends TestCase
{
    public function testSelectBackfillTargetsKeepsOnlyMessagesPostedAfterRegistration(): void
    {
        $historyMessageList = new HistoryMessageList([
            $this->historyMessage('1300', '2026-10-13 12:00:01+09:00'),
            $this->historyMessage('1200', '2026-10-13 12:00:00+09:00'),
            $this->historyMessage('1100', '2026-10-13 11:59:59+09:00'),
        ]);

        $targets = $historyMessageList->selectBackfillTargets(
            CreatedAt::forStringTime('2026-10-13 12:00:00+09:00'),
            new DiscordMessageIdList([]),
        );

        $this->assertSame(['1300'], $this->rawIds($targets));
    }

    public function testSelectBackfillTargetsSkipsMessagesAlreadyTracked(): void
    {
        $historyMessageList = new HistoryMessageList([
            $this->historyMessage('1300', '2026-10-13 13:00:00+09:00'),
            $this->historyMessage('1200', '2026-10-13 12:30:00+09:00'),
            $this->historyMessage('1100', '2026-10-13 12:10:00+09:00'),
        ]);

        $targets = $historyMessageList->selectBackfillTargets(
            CreatedAt::forStringTime('2026-10-13 12:00:00+09:00'),
            new DiscordMessageIdList([new DiscordMessageId('1200')]),
        );

        $this->assertSame(['1300', '1100'], $this->rawIds($targets));
    }

    public function testSelectBackfillTargetsKeepsPostedTime(): void
    {
        $historyMessageList = new HistoryMessageList([
            $this->historyMessage('1300', '2026-10-13 13:00:00+09:00'),
        ]);

        $targets = $historyMessageList->selectBackfillTargets(
            CreatedAt::forStringTime('2026-10-13 12:00:00+09:00'),
            new DiscordMessageIdList([]),
        );

        $this->assertSame(
            CreatedAt::forStringTime('2026-10-13 13:00:00+09:00')->getRawValue(),
            $targets[0]->getCreatedAt()->getRawValue(),
        );
    }

    #[DataProvider('provideNeedsOlderPage')]
    public function testNeedsOlderPage(HistoryMessageList $historyMessageList, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $historyMessageList->needsOlderPage(CreatedAt::forStringTime('2026-10-13 12:00:00+09:00')),
        );
    }

    public static function provideNeedsOlderPage(): array
    {
        return [
            'every message posted after registration' => [
                new HistoryMessageList([
                    self::historyMessage('1300', '2026-10-13 13:00:00+09:00'),
                    self::historyMessage('1200', '2026-10-13 12:00:01+09:00'),
                ]),
                true,
            ],
            'a message posted at registration' => [
                new HistoryMessageList([
                    self::historyMessage('1300', '2026-10-13 13:00:00+09:00'),
                    self::historyMessage('1200', '2026-10-13 12:00:00+09:00'),
                ]),
                false,
            ],
            'a message posted before registration' => [
                new HistoryMessageList([
                    self::historyMessage('1300', '2026-10-13 13:00:00+09:00'),
                    self::historyMessage('1100', '2026-10-13 11:00:00+09:00'),
                ]),
                false,
            ],
            'end of history' => [
                new HistoryMessageList([]),
                false,
            ],
        ];
    }

    public function testGetOldestDiscordMessageIdComparesIdsAsNumbers(): void
    {
        $historyMessageList = new HistoryMessageList([
            $this->historyMessage('1000', '2026-10-13 13:00:00+09:00'),
            $this->historyMessage('999', '2026-10-13 12:59:00+09:00'),
            $this->historyMessage('1001', '2026-10-13 13:01:00+09:00'),
        ]);

        $this->assertSame('999', $historyMessageList->getOldestDiscordMessageId()->getRawValue());
    }

    public function testGetOldestDiscordMessageIdOfEmptyListThrows(): void
    {
        $this->expectException(LogicException::class);

        (new HistoryMessageList([]))->getOldestDiscordMessageId();
    }

    private static function historyMessage(string $discordMessageId, string $postedAt): HistoryMessage
    {
        return new HistoryMessage(
            new DiscordMessageId($discordMessageId),
            CreatedAt::forStringTime($postedAt),
        );
    }

    /**
     * @return string[]
     */
    private function rawIds(HistoryMessageList $historyMessageList): array
    {
        return $historyMessageList->getDiscordMessageIdList()->getRawValueList();
    }
}
