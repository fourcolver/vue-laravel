@extends('mails.layout')

@section('title')
    {{ $details['title'] ?? "" }}
@endsection

@section('body')
    {!! $details['body'] ?? "" !!}
    @if ($user)
        <a class="button button-primary"
           href="{{ $user->autologinUrl }}">
        {{ __("See service request") }}
        </a>
    @endif
@endsection
