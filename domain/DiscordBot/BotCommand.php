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
        try {
            $value = match ($displayName) {
                'hello' => self::HELLO,
                '薬物登録' => self::REGISTER_DRUG,
                'のんだ' => self::MEDICATION
            };
        } catch(\UnhandledMatchError $e) {
            return self::COMMAND_NOT_FOUND;
        }
        return $value;
    }

    public function getCommandArgumentClass(array $commandArgs)
    : RegisterDrugCommandArgument|MedicationCommandArgument {
        return match ($this) {
            self::REGISTER_DRUG => new RegisterDrugCommandArgument($commandArgs),
            self::MEDICATION => new MedicationCommandArgument($commandArgs),
        };
    }
}
