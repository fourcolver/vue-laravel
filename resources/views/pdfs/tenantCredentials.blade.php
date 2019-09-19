@extends('pdfs.layout')

@section('title')
    {{ __("tenant.tenant_credentials") }}
@endsection

@section('body')
    <p>{{ __("tenant.url") }}: <a href="{{ url('/') }}">{{ url('/') }}</a></p>
    <p>{{ __("tenant.username") }}: {{ $tenant->user->email }}</p>
    <p>{{ __("tenant.url") }}: {{ $url }}</p>
@endsection
