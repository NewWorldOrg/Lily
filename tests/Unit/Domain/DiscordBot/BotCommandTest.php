<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DiscordBot;

use Domain\Common\RawString;
use Domain\DiscordBot\BotCommand;
use Domain\Exception\InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BotCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    #[DataProvider('displayNameProvider')]
    public function testDisplayName(RawString $displayName, BotCommand $command)
    {
        $this->assertEquals(
            $displayName,
            $command->displayName(),
        );
    }

    public static function displayNameProvider(): array
    {
        return [
            'registerDrug' => [
                new RawString('薬物登録'),
                BotCommand::REGISTER_DRUG,
            ],
            'medication' => [
                new RawString('のんだ'),
                BOtCommand::MEDICATION,
            ],
        ];
    }

    #[DataProvider('makeFromDisplayNameProvider')]
    public function testMakeFromDisplayName(string $displayName, BotCommand $command)
    {
        $this->assertEquals(
            $command,
            BotCommand::makeFromDisplayName($displayName),
        );
    }

    public static function makeFromDisplayNameProvider(): array
    {
        return [
            [
                '薬物登録',
                BotCommand::REGISTER_DRUG,
            ],
            [
                'のんだ',
                BotCommand::MEDICATION,
            ]
        ];
    }
}
