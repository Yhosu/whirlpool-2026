@props([
    'type' => 'primary',
    'title' => null,
    'animation' => true,
    'duration' => 2000,
])

@php

    $icon = match($type) {
        'primary' => null,
        'second' => null,
        'success' => 'check',
        'error' => 'x-octagon',
        'warning' => 'alert-triangle',
        'info' => 'info',
        default => null,
    };

    $alertClass = match($type) {
        'primary' => 'alert-primary',
        'second' => 'alert-secondary',
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-primary',
    };
@endphp

<div x-data="{ shown: false, timeout: null }"
    x-init="() => { 
        if (@json($animation)) {
            clearTimeout(timeout); 
            shown = true; 
            timeout = setTimeout(() => { 
                shown = false; 
                setTimeout(() => { $el.remove() }, {{ $duration }}); // Remueve el elemento después de la duración de la transición
            }, 2000); 
        } else {
            shown = true;
        }
    }"
    x-show.transition.out.opacity.duration.{{ $duration }}ms="shown"
    x-transition:leave.opacity.duration.{{ $duration }}ms
    style="display: none;"
    {{ $attributes->merge(['class' => 'd-block']) }}>
    <div class="alert {{ $alertClass }}" role="alert">
        @if($title != null)
            <h4 class="alert-heading">{{ $title }}</h4>
        @endif
        <div class="alert-body">
            @if ($icon != null)
                <x-cy-icon-feather :icon="$icon" class="me-50" />
            @endif
            <span class="align-middle">{{ $slot }}</span>
        </div>
    </div>
</div>