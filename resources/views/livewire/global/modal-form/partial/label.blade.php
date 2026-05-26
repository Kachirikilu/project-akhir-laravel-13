@if (isset($noLabel) == 0)
    <label for="{{ $modelString }}" class="block text-sm font-medium mb-2 text-[var(--contrast-main-text)]">
        @if ($nameAlpine ?? null)
            <span x-text="$store.{{ $alpine ?? 'config' }}.{{ $nameAlpine ?? null }}"></span>
        @else
            {{ $nameX2String ?? $nameXString ?? ucfirst($modelString) }}
        @endif
        @if ($isRequired ?? true)
            <span class="text-red-500">*</span>
        @endif
    </label>
@endif
