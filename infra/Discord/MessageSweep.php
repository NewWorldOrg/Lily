<?php

declare(strict_types=1);

namespace Infra\Discord;

use Discord\Discord;
use Discord\Http\Exceptions\NotFoundException as DiscordNotFoundException;
use Discord\Parts\Channel\Channel;
use Discord\Parts\Channel\Message as DiscordMessage;
use Discord\Parts\Part;
use Domain\Channel\ChannelId;
use Domain\Channel\ChannelRepository;
use Domain\Common\CommonTime;
use Domain\Common\CreatedAt;
use Domain\Message\DiscordMessageIdList;
use Domain\Message\Message;
use Domain\Message\MessageList;
use Domain\Message\MessageRepository;
use Domain\Message\MessageSweepSchedule;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class MessageSweep
{
    private const int UNKNOWN_MESSAGE = 10008;

    public function __construct(
        private readonly ChannelRepository $channelRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageSweepSchedule $schedule,
    ) {
    }

    public function run(Discord $discord): void
    {
        try {
            $now = $this->now();
            $messageList = $this->schedule->selectRequestable(
                $this->messageRepository->oneDayHasPassMessageList((new CreatedAt($now->timeStamp()))->subDay(1)),
                $now,
            );

            /** @var ChannelId $channelId */
            foreach ($messageList->getChannelIdList() as $channelId) {
                $this->sweepChannel($discord, $channelId, $messageList->getByChannelId($channelId), $now);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to sweep messages: {$e->getMessage()}", ['exception' => $e]);
        }
    }

    private function sweepChannel(
        Discord $discord,
        ChannelId $channelId,
        MessageList $messageList,
        CommonTime $now,
    ): void {
        try {
            $discordChannelId = $this->channelRepository->get($channelId)->getDiscordChanelId()->getRawValue();
            $discordChannel = $discord->getChannel($discordChannelId);

            if (is_null($discordChannel)) {
                $this->schedule->postponeChannel($channelId, $now);
                Log::warning("Skipped sweeping channel {$discordChannelId} because the bot cannot resolve it.");
                return;
            }

            /** @var Message $message */
            foreach ($messageList as $message) {
                $this->sweepMessage($discordChannel, $message);
            }
        } catch (\Throwable $e) {
            $this->schedule->postponeChannel($channelId, $now);
            Log::error("Failed to sweep channel {$channelId->getRawValue()}: {$e->getMessage()}", ['exception' => $e]);
        }
    }

    private function sweepMessage(Channel $discordChannel, Message $message): void
    {
        $discordMessageId = $message->getDiscordMessageId()->getRawValue();

        $this->schedule->markPending($message);

        resolve(null)
            ->then(fn () => $discordChannel->messages->fetch($discordMessageId))
            ->then(fn (Part $discordMessage) => $this->deleteOnDiscord($discordMessage))
            ->then(
                fn () => $this->forget($message),
                function (\Throwable $e) use ($message): void {
                    if (!$this->isUnknownMessage($e)) {
                        throw $e;
                    }

                    $this->forget($message);
                },
            )
            ->catch(function (\Throwable $e) use ($message, $discordMessageId): void {
                $this->schedule->postpone($message, $this->now());
                Log::error("Failed to sweep message {$discordMessageId}: {$e->getMessage()}", ['exception' => $e]);
            })
            ->finally(fn () => $this->schedule->release($message));
    }

    /**
     * @return PromiseInterface<mixed>
     */
    private function deleteOnDiscord(Part $discordMessage): PromiseInterface
    {
        /** @var DiscordMessage $discordMessage */
        if (!$discordMessage->isDeletable()) {
            Log::warning(
                "Dropping message {$discordMessage->id}: Discord does not delete type {$discordMessage->type}."
            );

            return resolve(null);
        }

        return $discordMessage->delete();
    }

    private function forget(Message $message): void
    {
        $this->messageRepository->deleteByDiscordMessageIdList(
            new DiscordMessageIdList([$message->getDiscordMessageId()])
        );
    }

    private function isUnknownMessage(\Throwable $e): bool
    {
        return $e instanceof DiscordNotFoundException && $e->getCode() === self::UNKNOWN_MESSAGE;
    }

    private function now(): CommonTime
    {
        return new CommonTime(Date::now()->getTimestamp());
    }
}
