<?php

declare(strict_types=1);

namespace Infra\Discord\EmbedHelper;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;

class CommandNotFoundHelper
{
    private string $title = '実装されていないコマンドです';

    public function __construct(private readonly Discord $discord, private readonly Message $message)
    {
    }

    public function convertIntoDiscordEmbed(): Embed
    {
        $userAvatar = $this->message->author->getAvatarAttribute('png');
        $botAvatar = $this->discord->user->getAvatarAttribute('png');
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription('コマンドが見つかりませんでした');
        $embed->setColor('#eac645');
        $embed->setAuthor($this->message->author->username, $userAvatar);
        $embed->setThumbnail($botAvatar);
        $embed->setFooter('NewWorldMilitaryAdviser');

        return $embed;
    }
}
