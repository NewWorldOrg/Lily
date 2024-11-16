@extends('vendor.adminlte.page')

@section('content-header')
    <div>
        <h3>
            <span class="oi oi-eye"></span>
            服薬履歴編集
        </h3>
        <div class="text-right">
            <a href="{{ route('admin.medication_histories.index') }}" class="btn btn-round btn-info">
                <span class="oi oi-chevron-left"></span>
                服薬履歴一覧に戻る
            </a>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.medication_histories.update', $medicationHistory) }}" method="POST">
    <div class="form-group info">
        <label>服薬者</label>
        <input name="taker" value="{{ $medicationHistory->user->name }}" class="form-control" disabled />
    </div>
    <div class="form-group info">
        <label>薬物名</label>
        <input name="drug_name" value="{{ $medicationHistory->drug->drug_name }}" class="form-control" disabled />
    </div>
    <div class="form-group info">
        <label>服薬用</label>
        <input name="amount" value="{{ old('amount', $medicationHistory->amount) }}" class="form-control" placeholder="Enter amount" required/>
    </div>
    <button type="submit" class="btn btn-round btn-info">更新</button>
    </form>
@endsection
