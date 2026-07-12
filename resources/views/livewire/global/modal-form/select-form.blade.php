@php
    $isDisabled = $disabled ?? false;
    $xOptions = is_array($xOptions ?? null) ? $xOptions : [];
    $xValues = is_array($xValues ?? null) ? $xValues : $xOptions;
    $xPilih = is_array($xPilih ?? null) ? $xPilih : null;
    $isShow = $isShowFrist ?? null;

    $alpineState = $alpine ?? 'config';

    $isLivewireState = $isLivewire ?? null;
    $modelLivewire = "{$alpineState}_input.{$modelString}";

    $fullModelPath = isset($itemsString) ? "{$modelString}.{$itemsString}" : $modelString;
@endphp

<div class="relative" wire:key="select-form-{{ $modelString }}-{{ $alpineState }}" x-data="{
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

    @if($isLivewireState)
    @if(isset($itemsString))
    valueInput: @entangle($modelLivewire . '.' . $itemsString).live,
    @else
    valueInput: @entangle($modelLivewire).live,
    @endif
    @endif
}"
    x-init="$nextTick(() => {
        const isShow = {{ $isShow == 1 ? 'true' : 'false' }};
        const firstValue = '{{ $xValues[0] ?? '' }}';
        const firstLabel = '{{ $xOptions[0] ?? '' }}';
    
        let currentStored = getNestedValue($store.{{ $alpineState }}, '{{ $fullModelPath }}');
    
        if (isShow && (!currentStored || currentStored === '')) {
            setNestedValue($store.{{ $alpineState }}, '{{ $fullModelPath }}', firstValue);
            value = firstLabel;
        } else {
            value = getLabel(currentStored);
        }
    });
    
    
    @if($isLivewireState)
    setNestedValue(
        $store.{{ $alpineState }},
        '{{ $fullModelPath }}',
        valueInput
    );
    
    $watch('valueInput', value => {
        setNestedValue(
            $store.{{ $alpineState }},
            '{{ $fullModelPath }}',
            value
        );
    });
    @endif
    
    value = getLabel(
        getNestedValue(
            $store.{{ $alpineState }},
            '{{ $fullModelPath }}'
        )
    );
    
    $watch(
        () => getNestedValue(
            $store.{{ $alpineState }},
            '{{ $fullModelPath }}'
        ),
        val => value = getLabel(val)
    );"
    x-effect="
        options = @js($xOptions);
        values = @js($xValues);

        isDisabled = @js($isDisabled);

        const rawVal = getNestedValue(
            $store.{{ $alpineState }},
            '{{ $fullModelPath }}'
        );

        if ($store.{{ $alpineState }}?.isEdit === 0) {
            value = '';
        } else {
            value = getLabel(rawVal);
        }

        @if ($isLivewireState)
setNestedValue(
                $store.{{ $alpineState }},
                '{{ $fullModelPath }}',
                valueInput ?? ''
            );
@endif
    ">
    {{-- <label for="{{ $modelString }}" class="block text-sm font-medium" :class="isDisabled ? 'opacity-50' : ''">
        {{ $nameXString ?? ucfirst($modelString) }}

        @if ($isRequired ?? true)
            <span class="text-red-500" x-show="!isDisabled">*</span>
        @endif
    </label> --}}
    @include('livewire.global.modal-form.partial.label')


    <div class="relative {{ $noLabel ?? false ? '' : 'mt-2' }}">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="isDisabled
                    ?
                    'text-gray-400' :
                    ($store.{{ $alpineState }}?.colorIcon)" />
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
            class="placeholder-shown:pr-2 text-xs sm:text-sm focus:ring-2 focus:ring-[var(--focus-color)] outline-none w-full border rounded-lg pl-10 px-3 py-2 pr-10 transition-all duration-200">

        <template x-if="!isDisabled">
            @if ($isLivewireState)
                @include('livewire.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'value',
                    'xClick' => "
                                                                        value = '';
                                                                        valueInput = '';
                                                                        setNestedValue(
                                                                            \$store.$alpineState,
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
        class="scrollbar-medium bg-[var(--main-pop-up-color)] border-[var(--focus-color)] border absolute left-0 right-0 z-[100] mt-1 rounded-lg shadow-2xl {{ $maxH ?? 'max-h-80' }} overflow-y-auto custom-scrollbar">
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
                        $store.{{ $alpineState }},
                        '{{ $fullModelPath }}',
                        selectedValue
                    );
                    @if ($isLivewireState) valueInput = selectedValue; @endif
                    open = false;
                "
                class="px-4 py-2 cursor-pointer hover:bg-[var(--hover-pop-up-color)] active:bg-[var(--hover-pop-up-color)]/90">
                <div class="flex flex-wrap items-start gap-x-4 gap-y-1 my-1">
                    <span class="my-2 flex-1 text-xs sm:text-sm text-[var(--contrast-main-text)] font-semibold">
                        {{ $label }}
                    </span>
                    <span class="my-1 shrink-0 text-xs sm:text-sm bg-[var(--focus-color)] text-white px-2 py-1 rounded-md">
                        {{ $xPilih[$i] ?? 'Pilih' }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    @if (!empty($message))
        <span class="text-xs sm:text-sm text-red-500 mt-1 block">
            {{ $message }}
        </span>
    @endif
</div>
