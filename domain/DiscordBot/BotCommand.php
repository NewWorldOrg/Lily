<?php

declare(strict_types=1);

namespace Domain\DiscordBot;

use Domain\Base\BaseEnum;
use Domain\Common\RawString;
use Domain\DiscordBot\CommandArgument\MedicationCommandArgument;
use Domain\DiscordBot\CommandArgument\RegisterDrugCommandArgument;

enum BotCommand: string implements BaseEnum
{
    case HELLO = 'hello';
    case REGISTER_DRUG = 'registerDrug';
    case MEDICATION = 'medication';
    case COMMAND_NOT_FOUND = 'commandNotFound';

    public function displayName(): RawString
    {
        try {
            return match($this) {
                self::HELLO => new RawString('hello'),
                self::REGISTER_DRUG => new RawString('薬物登録'),
                self::MEDICATION => new RawString('のんだ'),
            };
        } catch ( \UnhandledMatchError $e) {
            return new RawString('実装されていないコマンドです');
        }
    }

    public function getValue(): RawString
    {
        return new RawString($this->value);
    }

    public static function makeFromDisplayName(string $displayName): self
    {
        return match ($displayName) {
            'hello' => self::HELLO,
            '薬物登録' => self::REGISTER_DRUG,
            'のんだ' => self::MEDICATION,
            default => self::COMMAND_NOT_FOUND,
        };
    }

    public function getCommandArgumentClass(array $commandArgs)
    : RegisterDrugCommandArgument|MedicationCommandArgument {
        return match ($this) {
            self::REGISTER_DRUG => new RegisterDrugCommandArgument($commandArgs),
            self::MEDICATION => new MedicationCommandArgument($commandArgs),
        };
    }

    public function isHello(): bool
    {
        return $this === self::HELLO;
    }

    public function isRegisterDrug(): bool
    {
        return $this === self::REGISTER_DRUG;
    }

    public function isMedication(): bool
    {
        return $this === self::MEDICATION;
    }

    public function isCommandNotFound(): bool
    {
        return $this === self::COMMAND_NOT_FOUND;
    }
}
