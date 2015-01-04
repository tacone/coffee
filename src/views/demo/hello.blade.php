@extends('coffee::demo.master')


@section('content')

<div class="row">
    <div class="col-xs-7">


        {{ $form->output() }}

        {{--@foreach ($form as $field) {--}}{--<div class="form-group">--}}
            {{--{{ $field->label }}--}}
            {{--{{ $field->control() }}--}}
{{--            {{ $field }}--}}
        {{--</div>--}}
        {{--@endforeach--}}

    </div>
    <div class="col-xs-5">
        <code>$form->toArray()</code>
        <pre>{{ json_encode($form->toArray(), JSON_PRETTY_PRINT ) }}</pre>
        <code>$model->toArray()</code>
        <pre>{{ json_encode($model->toArray(), JSON_PRETTY_PRINT ) }}</pre>
    </div>
</div>

@stop
