@extends('coffee::demo.master')

@section('content')

    {{ $form->start }}

    <!-- Look ma', customization! -->
    <p><em>
            This is a simple form printed in a custom view.
            It should look similar to the simple demo.
        </em></p>
    <p><em>
            This form uses </em>
        <code>method="{{ $form->attr('method') }}"</code>
    </p>

    @foreach($form as $field)
        @if ($field->name == 'author.firstname')
            {{-- Special case for the first name field --}}
            {{ $field->start }}
            {{ $field->label }}
            <p><em>If the author has a second name and/or a third, please write
                    them all here.
                </em></p>
            {{ $field->content() }}
            {{ $field->errors() }}
            {{ $field->end }}
        @else
            {{-- Just print the vanilla field here --}}
            {{ $field }}
        @endif
    @endforeach

    {{ $form->end }}

@stop
