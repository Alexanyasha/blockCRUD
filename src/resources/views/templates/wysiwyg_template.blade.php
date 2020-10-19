@once
    @section('before_styles')
        <link rel="stylesheet" type="text/css" href="/blockcrud/css/style.css">
    @endsection
@endonce

@include('crud::fields.inc.wrapper_start')
    <div class="blockcrud_toggle_wrapper">
        @if (isset($field['show_when']))
            @foreach ($field['show_when'] as $fi => $val)
                <input class="blockcrud_toggle_when" type="hidden" name="cond_{{ $fi }}" value="{{ $val }}">
            @endforeach
        @endif
        <label>{!! $field['label'] !!}</label>
        <div class="d-flex">
            <div class="col-12 blockcrud-code-preview" name="{{ $field['preview_for'] ?? $field['name'] }}">
                <preview-code-{{ $field['name'] }} stylesheet="/css/style.css" stylesheet2="/blockcrud/css/editable.css" class="blockcrud_preview_area"></preview-code>

                @php
                $template_render = str_replace(
                    ['<script', '</script>'], 
                    ['<replaced-script', '</replaced-script>'], 
                    (old($field['name']) ? old($field['name']) : 
                        (isset($field['value']) ? $field['value'] : 
                            (isset($field['default']) ? $field['default'] : '' )
                        )
                    )
                );
                @endphp
                <script>
                    customElements.define("preview-code-{{ $field['name'] }}", class extends HTMLElement {
                        connectedCallback() {
                            const shadow = this.attachShadow({mode: 'open'});
                            shadow.innerHTML = `
                                <link rel="stylesheet" type="text/css" href="${this.getAttribute('stylesheet')}">
                                <link rel="stylesheet" type="text/css" href="${this.getAttribute('stylesheet2')}">
                                <div class="shadow_wrapper" name="{{ $field['preview_for'] ?? $field['name'] }}" @include('crud::fields.inc.attributes')>
                                    {!! $template_render !!}
                                </div>
                            `;

                            // var scripts = shadow.querySelectorAll('replaced-script');

                            // for(let s = 0; s < scripts.length; s++) {
                            //     nodeScriptReplace(scripts[s]);
                            // }

                            function nodeScriptReplace(node) {
                                if ( nodeScriptIs(node) === true ) {
                                    try {
                                        node.parentNode.replaceChild( nodeScriptClone(node) , node );
                                    } catch (err) {
                                        
                                    }
                                }
                                else {
                                    var i        = 0;
                                    var children = node.childNodes;
                                    while ( i < children.length ) {
                                            nodeScriptReplace( children[i++] );
                                    }
                                }

                                return node;
                            }

                            function nodeScriptIs(node) {
                                return node.tagName === 'REPLACED-SCRIPT';
                            }

                            function nodeScriptClone(node){
                                var script  = document.createElement("script");
                                script.text = node.innerHTML;
                                for( var i = node.attributes.length-1; i >= 0; i-- ) {
                                    script.setAttribute( node.attributes[i].name, node.attributes[i].value );
                                }
                                return script;
                            }
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

    @endpush
@endif

@once
    @section('after_scripts')
        <script src="/blockcrud/js/main.js"></script>   
    @endsection
@endonce
