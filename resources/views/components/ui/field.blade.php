@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'wide' => false])

{{-- Campo de formulario con etiqueta, ayuda y error. Pasa a <input> cualquier atributo extra (required, maxlength...). --}}
@php($id = $attributes->get('id', $name))

<div @class(['field', 'field-full' => $wide])>
    <label for="{{ $id }}">{{ $label }}</label>
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
           @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
           {{ $attributes->except('id') }}>

    @if ($hint)
        <span class="field-hint">{{ $hint }}</span>
    @endif
    @error($name)
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
