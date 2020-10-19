@once
    @section('before_styles')
        <link rel="stylesheet" type="text/css" href="/blockcrud/css/style.css">
    @endsection
@endonce

@php
	// if not otherwise specified, the hidden input should take up no space in the form
    $field['wrapper']['class'] = $field['wrapper']['class'] ?? $field['wrapperAttributes']['class'] ?? "blockcrud_hidden";
@endphp

@include('crud::fields.inc.wrapper_start')
    <div class="blockcrud_toggle_wrapper blockcrud-hidden">
        @if (isset($field['show_when']))
            @foreach ($field['show_when'] as $fi => $val)
                <input class="blockcrud_toggle_when" type="hidden" name="cond_{{ $fi }}" value="{{ $val }}">
            @endforeach
        @endif
        <label>{!! $field['label'] !!}</label>
        <div class="d-flex">
            @if (isset($field['value']) && is_array($field['value']))
                @foreach ($field['value'] as $name => $textarea)
                    <textarea 
                        name="{{ $field['name'] }}[{{ $name }}]"
                        @include('crud::fields.inc.attributes')

                        >{{ $textarea }}</textarea>

                @endforeach
            @endif
        </div>
    </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD EXTRA CSS  --}}
    {{-- push things in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- no styles -->
    @endpush

    {{-- FIELD EXTRA JS --}}
    {{-- push things in the after_scripts section --}}
    @push('crud_fields_scripts')

    @endpush
@endif

@once
    @section('after_scripts')
        <script src="/blockcrud/js/main.js"></script>   
    @endsection
@endonce
