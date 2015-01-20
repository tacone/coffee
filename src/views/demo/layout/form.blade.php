@extends('coffee::demo.layout.master')

@section('variations')
    <ul class="nav nav-pills">
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\FormController@anySimple', 'Simple') }}
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\FormController@anyIndex', 'Complex') }}
        {{ $demo->activeLink('Tacone\Coffee\Demo\Controllers\FormController@anyIndex', 'Custom view',['view'=>'custom']) }}
    </ul>
@stop
