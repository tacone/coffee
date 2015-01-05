@extends('coffee::demo.master')

@section('content')

    {{ $form->begin }}

    <!-- Look ma', customization! -->
    <p>
        <em>
            This is a simple form printed in a custom view.
            It should look similar to the simple demo.
        </em>
    </p>

    @foreach($form as $field)
        <div class="form-group {{ count($field->errors)? 'has-error':'' }}">
            {{ $field->label }}
            {{ $field->control() }}
            {{ $field->errors() }}
        </div>
    @endforeach

    {{ $form->end }}

@stop