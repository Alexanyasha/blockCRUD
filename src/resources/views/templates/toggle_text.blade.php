<!-- text input -->
@include('crud::fields.inc.wrapper_start')
    <div class="blockcrud_toggle_wrapper">
        @forelse ($field['show_when'] as $fi => $val)
            <input class="blockcrud_toggle_when" type="hidden" name="cond_{{ $fi }}" value="{{ $val }}">
        @empty

        @endforelse
        <label>{!! $field['label'] !!}</label>
        @include('crud::fields.inc.translatable_icon')

        @if (isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
            @if (isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
            <input
                type="text"
                name="{{ $field['name'] }}"
                value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
                @include('crud::fields.inc.attributes')
            >
            @if (isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}</span></div> @endif
        @if (isset($field['prefix']) || isset($field['suffix'])) </div> @endif

        {{-- HINT --}}
        @if (isset($field['hint']))
            <p class="help-block">{!! $field['hint'] !!}</p>
        @endif
    </div>
</div>
