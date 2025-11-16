<?php

declare(strict_types=1);

namespace Domain\DiscordBot;

use Domain\Base\BaseEnum;
use Domain\Common\RawString;
use Domain\DiscordBot\CommandArgument\MedicationCommandArgument;
use Domain\DiscordBot\CommandArgument\RegisterDrugCommandArgument;

enum BotCommand: string implements BaseEnum
{
    case REGISTER_DRUG = 'registerDrug';
    case MEDICATION = 'medication';
    case INIT_SLASH_COMMANDS = 'initSlashCommands';
    case COMMAND_NOT_FOUND = 'commandNotFound';

    public function displayName(): RawString
    {
        try {
            return match($this) {
                self::REGISTER_DRUG => new RawString('薬物登録'),
                self::MEDICATION => new RawString('のんだ'),
                self::INIT_SLASH_COMMANDS => 'initSlashCommands',
            };
        } catch ( \UnhandledMatchError) {
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
            '薬物登録' => self::REGISTER_DRUG,
            'のんだ' => self::MEDICATION,
            'initSlashCommands' => self::INIT_SLASH_COMMANDS,
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

    public function isRegisterDrug(): bool
    {
        return $this === self::REGISTER_DRUG;
    }

    public function isMedication(): bool
    {
        return $this === self::MEDICATION;
    }

    public function isInitSlashCommands(): bool
    {
        return $this === self::INIT_SLASH_COMMANDS;
    }
}
