@extends('coffee::demo.layout.master')

@section('variations')
    <ul class="nav nav-pills">

        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\DemoController@anySimple', 'Simple') }}
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\DemoController@anyIndex', 'Complex') }}
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\DemoController@anyIndex', 'Custom view',['view'=>'custom']) }}
    </ul>
@stop
