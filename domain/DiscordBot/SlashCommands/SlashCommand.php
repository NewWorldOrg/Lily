<?php

declare(strict_types=1);

namespace Domain\DiscordBot\SlashCommands;

use Domain\Base\BaseEnum;
use Domain\Common\RawString;

enum SlashCommand: string implements BaseEnum
{
    case CLEANUP_HISTORY = 'cleanup-history';
    case REGISTER_CHANNEL = 'register-channel';

    public function getValue(): RawString
    {
        return new RawString($this->value);
    }

    public function displayName(): RawString
    {
        return match ($this) {
            self::CLEANUP_HISTORY => new RawString('一括削除'),
            self::REGISTER_CHANNEL => new RawString('チャンネル登録'),
        };
    }

    public function getDescription(): Description
    {
        return match ($this) {
            self::CLEANUP_HISTORY => new Description('直近100件のログを一括削除します'),
            self::REGISTER_CHANNEL => new Description('チャンネルを監視対象に追加'),
        };
    }

    public static function toArray(): SlashCommandList
    {
        return new SlashCommandList(
            [self::CLEANUP_HISTORY, self::REGISTER_CHANNEL]
        );
    }
}
