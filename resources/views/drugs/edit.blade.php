@extends('vendor.adminlte.page')

@section('content-header')
    <div>
        <h3>
            <span class="oi oi-eye"></span>
            薬物編集
        </h3>
        <div class="text-right">
            <a href="{{ route('admin.drugs.index') }}" class="btn btn-round btn-info">
                <span class="oi oi-chevron-left"></span>
                薬物一覧に戻る
            </a>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.drugs.update', $drug) }}" method="POST">
        <div class="form-group info">
            <label>薬物名</label>
            <input name="drug_name" value="{{ old('drug_name', $drug->drug_name) }}" class="form-control " placeholder="Enter drug name" required/>
        </div>
        <div class="form-group info">
            <label>URL (wiki)</label>
            <input name="url" value="{{ old('url', $drug->url) }}" class="form-control " placeholder="Enter URL" required/>
        </div>
        <button type="submit" class="btn btn-round btn-info">追加</button>
    </form>
@endsection
