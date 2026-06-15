@php
    $isDisabled = $disabled ?? false;
    $xOptions = is_array($xOptions ?? null) ? $xOptions : [];
    $xValues = is_array($xValues ?? null) ? $xValues : $xOptions;

    $storeName = $alpine ?? 'config';

    // contoh:
    // modelString = list_absensi_array
    // itemsString = 0.status
    $fullModelPath = isset($itemsString) ? "{$modelString}.{$itemsString}" : $modelString;
@endphp

<div class="relative" wire:key="select-form-{{ $modelString }}-{{ $storeName }}" x-data="{
    open: false,
    options: [],
    values: [],
    isDisabled: @js($isDisabled),

    getLabel(val) {
        if (val === '' || val === null || val === undefined) {
            return '';
        }

        const index = this.values.findIndex(
            item => String(item) === String(val)
        );

        return index !== -1 ? this.options[index] : '';
    },

    getNestedValue(obj, path) {
        return path.split('.').reduce((o, key) => o?.[key], obj);
    },

    setNestedValue(obj, path, value) {
        const keys = path.split('.');
        const lastKey = keys.pop();

        const target = keys.reduce((o, key) => {
            if (!o[key]) o[key] = {};
            return o[key];
        }, obj);

        target[lastKey] = value;
    },

    value: '',

    @if($isLivewire ?? false)
    @if(isset($itemsString))
    valueInput: @entangle($modelString . '.' . $itemsString).live,
    @else
    valueInput: @entangle($modelString).live,
    @endif
    @endif
}"
    x-init="value = getLabel(
        getNestedValue(
            $store.{{ $storeName }},
            '{{ $fullModelPath }}'
        )
    );
    
    $watch(
        () => getNestedValue(
            $store.{{ $storeName }},
            '{{ $fullModelPath }}'
        ),
        (val) => {
            value = getLabel(val);
        }
    );
    
    @if($isLivewire ?? false)
    $watch('valueInput', (newVal) => {
        setNestedValue($store.{{ $storeName }}, '{{ $fullModelPath }}', newVal ?? '');
        value = getLabel(newVal);
    });
    @endif"
    x-effect="
        options = @js($xOptions);
        values = @js($xValues);

        isDisabled = @js($isDisabled);

        const rawVal = getNestedValue(
            $store.{{ $storeName }},
            '{{ $fullModelPath }}'
        );

        if ($store.{{ $storeName }}?.isEdit === 0) {
            value = '';
        } else {
            value = getLabel(rawVal);
        }

        @if ($isLivewire ?? false)
            setNestedValue(
                $store.{{ $storeName }},
                '{{ $fullModelPath }}',
                valueInput ?? ''
            );
        @endif
    ">
    <label for="{{ $modelString }}" class="block text-sm font-medium" :class="isDisabled ? 'opacity-50' : ''">
        {{ $nameXString ?? ucfirst($modelString) }}

        @if ($isRequired ?? true)
            <span class="text-red-500" x-show="!isDisabled">*</span>
        @endif
    </label>

    <div class="relative mt-2">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="isDisabled
                    ?
                    'text-gray-400' :
                    ($store.{{ $storeName }}?.colorIcon)" />
        </div>

        <input autocomplete="off" x-model="value" type="text" readonly @click="if (!isDisabled) open = true"
            @click.outside="open = false" @keydown.escape.window="open = false" id="{{ $modelString }}"
            :placeholder="isDisabled
                ?
                '{{ $placeholder ?? 'Terkunci' }}' :
                '{{ $placeholder ?? 'Pilih Opsi' }}'"
            :class="isDisabled
                ?
                'bg-gray-100 dark:bg-zinc-800 cursor-not-allowed opacity-70 text-gray-500 border-gray-200' :
                'bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)] cursor-pointer'"
            class="focus:ring-2 focus:ring-[var(--focus-color)] outline-none w-full border rounded-lg pl-10 px-3 py-2 pr-10 transition-all duration-200">

        <template x-if="!isDisabled">
            @if ($isLivewire ?? false)
                @include('livewire.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'value',
                    'xClick' => "
                        value = '';
                        valueInput = '';
                        setNestedValue(
                            \$store.$storeName,
                            '$fullModelPath',
                            ''
                        );
                    ",
                ])
            @else
                @include('livewire.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'value',
                    'xClick' => "value = ''",
                    'xAlpine' => $modelString,
                ])
            @endif
        </template>
    </div>

    <div x-show="open && !isDisabled" x-cloak x-transition
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl max-h-60 overflow-y-auto custom-scrollbar">
        @foreach ($xOptions as $i => $option)
            @php
                $label = is_array($option) ? $option['label'] ?? '-' : $option;

                $valueOption = is_array($option) ? $option['value'] ?? $label : $option;

                $selectedValue = $xValues[$i] ?? $valueOption;
            @endphp

            <div wire:key="option-{{ $i }}"
                @click="
                    const selectedValue =
                        {{ is_numeric($selectedValue) ? $selectedValue : "'{$selectedValue}'" }};

                    value = '{{ $label }}';

                    setNestedValue(
                        $store.{{ $storeName }},
                        '{{ $fullModelPath }}',
                        selectedValue
                    );

                    @if ($isLivewire ?? false) valueInput = selectedValue; @endif

                    open = false;
                "
                class="px-4 py-2 cursor-pointer hover:bg-[var(--hover-pop-up-color)]">
                <div class="flex justify-between items-center my-1">
                    <span class="text-[var(--contrast-main-text)] font-semibold">
                        {{ $label }}
                    </span>

                    <span class="bg-[var(--focus-color)] text-white text-xs px-2 py-1 rounded-md ml-2">
                        Pilih
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    @if (!empty($message))
        <span class="text-red-500 text-sm mt-1 block">
            {{ $message }}
        </span>
    @endif
</div>
