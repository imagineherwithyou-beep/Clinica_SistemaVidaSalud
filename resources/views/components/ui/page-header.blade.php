@props(['title', 'subtitle' => null])

{{-- Cabecera de cualquier página de módulo. Los botones van en el slot "actions". --}}
<div class="page-header">
    <div>
        <h1>{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-header-sub">{{ $subtitle }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="page-header-actions">{{ $actions }}</div>
    @endisset
</div>
