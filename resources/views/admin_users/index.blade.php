@extends('vendor.adminlte.page')

@section('content-header')
<div>
    <h3>
        <span class="oi oi-person"></span>
        管理ユーザー覧 ( 全{{ $adminUsers->count() }}件 )
    </h3>
    <div class="text-right">
        <a href="{{ route('admin.admin_users.create') }}" class="btn btn-round btn-info" rel="tooltip">
            <span class="oi oi-plus"></span> 新規作成
        </a>
    </div>
</div>
@endsection

@section('content')
    <table class="table">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>user id</th>
                <th>name</th>
                <th>role</th>
                <th>status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        @foreach($adminUsers as $item)
            <?php /** @var Domain\AdminUser\AdminUser $item */ ?>
            <tr>
                <td>{{ $item->getId() }}</td>
                <td>{{ $item->getUserId() }}</td>
                <td>{{ $item->getName() }}</td>
                <td>{{ $item->getRole()->displayName() }}</td>
                <td>{{ $item->getStatus()->displayName() }}</td>
                <td class="td-actions text-right">
                    <a href="{{ route('admin.admin_users.edit', $item->getId()->getRawValue()) }}" class="btn btn-success btn-round" rel="tooltip" data-placement="bottom" title="Edit">
                        <span class="oi oi-pencil"></span>
                    </a>
                    <a href="javascript:void(0)" data-url="{{ route('admin.admin_users.destroy', $item->getId()->getRawValue()) }}"
                       class="btn btn-danger btn-round delete-form-btn" rel="tooltip"
                       data-label="{{ $item->getName()->getRawValue() }}" title="Delete">
                        <span class="oi oi-x"></span>
                    </a>
                </td>
            </tr>
        @endforeach
    </table>
    <form action="#" id="form-delete" method="POST">
        @csrf
        <input type="hidden" name="_method" value="delete" />
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    </form>
@endsection

@push('js')
    <script>

        $(function(){
            $('.delete-form-btn').on('click', function() {
                let btn = $(this);
                if (confirm('「' + btn.data('label') + '」' + 'を削除してよろしいでしょうか？')) {
                    $('#form-delete').attr('action', btn.data('url'));
                    $('#form-delete').submit();
                }
            })
        });

    </script>
@endpush
