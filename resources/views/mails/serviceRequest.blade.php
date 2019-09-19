@extends('mails.layout')

@section('title')
    {{__("New service request from tenant")}}
@endsection

@section('body')
    <p>{{$tenant->first_name}} {{$tenant->last_name}} made a new service request.</p>
@endsection
