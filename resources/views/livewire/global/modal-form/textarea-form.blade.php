<div x-data="{ 
    parentSelected: @isset($parentIdString) @entangle($parentIdString).live @else null @endisset,
    hasParent: {{ isset($parentIdString) ? 'true' : 'false' }},
    
    get isParentReady() {
        if (!this.hasParent) return true;
        if (Array.isArray(this.parentSelected)) return this.parentSelected.length > 0;
        return this.parentSelected != null && this.parentSelected != '';
    }
}"
x-effect="
    if($store.{{ $alpine ?? 'config' }}?.isEdit === 0 && !hasParent){
        $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '';
    }
" 
wire:key="input-form-{{ $modelString }}-{{ $alpine }}">
    <div :class="(hasParent && !isParentReady) ? 'opacity-50 transition-opacity' : ''">
        @include('livewire.global.modal-form.partial.label')
    </div>

    <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">
        <div class="absolute top-3 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini" 
                x-bind:class="isParentReady ? ($store.{{ $alpine ?? 'config' }}?.colorIcon ?? 'text-blue-500') : 'text-gray-400'" />
        </div>

        <textarea 
            x-model.debounce.500ms="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}"
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