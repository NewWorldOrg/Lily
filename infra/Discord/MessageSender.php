<?php

declare(strict_types=1);

namespace Infra\Discord;

use Discord\Builders\MessageBuilder;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;

class MessageSender
{
    public function __construct()
    {
    }

    public function sendEmbed(Message $message, Embed $embed): void
    {
        try {
            $message->channel->sendMessage(MessageBuilder::new()->addEmbed($embed));
        } catch (\Exception $e) {
            $message->channel->sendMessage($e->getMessage());
        }
    }
}
