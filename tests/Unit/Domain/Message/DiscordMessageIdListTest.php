<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Message;

use Domain\Message\DiscordMessageId;
use Domain\Message\DiscordMessageIdList;
use Tests\TestCase;

class DiscordMessageIdListTest extends TestCase
{
    public function testHas(): void
    {
        $discordMessageIdList = new DiscordMessageIdList([
            new DiscordMessageId('1001'),
            new DiscordMessageId('1002'),
        ]);

        $this->assertTrue($discordMessageIdList->has(new DiscordMessageId('1002')));
        $this->assertFalse($discordMessageIdList->has(new DiscordMessageId('1003')));
    }

    public function testGetRawValueList(): void
    {
        $discordMessageIdList = new DiscordMessageIdList([
            new DiscordMessageId('1001'),
            new DiscordMessageId('1002'),
        ]);

        $this->assertSame(['1001', '1002'], $discordMessageIdList->getRawValueList());
    }
}
