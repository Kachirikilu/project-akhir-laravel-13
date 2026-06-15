<div class="relative" wire:key="select-form-{{ $modelString }}" x-data="{
    open: false,
    {{-- Ambil opsi dan value dari Blade ke JS --}}
        options: [],
        values: [],
    
    getLabel(val) {
        if (val === '' || val === null || val === undefined) return '';
        const index = this.values.indexOf(Number(val));
        return index !== -1 ? this.options[index] : val;
    },
    
    value: ''
}"
x-init="value = getLabel($store.{{ $alpine ?? 'config' }}?.{{ $modelString }})"
x-effect="

    options = @js($xOptions);
        values = @js($xValues ?? $xOptions);
        
        {{-- Re-sync label jika nilai di store berubah --}}
        const rawVal = $store.{{ $alpine ?? 'config' }}?.{{ $modelString }};
        if ((rawVal === null || rawVal === undefined || rawVal === '') && $store.{{ $alpine ?? 'config' }}?.isEdit === 0) {
            value = '';
        } else {
            value = getLabel(rawVal);
        }
"
wire:key="select-form-{{ $modelString }}">

    <label for="{{ $modelString }}" class="block text-sm font-medium" :class="isDisabled ? 'opacity-50' : ''">
        {{ $nameXString ?? ucfirst($modelString) }}
        @if ($isRequired ?? true)
            <span class="text-red-500" x-show="!isDisabled">*</span>
        @endif
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input autocomplete="off" x-model="value" {{-- Gunakan x-model agar Alpine tahu isinya --}} type="text" readonly @click="open = true"
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
            placeholder="{{ $placeholder ?? 'Pilih Opsi' }}"
            class="bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
                placeholder-[var(--contrast-third-text)]
            w-full border rounded-lg pl-10 px-3 py-2 pr-10 cursor-pointer">

        {{-- 2. Tombol Reset --}}
        @include('livewire.global.search-and-filters.partial.reset-button', [
            'xShow' => 'value',
            'xClick' => "value = ''",
            'xAlpine' => $modelString,
            // 'xColor' => $colorIcon
        ])
    </div>

    {{-- Dropdown Result --}}
    <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" 
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">

        @foreach ($xOptions as $i => $option)
            @php
                $currentVal = isset($xValues[$i]) ? $xValues[$i] : $option;
            @endphp

            <div wire:key="option-{{ $option }}"
                @click="
                    value = '{{ $option }}'; 
                    {{-- $store.{{ $alpine ?? 'config' }}['{{ $modelString }}'] = value; --}}
                    $store.{{ $alpine ?? 'config' }}['{{ $modelString }}'] = {{ is_numeric($currentVal) ? $currentVal : "'$currentVal'" }};
                    open = false
                "
                class="px-4 py-2 cursor-pointer transition-colors duration-200
                bg-[var(--main-pop-up-color)] border-[var(--focus-color)]
                hover:bg-[var(--hover-pop-up-color)] hover:text-[var(--main-text)]
                {{-- border-b last:border-none  --}}
                text-sm">
                <div class="flex justify-between items-center my-1">
                    <span
                        class="text-[var(--contrast-main-text)] font-semibold leading-tight">{{ $option }}</span>
                    <span
                        class="bg-[var(--focus-color)] text-[var(--main-text)] text-xs text-white px-2 py-1 rounded-md ml-2">Pilih</span>
                </div>
            </div>
        @endforeach
    </div>
    @error($modelString)
        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
    @enderror
</div>
