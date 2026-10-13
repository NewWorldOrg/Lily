<?php

declare(strict_types=1);

namespace Infra\Discord;

use Discord\Discord;
use Discord\Helpers\ExCollectionInterface;
use Discord\Parts\Channel\Message;
use Domain\Channel\Channel;
use Domain\Channel\ChannelRepository;
use Domain\Common\CreatedAt;
use Domain\Exception\NotFoundException;
use Domain\Message\DiscordMessageId;
use Domain\Message\HistoryMessage;
use Domain\Message\HistoryMessageList;
use Domain\Message\MessageRepository;
use Illuminate\Support\Facades\Log;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class MessageBackfill
{
    private const int HISTORY_PAGE_SIZE = 100;

    public function __construct(
        private readonly ChannelRepository $channelRepository,
        private readonly MessageRepository $messageRepository,
    ) {
    }

    public function run(Discord $discord): void
    {
        try {
            $channelList = $this->channelRepository->getChannels();
        } catch (\Throwable $e) {
            Log::error("Failed to load channels to backfill: {$e->getMessage()}", ['exception' => $e]);
            return;
        }

        /** @var Channel $channel */
        foreach ($channelList as $channel) {
            $discordChannelId = $channel->getDiscordChanelId()->getRawValue();
            $registeredAt = $channel->getCreatedAt();

            if (is_null($registeredAt)) {
                Log::warning("Skipped backfilling channel {$discordChannelId} because its registration time is unknown.");
                continue;
            }

            resolve(null)
                ->then(fn () => $this->backfillHistoryPage($discord, $channel, $registeredAt, null))
                ->catch(function (\Throwable $e) use ($discordChannelId) {
                    Log::error("Failed to backfill channel {$discordChannelId}: {$e->getMessage()}", ['exception' => $e]);
                });
        }
    }

    /**
     * @return PromiseInterface<null>
     */
    private function backfillHistoryPage(
        Discord $discord,
        Channel $channel,
        CreatedAt $registeredAt,
        ?DiscordMessageId $before,
    ): PromiseInterface {
        $discordChannelId = $channel->getDiscordChanelId()->getRawValue();
        $discordChannel = $discord->getChannel($discordChannelId)
            ?? throw new NotFoundException("Channel {$discordChannelId} is not found.");

        $options = ['limit' => self::HISTORY_PAGE_SIZE];

        if (!is_null($before)) {
            $options['before'] = $before->getRawValue();
        }

        return $discordChannel->getMessageHistory($options)->then(
            function (ExCollectionInterface $messages) use ($discord, $channel, $registeredAt): ?PromiseInterface {
                $historyMessageList = $this->toHistoryMessageList($messages);

                $this->messageRepository->createHistoryMessageList(
                    $channel->getId(),
                    $historyMessageList->selectBackfillTargets(
                        $registeredAt,
                        $this->messageRepository->getTrackedDiscordMessageIdList(
                            $historyMessageList->getDiscordMessageIdList()
                        ),
                    ),
                );

                if (!$historyMessageList->needsOlderPage($registeredAt)) {
                    return null;
                }

                return $this->backfillHistoryPage(
                    $discord,
                    $channel,
                    $registeredAt,
                    $historyMessageList->getOldestDiscordMessageId(),
                );
            }
        );
    }

    private function toHistoryMessageList(ExCollectionInterface $messages): HistoryMessageList
    {
        $historyMessages = [];

        /** @var Message $message */
        foreach ($messages->values() as $message) {
            $historyMessages[] = new HistoryMessage(
                new DiscordMessageId($message->id),
                new CreatedAt($message->timestamp->getTimestamp()),
            );
        }

        return new HistoryMessageList($historyMessages);
    }
}
