<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\Common\CreatedAt;
use Domain\Drug\DrugId;
use Domain\MedicationHistory\MedicationHistory as MedicationHistoryDomain;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationHistoryId;
use Domain\MedicationHistory\UserId;
use Infra\EloquentModels\Model as AppModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationHistory extends AppModel
{
    protected $table = 'medication_histories';

    protected $guarded = [
        'id',
        'user_id',
        'drug_id',
    ];

    public $sortable = [
        'id',
        'name',
        'drug_name',
    ];

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }

    public function toDomain(): MedicationHistoryDomain
    {
        return new MedicationHistoryDomain(
            new MedicationHistoryId((int)$this->id),
            new UserId((int)$this->user_id),
            new DrugId((int)$this->drug_id),
            new Amount((float)$this->amount),
            CreatedAt::forStringTime((string)$this->created_at),
        );
    }
}
