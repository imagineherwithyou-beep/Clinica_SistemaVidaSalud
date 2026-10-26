@props(['name', 'label', 'options' => [], 'value' => null, 'hint' => null, 'placeholder' => 'Seleccione una opción', 'wide' => false])

{{-- Lista desplegable. $options es un arreglo o colección [valor => texto]. --}}
@php($id = $attributes->get('id', $name))

<div @class(['field', 'field-full' => $wide])>
    <label for="{{ $id }}">{{ $label }}</label>
    <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except('id') }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $clave => $texto)
            <option value="{{ $clave }}" @selected((string) old($name, $value) === (string) $clave)>{{ $texto }}</option>
        @endforeach
    </select>

    @if ($hint)
        <span class="field-hint">{{ $hint }}</span>
    @endif
    @error($name)
        <span class="field-error">{{ $message }}</span>
    @enderror
</div>
