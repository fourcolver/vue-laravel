@extends('mails.layout')

@section('title')
    {{ $details['title'] ?? "" }}
@endsection

@section('logo')
    {{ $logo ?? "" }}
@endsection

@section('body')
    {!! $body ?? "" !!}
@endsection
