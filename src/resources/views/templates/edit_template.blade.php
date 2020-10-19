@once
    @section('before_styles')
        <link rel="stylesheet" type="text/css" href="/blockcrud/css/style.css">
    @endsection
@endonce

@include('crud::fields.inc.wrapper_start')
    <div class="blockcrud_toggle_wrapper">
    @if (isset($field['show_when']))
        @forelse ($field['show_when'] as $fi => $val)
            <input class="blockcrud_toggle_when" type="hidden" name="cond_{{ $fi }}" value="{{ $val }}">
        @empty

        @endforelse
    @endif
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
                
                <preview-code-{{ $field['name'] }} stylesheet="/css/style.css" class="blockcrud_preview_area"></preview-code>

                <script>
                    customElements.define("preview-code-{{ $field['name'] }}", class extends HTMLElement {
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

@once
    @section('after_scripts')
        <script src="/blockcrud/js/main.js"></script>
    @endsection
@endonce
