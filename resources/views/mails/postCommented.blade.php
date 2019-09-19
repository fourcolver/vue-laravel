@extends('mails.layout')

@section('title')
    {{ $subject ?? "" }}
@endsection

@section('body')
    {!! $body ?? "" !!}
@endsection
