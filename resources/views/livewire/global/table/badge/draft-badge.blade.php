@if ($sortir ?? $xValue)
    <flux:badge color="red" size="{{ $size ?? 'sm' }}" icon="document-text">
        Draf
    </flux:badge>
@else
    <flux:badge color="green" size="{{ $size ?? 'sm' }}" icon="check-circle">
        Aktif
    </flux:badge>
@endif
