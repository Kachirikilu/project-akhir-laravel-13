@switch($sortir ?? $xValue)
    @case(1)
        <flux:badge icon="{{ ($noIcon ?? false) ? '' : 'academic-cap' }}" color="emerald" size="{{ $size ?? 'sm' }}">{{ $xValue ?? '---' }}
        </flux:badge>
    @break

    @case(2)
        <flux:badge icon="{{ ($noIcon ?? false) ? '' : 'book-open' }}" color="amber" size="{{ $size ?? 'sm' }}">{{ $xValue ?? '---' }}
        </flux:badge>
    @break

    @case(3)
        <flux:badge icon="{{ ($noIcon ?? false) ? '' : 'building-library' }}" color="indigo" size="{{ $size ?? 'sm' }}">
            {{ $xValue ?? '---' }}
        </flux:badge>
    @break

    @default
        <flux:badge icon="{{ ($noIcon ?? false) ? '' : 'globe-alt' }}" color="red" size="{{ $size ?? 'sm' }}">{{ $xValue ?? '---' }}
        </flux:badge>
@endswitch
