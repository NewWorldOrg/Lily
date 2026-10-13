<?php

declare(strict_types=1);

namespace Domain\Message;

use Domain\Channel\ChannelId;
use Domain\Common\CommonTime;

class MessageSweepSchedule
{
    public const int COOLDOWN_SECONDS = 300;

    /**
     * @var array<array-key, true>
     */
    private array $pendingDiscordMessageIds = [];

    /**
     * @var array<array-key, CommonTime>
     */
    private array $messageRetryTimes = [];

    /**
     * @var array<array-key, CommonTime>
     */
    private array $channelRetryTimes = [];

    public function selectRequestable(MessageList $messageList, CommonTime $now): MessageList
    {
        $requestable = [];

        /** @var Message $message */
        foreach ($messageList as $message) {
            if ($this->isRequestable($message, $now)) {
                $requestable[] = $message;
            }
        }

        return new MessageList($requestable);
    }

    public function markPending(Message $message): void
    {
        $this->pendingDiscordMessageIds[$message->getDiscordMessageId()->getRawValue()] = true;
    }

    public function release(Message $message): void
    {
        unset($this->pendingDiscordMessageIds[$message->getDiscordMessageId()->getRawValue()]);
    }

    public function postpone(Message $message, CommonTime $now): void
    {
        $this->messageRetryTimes = $this->withoutExpired($this->messageRetryTimes, $now);
        $this->messageRetryTimes[$message->getDiscordMessageId()->getRawValue()] = $this->retryTime($now);
    }

    public function postponeChannel(ChannelId $channelId, CommonTime $now): void
    {
        $this->channelRetryTimes = $this->withoutExpired($this->channelRetryTimes, $now);
        $this->channelRetryTimes[$channelId->getRawValue()] = $this->retryTime($now);
    }

    private function isRequestable(Message $message, CommonTime $now): bool
    {
        $discordMessageId = $message->getDiscordMessageId()->getRawValue();

        return !isset($this->pendingDiscordMessageIds[$discordMessageId])
            && !$this->isWaiting($this->messageRetryTimes[$discordMessageId] ?? null, $now)
            && !$this->isWaiting($this->channelRetryTimes[$message->getChannelId()->getRawValue()] ?? null, $now);
    }

    private function isWaiting(?CommonTime $retryTime, CommonTime $now): bool
    {
        return !is_null($retryTime) && $now->isLessThan($retryTime);
    }

    private function retryTime(CommonTime $now): CommonTime
    {
        return new CommonTime($now->timeStamp() + self::COOLDOWN_SECONDS);
    }

    /**
     * @param array<array-key, CommonTime> $retryTimes
     * @return array<array-key, CommonTime>
     */
    private function withoutExpired(array $retryTimes, CommonTime $now): array
    {
        return array_filter($retryTimes, fn (CommonTime $retryTime) => $this->isWaiting($retryTime, $now));
    }
}
