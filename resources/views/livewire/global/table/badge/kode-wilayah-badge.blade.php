@switch($sortir ?? $xValue)
    @case('IDL')
        <flux:badge icon="academic-cap" color="emerald" size="sm">{{ $xValue ?? '-' }}
        </flux:badge>
    @break
    @case('PLG')
        <flux:badge icon="academic-cap" color="amber" size="sm">{{ $xValue ?? '-' }}</flux:badge>
    @break
    @default
        <flux:badge icon="academic-cap" color="red" size="sm">{{ $xValue ?? '-' }}</flux:badge>
@endswitch
