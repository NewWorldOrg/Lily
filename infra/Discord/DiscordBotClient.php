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
use Domain\MedicationHistory\UserId;
use Domain\User\DefinitiveRegisterToken\DefinitiveRegisterToken;
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

                if (!$this->commandPrefixChecker($commandPrefix) || $this->botCheck($this->message)) {
                    return false;
                }

                $input = str_replace($this->commandPrefix, '', $this->message->content);
                $input = mb_convert_kana($input, 'rsa');
                $commandName = mb_strstr($input, ' ', true) ?: '';
                $cmd = BotCommand::makeFromDisplayName($commandName);
                $args = $cmd->getCommandArgumentClass($this->argSplitter($input));

                match (true) {
                    $cmd->isHello() => $this->discordBotCommandSystem->hello($discord, $this->message),
                    $cmd->isRegisterDrug() => $this->discordBotCommandSystem->registerDrug($args, $discord, $this->message),
                    $cmd->isMedication() => $this->discordBotCommandSystem->medication($args, $discord, $this->message),
                    default => $this->discordBotCommandSystem->commandNotFound($discord, $this->message),
                };

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

    private function argSplitter(string $commandContents): array
    {
        $pattern = '/
            "([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"
            | \'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'
            | ([^\\s]+)
            /xu';

        preg_match_all($pattern, $commandContents, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

        $tokens = [];

        foreach ($matches as $m) {
            $tokens[] = match (true) {
                !is_null($m[1]) => stripcslashes($m[1]),
                !is_null($m[2]) => stripcslashes($m[2]),
                default => $m[3],
            };
        }

        unset($tokens[0]);

        return array_values($tokens);
    }

    private function botCheck(Message $message): bool
    {
        $bot = $this->discord->user;

        if (
            $message->author->id === $bot->id
            || $message->author->bot
        ) {
            return true;
        }

        return false;
    }
}
