<?php

declare(strict_types=1);

namespace Infra\Discord;

use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Http\Drivers\React;
use Discord\Http\Http;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Domain\DiscordBot\BotCommand;
use Domain\User\DefinitiveRegisterToken\DefinitiveRegisterToken;
use Domain\User\UserId;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use stdClass;

class DiscordBotClient
{
    public function __construct(
        private readonly DiscordBotCommandSystem $discordBotCommandSystem,
    ) {
    }

    /**
     * Discord Instants
     *
     * @var Discord
     */
    protected Discord $discord;
    protected Message $message;

    /**
     * This Command Prefix
     *
     * @var string
     */
    protected string $commandPrefix = '!';

    protected string $dmChannelId;

    /**
     * Starting run a discord bot
     *
     * @param string $botToken
     * @throws IntentException
     */
    public function run(): void
    {
        $this->discord = new Discord([
            'token' => env('DISCORD_BOT_TOKEN'),
            'loadAllMembers' => true,
            'intents' => Intents::getDefaultIntents() | Intents::GUILD_MEMBERS,
        ]);

        $this->discord->on('ready', function(Discord $discord) {
            $discord->on('message', function(Message $message) use ($discord) {
                $this->message = $message;
                $commandPrefix = substr($this->message->content, 0, 1);
                $removedCommandPrefix = str_replace($this->commandPrefix, '', $this->message->content);
                $commandContents = [];

                if (!$this->commandPrefixChecker($commandPrefix) || !$this->commandCheck($this->message)) {
                    return false;
                }

                $removedCommandPrefix = mb_convert_kana($removedCommandPrefix, 'rsa');
                $commandName = mb_strstr($removedCommandPrefix, ' ', true) ?: $removedCommandPrefix;
                $botCommandName = BotCommand::makeFromDisplayName($commandName)->getValue()->getRawValue();

                if (mb_strstr($removedCommandPrefix, ' ', true) || mb_strstr($removedCommandPrefix, '　', true)) {
                    $commandContents += $this->argSplitter($removedCommandPrefix);
                    unset($commandContents[0]);
                    $commandArgs = array_values($commandContents);
                    $commandArgs = BotCommand::makeFromDisplayName($commandName)->getCommandArgumentClass($commandArgs);
                    $this->discordBotCommandSystem->$botCommandName($commandArgs, $discord, $this->message);
                    return true;
                }

                $this->discordBotCommandSystem->$botCommandName($discord, $this->message);

                return true;
            });
        });
        $this->discord->run();
    }

    public function sendDefinitiveRegisterUrlByDm(UserId $userId, DefinitiveRegisterToken $token): void
    {
        $loop = \React\EventLoop\Loop::get();
        $logger = (new Logger('discord-direct-message'))->pushHandler(new StreamHandler('php://stdout'));
        $driver = new React($loop);
        $discordHttp = new Http('Bot '. env('DISCORD_BOT_TOKEN'), $loop, $logger, $driver);

        $dmChannelIdRequestPath = '/users/@me/channels';
        $content = ['recipient_id' => $userId->getRawValue()];
        $discordHttp->post($dmChannelIdRequestPath, $content)->then(function(stdClass $response) {
            $this->dmChannelId = $response->id;
        });
        $loop->run();

        $dmApiPath = "/channels/{$this->dmChannelId}/messages";
        $registerUrl = url("/definitive_register?token={$token->getToken()->getRawValue()}");
        $discordHttp->post($dmApiPath, ['content' => $registerUrl]);
        $loop->run();
        $loop->stop();
    }

    /**
     * Command prefix check
     *
     * @param string $commandPrefix
     * @return bool
     */
    private function commandPrefixChecker(string $commandPrefix): bool
    {
        return $this->commandPrefix === $commandPrefix;
    }


    /**
     * Split the message and get dhe arguments
     *
     * @param string $commandContents
     * @return array
     */
    private function argSplitter(string $commandContents): array
    {
        return explode(' ', $commandContents);
    }

    private function commandCheck(Message $message): bool
    {
        $bot = $this->discord->user;

        if (
            $message->author->id === $bot->id
            || $message->author->bot
        ) {
            return false;
        }

        return true;
    }
}
