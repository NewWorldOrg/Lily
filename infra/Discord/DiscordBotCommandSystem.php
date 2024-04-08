<?php

namespace Infra\Discord;

use Discord\Discord;
use Discord\Parts\Channel\Message;
use Domain\Common\RawString;
use Domain\DiscordBot\CommandArgument\MedicationCommandArgument;
use Domain\DiscordBot\CommandArgument\RegisterDrugCommandArgument;
use Domain\Drug\DrugName;
use Domain\Drug\DrugRepository;
use Domain\Drug\DrugUrl;
use Domain\Exception\InvalidArgumentException;
use Domain\Exception\NotFoundException;
use Domain\MedicationHistory\MedicationHistoryRepository;
use Domain\User\UserId;
use Domain\User\UserRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Infra\Discord\EmbedHelper\CommandNotFoundHelper;
use Infra\Discord\EmbedHelper\DrugRegisterHelper;
use Infra\Discord\EmbedHelper\HelloHelper;
use Infra\Discord\EmbedHelper\MedicationHistoryHelper;

class DiscordBotCommandSystem
{
    private RawString $wikiApiUrl;
    private RawString $wikiViewPageUrl;
    private MessageSender $messageSender;

    public function __construct (
        private DrugRepository $drugRepository,
        private MedicationHistoryRepository $medicationHistoryRepository,
        private UserRepository $userRepository,
    ) {
        $this->wikiApiUrl = new RawString('https://ja.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro&explaintext&redirects=1&titles=');
        $this->wikiViewPageUrl = new RawString('https://ja.wikipedia.org/wiki/');
    }

    public function hello(Discord $discord, Message $message): void
    {
        $this->messageSender = new MessageSender();
        $this->messageSender->sendEmbed(
            $message,
            (new HelloHelper($discord))->convertIntoDiscordEmbed(),
        );
    }

    public function registerDrug(
        RegisterDrugCommandArgument $args,
        Discord $discord,
        Message $message,
    ): void {
        $this->messageSender = new MessageSender();
        $drugRegisterHelper = new DrugRegisterHelper($discord, $message);
        $drugName = $args->getDrugName();

        $result = Http::get($this->wikiApiUrl . $drugName->getRawValue());

        if (empty($result) || isset($result['query']['redirects'])) {
            $this->messageSender->sendEmbed(
                $message,
                $drugRegisterHelper->convertIntoDiscordEmbedFailure(),
            );
        }

        foreach ($result['query']['pages'] as $key => $value) {
            if (isset($value['title'])) {
                $drugName = new DrugName($value['title']);
            }
        }

        try {
            $url = new DrugUrl($this->wikiViewPageUrl->getRawValue() . $drugName->getRawValue());
            $drug = $this->drugRepository->create(
                $drugName,
                $url,
            );

            $this->messageSender->sendEmbed(
                $message,
                $drugRegisterHelper->convertInToDiscordEmbed($drug),
            );
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            $this->messageSender->sendEmbed(
                $message,
                $drugRegisterHelper->convertIntoDiscordEmbedFailure(),
            );
        }
    }

    public function medication(
        MedicationCommandArgument $args,
        Discord $discord,
        Message $message,
    ): void {
        $this->messageSender = new MessageSender();

        $medicationHistoryHelper = new MedicationHistoryHelper($discord, $message);

        try {
            $drug = $this->drugRepository->findDrugByName(
                $args->getDrugName()
            );

            $user = $this->userRepository->getUserByUserId(new UserId((int)$message->author->id));

            $medicationHistory = $this->medicationHistoryRepository->create(
                $user->getId(),
                $drug->getId(),
                $args->getAmount(),
            );

            $this->messageSender->sendEmbed(
                $message,
                $medicationHistoryHelper->toMedicationHistoryCreatedEmbed($medicationHistory, $drug),
            );
        } catch (InvalidArgumentException | NotFoundException $e) {
            Log::error($e->getMessage());
            $this->messageSender->sendEmbed(
                $message,
                $medicationHistoryHelper->toMedicationHistoryFailedEmbed(),
            );
        }
    }

    public function commandNotFound(Discord $discord, Message $message): void
    {
        $this->messageSender = new MessageSender();
        $commandNotFoundHelper = new CommandNotFoundHelper($discord, $message);

        $embed = $commandNotFoundHelper->convertIntoDiscordEmbed();

        $this->messageSender->sendEmbed($message, $embed);
    }
}
