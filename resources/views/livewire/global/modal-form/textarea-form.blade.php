@php
    $alpineState = $alpine ?? 'config';
    $isLivewireState = $isLivewire ?? null;
    $modelLivewire = "{$alpineState}_input.{$modelString}";
        $noEntangle = $noEntangle ?? false;
        $isBlur = $isLivewireBlur ?? false;
@endphp

<div x-data="{ 
        @if (!$noEntangle)
            @if ($isLivewireState)
                @if (isset($itemsString))
                    valueInput: @entangle($modelLivewire.'.'.$itemsString).live,
                @else
                    valueInput: @entangle($modelLivewire).live,
                @endif
            @endif
        @else
            valueInput: '',
        @endif
    parentSelected: @isset($parentIdString) @entangle($parentIdString).live @else null @endisset,
    hasParent: {{ isset($parentIdString) ? 'true' : 'false' }},
    
    get isParentReady() {
        if (!this.hasParent) return true;
        if (Array.isArray(this.parentSelected)) return this.parentSelected.length > 0;
        return this.parentSelected != null && this.parentSelected != '';
    }
}"
x-effect="
    if($store.{{ $alpineState }}?.isEdit === 0 && !hasParent){
        $store.{{ $alpineState }}.{{ $modelString }} = '';
    }
" 
wire:key="input-form-{{ $modelString }}-{{ $alpine }}">
    <div :class="(hasParent && !isParentReady) ? 'opacity-50 transition-opacity' : ''">
        @include('livewire.global.modal-form.partial.label')
    </div>

    <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">
        <div class="absolute top-3 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" 
                x-bind:class="isParentReady ? ($store.{{ $alpineState }}?.colorIcon ?? 'text-blue-500') : 'text-gray-400'" />
        </div>

        <textarea 
                @if (!$noEntangle) @if ($isLivewireState)
                    @if (isset($itemsString))
                        wire:model="{{ $modelLivewire . '.' . $itemsString }}"
                    @elseif ($isBlur)
                        wire:model.live.blur="{{ $modelLivewire }}"
                    @else
                        wire:model="{{ $modelLivewire }}" @endif
                    @endif
                @endif

                @if (!$isLivewireState || ($isXModal ?? false)) @if (isset($itemsString))
                        x-model="valueInput"
                    @else
                        x-model="$store.{{ $alpineState }}.{{ $modelString }}" @endif
                @endif
            {{-- x-model="$store.{{ $alpineState }}.{{ $modelString }}" --}}
            id="{{ $modelString }}"
            :disabled="!isParentReady"
            :placeholder="isParentReady 
                ? '{{ $placeholder }}' 
                : 'Pilih {{ $nameXParent ?? 'Parent' }} terlebih dahulu...'"
            :class="!isParentReady 
                ? 'opacity-50 cursor-not-allowed bg-gray-100 dark:bg-neutral-800' 
                : 'bg-[var(--second-table-color)]'"
            class="text-xs sm:text-sm focus:ring-2 focus:ring-[var(--focus-color)] outline-none table-border text-[var(--contrast-main-text)] w-full border rounded-lg pl-10 pr-4 py-2.5 min-h-[100px] transition-all"
        ></textarea>
    </div>

    @error($modelString)
        <span class="text-xs sm:text-sm text-red-500 mt-1 block">{{ $message }}</span>
    @enderror
</div>