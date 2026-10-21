<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Infra\Discord\DiscordBotClient;

class RunDiscordBot extends Command
{
    protected DiscordBotClient $botClient;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord-bot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starting run a discord bot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DiscordBotClient $discordBotService)
    {
        $this->botClient = $discordBotService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->botClient->run();
    }
}
