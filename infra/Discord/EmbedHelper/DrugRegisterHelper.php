<?php

declare(strict_types=1);

namespace Infra\Discord\EmbedHelper;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Domain\Drug\Drug;

class DrugRegisterHelper
{
    private string $title = '薬物登録';

    public function __construct(private readonly Discord $discord, private readonly Message $message)
    {
    }

    public function convertInToDiscordEmbed(Drug $drug): Embed
    {
        $userAvatar = $this->message->author->getAvatarAttribute('png');
        $botAvatar = $this->discord->user->getAvatarAttribute('png');
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription($drug->getName()->getRawValue() . 'を登録しました');
        $embed->setColor('#eac645');
        $embed->setAuthor($this->message->author->username, $userAvatar);
        $embed->setThumbnail($botAvatar);
        $embed->setFooter('NewWorldMilitaryAdviser');

        return $embed;
    }

    public function convertIntoDiscordEmbedFailure(): Embed
    {
        $botAvatar = $this->discord->user->getAvatarAttribute('png');
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription('見てるんだぞ');
        $embed->setColor('#eac645');
        $embed->setAuthor($this->discord->user->username, $botAvatar);
        $embed->addFieldValues('失敗', '薬物の登録に失敗しました', true);

        return $embed;
    }
}
