<div x-data="{ itemsAll: @entangle($kodeString ?? null).live }">
    <label class="block text-sm font-medium">{{ $nameXString ?? null }}</label>
    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString ?? null }}" variant="mini" x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input type="text" readonly
            @if($kodeString ?? null)
                x-bind:value="itemsAll?.{{ $itemString ?? 'kode' }} || '{{ $placeholder ?? null }}'"
            @elseif($kode2String ?? null)
                x-bind:value="{{ $kode2String }}?.{{ $itemString ?? 'kode' }} || '{{ $placeholder ?? null }}'"
            @else
                @if ($valueString ?? null)
                    value="{{ $valueString }}"
                @else
                    value="{{ $secondValue ?? null }}"
                @endif
            @endif
            placeholder="{{ $placeholder ?? null }}"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 px-3 py-2 text-center font-bold">
    </div>
</div>
