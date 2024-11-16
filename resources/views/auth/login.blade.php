@extends('vendor.adminlte.page')
@section('content')
<div class="login-box-body">
    <h4 class="login-box-msg"><strong>Please Login</strong></h4>
    <form class="form" action="{{ route('admin.auth.login.post') }}" method="POST">
        <div class="form-group{{ $errors->has('user_id') ? ' has-danger' : '' }} has-feedback">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="oi oi-person input-group-text form-control-feedback"></span>
                </div>
                <input name="user_id" value="{{ old('user_id') }}" class="form-control " placeholder="Enter user id" required/>
                @if ($errors->has('user_id'))
                    <div id="user_id-error" class="error text-danger pl-3" for="user_id" style="display: block;">
                        <strong>{{ $errors->first('user_id') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }} has-feedback">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="oi oi-lock-locked input-group-text form-control-feedback"></span>
                </div>
                <input type="password" name="password" value="{{ old('password') }}" class="form-control input-sm" placeholder="8 characters or more" required/>
                @if ($errors->has('password'))
                    <div id="password-error" class="error text-danger pl-3" for="password" style="display: block;">
                        <strong>{{ $errors->first('password') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-round">LOGIN</button>
        </div>
    </form>
</div>
@endsection
