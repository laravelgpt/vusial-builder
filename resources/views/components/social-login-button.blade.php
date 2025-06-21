@props([
    'provider',
    'name',
    'icon' => null,
    'color' => 'primary',
    'size' => 'md'
])

@php
$sizes = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg'
];

$colors = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'success' => 'btn-success',
    'danger' => 'btn-danger',
    'warning' => 'btn-warning',
    'info' => 'btn-info',
    'light' => 'btn-light',
    'dark' => 'btn-dark'
];

$buttonClass = 'btn ' . ($colors[$color] ?? $colors['primary']) . ' ' . ($sizes[$size] ?? '');
@endphp

<a href="{{ route('social.login', $provider) }}" class="{{ $buttonClass }}">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $name }}
</a> 