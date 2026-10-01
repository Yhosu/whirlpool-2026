@props([
    'icon' => null,
    'class' => '',
])

@php
    $iconFile = public_path("assets/img/feather-icons/{$icon}.svg");
@endphp

@if (file_exists($iconFile))
    @php
        $svgContent = file_get_contents($iconFile);
        $svgContent = str_replace(
            '<svg',
            '<svg class="'.$class.' feather feather-'.$icon.' ficon align-middle"',
            $svgContent
        );
    @endphp
    {!! $svgContent !!}
@else
    <p>Icon not found: {{ $icon }}</p>
@endif