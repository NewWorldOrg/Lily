<?php

declare(strict_types=1);

namespace Infra\Discord\EmbedHelper;

use Carbon\Carbon;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Domain\Drug\Drug;
use Domain\MedicationHistory\MedicationHistory;

class MedicationHistoryHelper
{
    private string $title = 'のんだ';

    public function __construct(private readonly Discord $discord, private readonly Message $message)
    {
    }

    public function toMedicationHistoryCreatedEmbed(MedicationHistory $medicationHistory, Drug $drug): Embed
    {
        $userAvatar = $this->message->author->getAvatarAttribute('png');
        $botAvatar = $this->discord->user->getAvatarAttribute('png');
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription(
            '<@'. $this->message->author->id . '>' . ' took '
            . $drug->getName()->getRawValue()
            . ' '
            . $medicationHistory->getAmount()->toFloat()
            . 'mg at '
            . $medicationHistory->getMedicationDate()->getDetail()
        );
        $embed->setColor('#eac645');
        $embed->setAuthor($this->message->author->username, $userAvatar);
        $embed->setThumbnail($botAvatar);
        $embed->setFooter('NewWorldMilitaryAdviser');

        return $embed;
    }

    public function toMedicationHistoryFailedEmbed(): Embed
    {
        $botAvatar = $this->discord->user->getAvatarAttribute('png');
        $embed = new Embed($this->discord);
        $embed->setTitle($this->title);
        $embed->setDescription('飲むな');
        $embed->setColor('#eac645');
        $embed->setAuthor($this->discord->user->username, $botAvatar);
        $embed->addFieldValues('失敗', 'のめませんでした', true);

        return $embed;
    }
}
