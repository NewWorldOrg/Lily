<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as AppController;
use App\Http\Requests\Admin\AdminRequest;
use App\Services\MedicationHistoryService;
use Domain\Common\Paginator\Paginate;
use Illuminate\Http\RedirectResponse;
use Infra\EloquentModels\MedicationHistory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\Admin\MedicationHistories\UpdateMedicationHistoryRequest;

class MedicationHistoryController extends AppController
{
    public function __construct(private readonly MedicationHistoryService $medicationHistoryService)
    {
        parent::__construct();
    }

    /**
     * 服薬履歴一覧
     *
     * @param AdminRequest $request
     * @return View
     */
    public function index(AdminRequest $request): View
    {
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 20);

        $paginate = Paginate::make((int)$page, (int)$perPage);

        $medicationHistories = $this->medicationHistoryService->getMedicationHistoryPaginator($paginate);

        return view('medication_histories.index', compact('medicationHistories'));
    }

    /**
     * 服薬履歴編集
     *
     * @param MedicationHistory $medicationHistory
     * @return View
     */
    public function edit(MedicationHistory $medicationHistory): View
    {
        return view('medication_histories.edit', compact('medicationHistory'));
    }

    /**
     * 服薬履歴更新
     *
     * @param MedicationHistory $medicationHistory
     * @param UpdateMedicationHistoryRequest $request
     * @return RedirectResponse
     */
    public function update(MedicationHistory $medicationHistory, UpdateMedicationHistoryRequest $request): RedirectResponse
    {
        $amount = $request->getAmount();
        $medicationHistory->amount = $amount->getRawValue();
        if (!$this->medicationHistoryService->updateMedicationHistory(
            $medicationHistory->toDomain(),
        )) {
            return redirect(route('admin.medication_histories.index'))->with(['error' => '服薬履歴の更新に失敗しました']);
        }
        return redirect(route('admin.medication_histories.index'))->with(['success' => '服薬履歴の更新に成功しました']);
    }
}
