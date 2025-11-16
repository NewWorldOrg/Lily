<?php

declare(strict_types=1);

namespace Infra\Discord;

use Discord\Discord;
use Discord\Exceptions\IntentException;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Intents;
use Domain\DiscordBot\BotCommand;
use Domain\DiscordBot\SlashCommands\SlashCommand;

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
     * The command prefix.
     *
     * @var string
     */
    protected string $commandPrefix = '!';

    /**
     * Launch the bot
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
                    $cmd->isInitSlashCommands() => SlashCommand::toArray()->map(
                        fn (SlashCommand $slashCommand) => $this->initSlashCommands($discord, $slashCommand)
                    ),
                    default => $this->discordBotCommandSystem->commandNotFound($discord, $this->message),
                };

                return true;
            });
        });
        $this->discord->run();
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

    public function initSlashCommands(DIscord $discord, SlashCommand $slashCommand): void
    {
        $discord->application->commands->create(
            [
                'name' => $slashCommand->displayName()->getRawValue(),
                'description' => $slashCommand->getDescription()->getRawValue(),
            ],
        );
    }

    /**
     * Return true if this message was sent by the bot.
     *
     * @param Message $message
     * @return bool
     */
    private function botCheck(Message $message): bool
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
