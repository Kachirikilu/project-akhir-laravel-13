@if ($sortir ?? $xValue)
    <flux:badge icon="check" color="green" size="sm" inset="top bottom">
        {{ $xValue }}
    </flux:badge>
@else
    <flux:badge icon="x-mark" color="zinc" size="sm" inset="top bottom">
        {{ $xValue }}
    </flux:badge>
@endif
