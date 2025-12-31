@extends('vendor.adminlte.page')

@section('content-header')
<div>
    <h3>
        <span class="oi oi-eye"></span>
        服薬履歴（全{{ $medicationHistories->count() }}件）
    </h3>
</div>
@endsection

@section('content')
<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>服薬者</th>
        <th>薬物名</th>
        <th>量(mg)</th>
        <th>服薬日時</th>
        <th class="text-right">Action</th>
    </tr>
    </thead>
    @foreach($medicationHistories as $item)
        <?php /** @var App\DataTransfer\MedicationHistory\MedicationHistoryDetail $item */ ?>
    <tr>
        <td>{{ $item->id->getRawValue() }}</td>
        <td>{{ $item->userId->getRawValue() }}</td>
        <td>{{ $item->drugName->getRawValue()}}</td>
        <td>{{ $item->amount->getRawValue() }}</td>
        <td>{{ $item->createdAt->getDetail() }}</td>
        <td class="td-actions text-right">
            <a href="{{ route('admin.medication_histories.edit', $item->id->getRawValue()) }}" class="btn btn-success btn-round" rel="tooltip" data-placement="bottom" title="Edit">
                <span class="oi oi-pencil"></span>
            </a>
        </td>
    </tr>
    @endforeach
</table>
<div class="box-footer clearfix">
    {{ $medicationHistories->withPath('/admin/medication_histories')->links('pagination::bootstrap-4') }}
</div>
@endsection
