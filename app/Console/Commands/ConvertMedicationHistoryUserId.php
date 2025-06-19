<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Infra\EloquentModels\MedicationHistory;
use Infra\EloquentModels\User;

class ConvertMedicationHistoryUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-medication-history-user-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $collection = User::where(['status' => 1])->get();
        $ul = $collection->map( fn (User $model) => $model->toDomain())->toArray();

        foreach ($ul as $user) {
            /** @var \Domain\User\User $user */
            echo "Update {$user->getId()} to {$user->getUserId()}. \n";
            $result = MedicationHistory::where(['user_id' => $user->getId()->getRawValue()])
                ->update(['user_id' => $user->getUserId()->getRawValue()]);
            echo "Done! {$result} \n";
        }
    }
}
