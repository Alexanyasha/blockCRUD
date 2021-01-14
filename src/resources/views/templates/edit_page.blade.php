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

        @php
            $content = (old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : 'no content' )));
            $block_items = \Backpack\BlockCRUD\app\Models\BlockItem::orderBy('name')->get();
        @endphp
        <label>{!! $field['label'] !!}</label>
        <div class="blockcrud_code_editor">
            <div class="blockcrud-js-add-block row mx-0">
                @if ($block_items->count() > 0)
                    <select class="form-control blockcrud-js-add-block-name col-12 col-lg-4 mr-lg-2">
                        @foreach ($block_items as $block_item)
                            <option value="{{ $block_item->slug }}">{{ $block_item->name }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-default blockcrud-js-add-block-btn col-12 col-lg-4 mt-3 mt-lg-0">Вставить на страницу</button>
                    <small class="col-12 col-lg-3 mt-3 mt-lg-0">Чтобы блок появился в области предпросмотра, сохраните страницу</small>
                @else
                    <div class="alert bg-warning">
                        Создайте хотя бы один блок, чтобы добавить его на страницу
                    </div>
                @endif
            </div>
            <div class="blockcrud_code_source">
                <div class="blockcrud-js-sortable-blocks">
                    @pageblocks_sortable($content)
                    <textarea
                        type="text"
                        name="{{ $field['name'] }}"
                        class="blockcrud-js-sortable-content blockcrud_hidden"
                        @include('crud::fields.inc.attributes')
                    >{{ $content }}</textarea>
                </div>
            </div>
            <label class="mt-4">{!! $field['label_preview'] ?? 'Предпросмотр' !!}</label>                
            <div class="blockcrud_code_preview blockcrud_refresh_page">
                <preview-code-{{ $field['name'] }} stylesheet="/css/style.css" stylesheet2="/blockcrud/css/editable.css" class="blockcrud_preview_area"></preview-code>

                <script>
                    customElements.define("preview-code-{{ $field['name'] }}", class extends HTMLElement {
                        connectedCallback() {
                            const shadow = this.attachShadow({mode: 'open'});
                            shadow.innerHTML = `
                                <link rel="stylesheet" type="text/css" href="${this.getAttribute('stylesheet')}">
                                <link rel="stylesheet" type="text/css" href="${this.getAttribute('stylesheet2')}">
                                <div class="shadow_wrapper">
                                    @pageblocks_edit($content)
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
        <script src="/blockcrud/js/sortable.js"></script>
    @endsection
@endonce
