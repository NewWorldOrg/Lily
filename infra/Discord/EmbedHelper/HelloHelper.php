<?php

declare(strict_types=1);

namespace Infra\Discord\EmbedHelper;

use Discord\Discord;
use Discord\Parts\Embed\Embed;

class HelloHelper
{
    private string $title = 'Hello';

    public function __construct(private readonly Discord $discord)
    {
    }

    public function convertIntoDiscordEmbed(): Embed
    {
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription('MilitaryAdvisor is watching you');
        $embed->setColor('#eac645');
        $embed->setAuthor(
            $this->discord->user->username,
            $this->discord->user->getAvatarAttribute('png')
        );

        return $embed;
    }
}
