@section('before_styles')
    <link rel="stylesheet" type="text/css" href="/blockcrud/css/style.css">
@endsection

@include('crud::fields.inc.wrapper_start')
    <div class="blockcrud_toggle_wrapper">
        @forelse ($field['show_when'] as $fi => $val)
            <input class="blockcrud_toggle_when" type="hidden" name="cond_{{ $fi }}" value="{{ $val }}">
        @empty

        @endforelse
        
        <label>{!! $field['label'] !!}</label>
        <div class="blockcrud_toggle_select">
            <select
                name="{{ $field['name'] }}@if (isset($field['allows_multiple']) && $field['allows_multiple']==true)[]@endif"
                style="width: 100%"
                data-init-function="bpFieldInitSelect2FromArrayElement"
                @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_from_array'])
                @if (isset($field['allows_multiple']) && $field['allows_multiple']==true)multiple @endif
                >

                @if (isset($field['allows_null']) && $field['allows_null']==true)
                    <option value="">-</option>
                @endif

                @if (count($field['options']))
                    @foreach ($field['options'] as $key => $value)
                        @if((old(square_brackets_to_dots($field['name'])) && (
                                $key == old(square_brackets_to_dots($field['name'])) ||
                                (is_array(old(square_brackets_to_dots($field['name']))) &&
                                in_array($key, old(square_brackets_to_dots($field['name'])))))) ||
                                (null === old(square_brackets_to_dots($field['name'])) &&
                                    ((isset($field['value']) && (
                                                $key == $field['value'] || (
                                                        is_array($field['value']) &&
                                                        in_array($key, $field['value'])
                                                        )
                                                )) ||
                                        (!isset($field['value']) && isset($field['default']) &&
                                        ($key == $field['default'] || (
                                                        is_array($field['default']) &&
                                                        in_array($key, $field['default'])
                                                    )
                                                )
                                        ))
                                ))
                            <option value="{{ $key }}" selected>{{ $value }}</option>
                        @else
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
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
        <!-- no scripts -->
    @endpush
@endif

@section('after_scripts')
    <script src="/blockcrud/js/main.js"></script>
@endsection
