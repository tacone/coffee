@extends('coffee::demo.layout.grid')

@section('content')
    <style>
        th.btn {
            border: 0 none;
            border-radius: 0;
            display: table-cell;
            font-weight: bold;
            /*width: 100%;*/
        }
    </style>

    {{ $grid }}

@stop
