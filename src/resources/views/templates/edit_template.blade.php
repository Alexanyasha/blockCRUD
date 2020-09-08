@section('before_styles')
    <link rel="stylesheet" type="text/css" href="/blockcrud/css/style.css">
@endsection

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    <div class="blockcrud_code_editor d-flex">
        <div class="blockcrud_code_source col-12 col-lg-6">
            <textarea
                type="text"
                name="{{ $field['name'] }}"
                @include('crud::fields.inc.attributes')
            >{{ old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) }}</textarea>
        </div>
        <div class="blockcrud_code_preview col-12 col-lg-6">
            
            <preview-code stylesheet="/css/style.css"></preview-code>

            <script>
                customElements.define('preview-code', class extends HTMLElement {
                    connectedCallback() {
                        const shadow = this.attachShadow({mode: 'open'});
                        shadow.innerHTML = `
                            <link rel="stylesheet" type="text/css" href="${this.getAttribute('stylesheet')}">
                            <div class="shadow_wrapper">
                                {!! old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : '' )) !!}
                            </div>
                        `;
                    }
                });
            </script>
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
