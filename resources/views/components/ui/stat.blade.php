@props(['icon', 'label', 'value', 'tone' => 'blue'])

<div class="stat">
    <span class="stat-icon stat-icon-{{ $tone }}"><x-ui.icon :name="$icon" :size="24" /></span>
    <div>
        <p class="stat-value">{{ $value }}</p>
        <p class="stat-label">{{ $label }}</p>
    </div>
</div>
