<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Message;

use Domain\Message\DiscordMessageId;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DiscordMessageIdTest extends TestCase
{
    #[DataProvider('provideIsOlderThan')]
    public function testIsOlderThan(string $id, string $other, bool $expected): void
    {
        $this->assertSame(
            $expected,
            (new DiscordMessageId($id))->isOlderThan(new DiscordMessageId($other)),
        );
    }

    public static function provideIsOlderThan(): array
    {
        return [
            'smaller id of the same length' => ['1427000000000000000', '1427000000000000001', true],
            'larger id of the same length' => ['1427000000000000001', '1427000000000000000', false],
            'shorter id' => ['999999999999999999', '1000000000000000000', true],
            'longer id' => ['1000000000000000000', '999999999999999999', false],
            'same id' => ['1427000000000000000', '1427000000000000000', false],
        ];
    }
}
