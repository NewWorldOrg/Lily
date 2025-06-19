@extends('vendor.adminlte.page')

@section('content-header')
<div>
    <h3>
        <span class="oi oi-person"></span>
        管理ユーザー編集
    </h3>
    <div class="text-right">
        <a href="{{ route('admin.admin_users.index') }}" class="btn btn-round btn-info">
            <span class="oi oi-chevron-left"></span>
            管理ユーザー一覧に戻る
        </a>
    </div>
</div>
@endsection

@section('content')
    <form action="{{ route('admin.admin_users.update', $adminUser) }}" method="POST">
        @csrf
        <div class="form-group info">
            <label for="InputUserId">User ID</label>
            <input name="user_id" value="{{ old('user_id') }}" class="form-control " placeholder="Enter user id" required/>
        </div>
        <div class="form-group">
            <label for="InputPassword">Password</label>
            <input type="password" name="password" value="{{ old('password') }}" class="form-control input-sm" placeholder="8 characters or more" required/>
        </div>
        <div class="form-group">
            <label for="InputPasswordConfirm">Password Confirm</label>
            <input type="password" name="password_confirm" class="form-control input-sm" placeholder="" required/>
        </div>
        <div class="form-group">
            <label for="TextareaUserName">User Name</label>
            <input name="name" value="{{ old('name') }}" class="form-control input-sm" placeholder="" required/>
        </div>
        <div class="form-group">
            <label for="InputRole">Role</label>
            <select name="role" class="form-control selectpicker" required>
                @foreach(\Domain\AdminUser\AdminUserRole::displayNameList() as $key => $val)
                    <option class="btn btn-link" value="{{ $key }}" @if(old('role') === $key) selected @endif>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="InputState">State</label>
            <select name="status" class="form-control selectpicker" required>
                @foreach(\Domain\AdminUser\AdminUserStatus::displayNameList() as $key => $val)
                    <option class="btn btn-link" value="{{ $key }}" @if(old('status') === $key) selected @endif>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-round btn-info">Submit</button>
    </form>
@endsection
