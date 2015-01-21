@extends('coffee::demo.layout.master')

@section('variations')
    <ul class="nav nav-pills">
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\GridController@anyIndex', 'Simple') }}
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\GridController@anyCallback', 'Callbacks') }}
    </ul>
@stop
