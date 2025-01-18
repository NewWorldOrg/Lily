@extends('vendor.adminlte.page')
@section('title', 'HOME')

@section('content')
    <p>トップページ</p>
    @include('layouts.partials.flash_message')
    @include('layouts.partials.errors')
@endsection
