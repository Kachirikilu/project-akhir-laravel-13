<div>
    @php
        $alpineState = $alpine ?? 'config';
        $isLivewireState = $isLivewire ?? null;
        $modelLivewire = "{$alpineState}_input.{$modelString}";
        $noEntangle = $noEntangle ?? false;
        $isBlur = $isLivewireBlur ?? false;
    @endphp

    <div x-data="{
        @if (!$noEntangle) @if ($isLivewireState) @if (isset($itemsString))
                valueInput: @entangle($modelLivewire.'.'.$itemsString).live,
            @else
                valueInput: @entangle($modelLivewire).live, @endif
        @endif
        @else
        valueInput: '',
            @endif
    
        showPassword: {{ $showPassword ?? 'false' }},
    
            inputType:
            @if (($isDate ?? false) === 1 || ($isDate ?? false) === true || ($isDate ?? false) === 'date') 'date'
            @elseif (($isDate ?? false) === 'month')
                'month'
            @elseif (($isDate ?? false) === 'year')
                'number'
            @elseif ($isTime ?? false)
                'time'
            @elseif ($isWeek ?? false)
                'week'
            @else
                '{{ $typeString ?? 'text' }}' @endif
    }"
        x-effect="
    const store = $store.{{ $alpineState }};

    if (!store) return;

    const setNestedValue = (obj, path, value) => {
        const keys = path.split('.');
        let current = obj;

        for (let i = 0; i < keys.length - 1; i++) {

            const key = isNaN(keys[i])
                ? keys[i]
                : Number(keys[i]);

            if (current[key] === undefined) {
                current[key] = {};
            }

            current = current[key];
        }

        const lastKey = isNaN(keys[keys.length - 1])
            ? keys[keys.length - 1]
            : Number(keys[keys.length - 1]);

        current[lastKey] = value;
    };

    if (store.isEdit === 0) {

        @if ($isLivewireState)
            @if (isset($itemsString))
                setNestedValue(
                    store,
                    '{{ $modelString . '.' . $itemsString }}',
                    ''
                );
@else
store.{{ $modelString }} = '';
            @endif
        @endif

        return;
    }

    @if ($isLivewireState)

        @if (isset($itemsString))
            setNestedValue(
                store,
                '{{ $modelString . '.' . $itemsString }}',
                valueInput ?? ''
            );
@else
store.{{ $modelString }} = valueInput ?? '';
        @endif

            @endif
        "
        wire:key="input-form-{{ $modelString }}-{{ $alpine }}">

        @include('livewire.global.modal-form.partial.label')

        <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">

            {{-- Icon Samping Kiri --}}
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                @if (($typeString ?? '') === 'password')
                    <template x-if="!showPassword">
                        <flux:icon icon="{{ $iconString ?? 'lock-closed' }}" variant="mini"
                            x-bind:class="$store.{{ $alpineState }}?.colorIcon"
                            class="cursor-pointer group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                    </template>

                    <template x-if="showPassword">
                        <flux:icon icon="{{ $icon2String ?? 'lock-open' }}" variant="mini"
                            x-bind:class="$store.{{ $alpineState }}?.colorIcon"
                            class="cursor-pointer text-[var(--contrast-main-text)] group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                    </template>
                @else
                    <flux:icon icon="{{ $iconString }}" variant="mini"
                        x-bind:class="$store.{{ $alpineState }}?.colorIcon" />

                @endif
            </div>

            {{-- Icon Mata Terbuka --}}


            <input @if ($isReadonly ?? null) readonly @endif
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

            name="{{ $modelString }}"
            x-bind:value="$store.{{ $alpineState }}?.isEdit ? $el.value : ('{{ $oldValue ?? ($value ?? '') }}')"
            {{-- Tipe input dinamis --}}
            :type="inputType" id="{{ $modelString }}" placeholder="{{ $placeholder ?? null }}"
            class="text-xs sm:text-sm bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
            focus:ring-2 {{ $isReadonly ?? null ? 'focus:ring-[var(--hover-table-color)]' : 'focus:ring-[var(--focus-color)]' }} outline-none w-full border rounded-lg pl-10 px-3 py-2"
            {{-- Auto Select --}} @if ($isFocusSelect ?? null) @focus="$el.select()" @endif {{-- YEAR ONLY --}}
            @if (($isDate ?? false) === 'year') inputmode="numeric"
                oninput="
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0,4);
                    let val = parseInt(this.value);
                    let min = {{ $minValue ?? 1900 }};
                    let max = {{ $maxValue ?? 9999 }};
                    if (!isNaN(val)) {
                        if (val < min) {
                            this.value = min;
                        }
                        if (val > max) {
                            this.value = max;
                        }
                    }
                "
            {{-- KODE ONLY --}}
            @elseif (!empty($isKode) && $isKode > 0)
                maxLength="{{ $isKode }}"
                oninput="
                    this.value = this.value
                        .replace(/[^a-zA-Z]/g, '')
                        .toUpperCase()
                        .slice(0, {{ $isKode }})
                "
            {{-- Nomor Telepon --}}
            @elseif (isset($isNoHP) && $isNoHP)
                type="text"
                inputmode="numeric"
                maxlength="18"
                x-data="{
                    format(el) {
                        let selectionStart = el.selectionStart;
                        let val = el.value;
                        let numbersBeforeCursor = val.substring(0, selectionStart).replace(/\D/g, '').length;

                        // 2. Format nilai
                        let clean = val.replace(/\D/g, '');
                        let parts = [];
                        if (clean.length > 0) parts.push(clean.substring(0, 3));
                        if (clean.length > 3) parts.push(clean.substring(3, 7));
                        if (clean.length > 7) parts.push(clean.substring(7, 12));
                        
                        let formatted = parts.join(' - ');
                        el.value = formatted;

                        let newCursorPos = 0;
                        let count = 0;
                        for (let i = 0; i < formatted.length; i++) {
                            if (/\d/.test(formatted[i])) count++;
                            if (count === numbersBeforeCursor) {
                                newCursorPos = i + 1;
                                break;
                            }
                        }
                        el.setSelectionRange(newCursorPos, newCursorPos);
                    }
                }"
                x-init="format($el)"
                x-on:input="format($el)"
                x-on:keydown.backspace="
                    let pos = $el.selectionStart;
                    if ($el.value.substring(pos - 3, pos) === ' - ') {
                        $el.setSelectionRange(pos - 2, pos - 2);
                    }
                "
            {{-- Number/ FLOAT ONLY --}}
            @elseif (isset($floatOnly) && $floatOnly)

                inputmode="decimal"

                oninput="
                    let val = this.value.replace(/,/g, '.');
                    val = val.replace(/[^0-9.]/g, '');
                    let parts = val.split('.');
                    if (parts.length > 2) {
                        val = parts[0] + '.' + parts.slice(1).join('');
                        parts = val.split('.');
                    }
                    parts[0] = parts[0].slice(0, {{ $maxLength ?? 255 }});
                    if (parts.length > 1) {
                        parts[1] = parts[1].slice(0, 2);
                    }
                    val = parts.join('.');
                    @if ($maxValue ?? null)
                        let numVal = parseFloat(val);
                        let maxVal = {{ $maxValue }};
                        if (!isNaN(numVal) && numVal > maxVal) {
                            val = maxVal.toString();
                        } @endif
            this.value = val;
            "
        @elseif (isset($numberOnly) && $numberOnly)
            inputmode="numeric"

            oninput=" let val = this.value;
                        val = val.replace(/[^0-9]/g, '');
                        @if ($maxLength ?? null) if (val.length > {{ $maxLength }}) {
                                val = val.slice(0, {{ $maxLength }});
                            } @endif

                        @if ($maxValue ?? null) let numVal = parseInt(val || 0);
                            let maxVal = {{ $maxValue }};

                            if (numVal > maxVal) {
                                val = maxVal.toString();
                            } @endif
                        this.value = val;
                    "

            onkeydown="
                    if (event.key === 'e' || event.key === 'E' || event.key === '.' || event.key === ',') {
                        event.preventDefault();
                    }
                "
        @else
            maxLength="{{ $maxLength ?? 255 }}"
            @endif>

            {{-- Tombol Mata --}}
            @if (($typeString ?? '') === 'password')
                <button type="button"
                    @click="
                        showPassword = !showPassword;
                        inputType = showPassword ? 'text' : 'password'
                    "
                    class="absolute inset-y-0 right-0 flex items-center pr-3 group focus:outline-none">

                    {{-- Icon Mata Terbuka --}}
                    <template x-if="!showPassword">
                        <flux:icon icon="eye" variant="mini" x-bind:class="$store.{{ $alpineState }}?.colorIcon"
                            class="cursor-pointer group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                    </template>

                    {{-- Icon Mata Tertutup --}}
                    <template x-if="showPassword">
                        <flux:icon icon="eye-slash" variant="mini"
                            class="cursor-pointer text-[var(--contrast-main-text)] group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                    </template>

                </button>
            @endif

        </div>

        {{-- Error Message --}}
        {{-- @if ($message ?? null)
        @error($modelString)
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">
                {{ $message }}
            </span>
        @enderror
    @endif --}}

        @if (!empty($message))
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">
                {{ $message }}
            </span>
        @endif

    </div>
</div>
