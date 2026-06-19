<div
    x-data="{
        @if($isLivewire ?? false)
            valueInput: @entangle($modelString).live,
        @endif

        showPassword: false,

        inputType:
            @if (($isDate ?? false) === 1 || ($isDate ?? false) === true || ($isDate ?? false) === 'date')
                'date'
            @elseif (($isDate ?? false) === 'month')
                'month'
            @elseif (($isDate ?? false) === 'year')
                'number'
            @elseif ($isTime ?? false)
                'time'
            @elseif ($isWeek ?? false)
                'week'
            @else
                '{{ $typeString ?? 'text' }}'
            @endif
    }"

    x-effect="
        const store = $store.{{ $alpine ?? 'config' }};

        if (!store) return;

        if (store.isEdit === 0) {
            store.{{ $modelString }} = '';
            return;
        }

        @if($isLivewire ?? false)
            store.{{ $modelString }} = valueInput ?? '';
        @endif
    "

    wire:key="input-form-{{ $modelString }}-{{ $alpine }}"
>

    @include('livewire.global.modal-form.partial.label')

    <div class="relative mt-1">

        {{-- Icon Samping Kiri --}}
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input
            @if($isLivewire ?? false)
                wire:model.live="{{ $modelString }}"
            @endif
            x-model="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}"
            name="{{ $modelString }}"
            x-bind:value="$store.{{ $alpine ?? 'config' }}?.isEdit ? $el.value : ''"

            {{-- Tipe input dinamis --}}
            :type="inputType"

            id="{{ $modelString }}"
            placeholder="{{ $placeholder ?? null }}"

            class="bg-[var(--second-table-color)] table-border text-[var(--contrast-main-text)]
            w-full border rounded-lg pl-10 px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"

            {{-- Auto Select --}}
            @if ($isFocusSelect ?? null)
                @focus="$el.select()"
            @endif

            {{-- YEAR ONLY --}}
            @if (($isDate ?? false) === 'year')
                inputmode="numeric"
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

            {{-- FLOAT ONLY --}}
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

                    this.value = parts.join('.');
                "

            {{-- NUMBER ONLY --}}
            @elseif (isset($numberOnly) && $numberOnly)

                inputmode="numeric"

                oninput="
                    this.value = this.value
                        .replace(/[^{{ $noZero ?? null ? 1 : 0 }}-9]/g, '')
                        .slice(0, {{ $maxLength ?? 255 }})
                "

            {{-- DEFAULT --}}
            @else
                maxLength="{{ $maxLength ?? 255 }}"
            @endif
        >

        {{-- Tombol Mata --}}
        @if (($typeString ?? '') === 'password')

            <button
                type="button"
                @click="
                    showPassword = !showPassword;
                    inputType = showPassword ? 'text' : 'password'
                "
                class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1 group focus:outline-none">

                {{-- Icon Mata Terbuka --}}
                <template x-if="!showPassword">
                    <flux:icon
                        icon="eye"
                        variant="mini"
                        x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon"
                        class="cursor-pointer group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                </template>

                {{-- Icon Mata Tertutup --}}
                <template x-if="showPassword">
                    <flux:icon
                        icon="eye-slash"
                        variant="mini"
                        class="cursor-pointer text-[var(--contrast-main-text)] group-hover:text-red-500 dark:group-hover:text-red-400 group-active:text-red-500/90 dark:group-active:text-red-400/90 transition duration-200" />
                </template>

            </button>

        @endif

    </div>

    {{-- Error Message --}}
    @if ($message ?? null)
        @error($modelString)
            <span class="text-xs sm:text-sm text-red-500 mt-1 block">
                {{ $message }}
            </span>
        @enderror
    @endif

</div>