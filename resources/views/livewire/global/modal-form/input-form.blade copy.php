<div x-data="{
    showPassword: false,
    {{-- Inisialisasi tipe input awal --}}
    inputType: '{{ $typeString ?? 'text' }}'
}"
    x-effect="
        if($store.{{ $alpine ?? 'config' }}?.isEdit === 0){
            $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '';
        }
    "
    wire:key="input-form-{{ $modelString }}-{{ $alpine }}">

    @include('livewire.global.modal-form.partial.label')

    <div class="relative mt-1">
        {{-- Icon Samping Kiri --}}
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString }}" variant="mini"
                x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>

        <input x-model="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}" name="{{ $modelString }}"
            x-bind:value="$store.{{ $alpine ?? 'config' }}?.isEdit ? $el.value : ''" {{-- Tipe input dinamis: jika password, bisa berubah jadi 'text' --}}
            :type="inputType" id="{{ $modelString }}" placeholder="{{ $placeholder }}"
            class="bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)]
                {{-- placeholder-[var(--contrast-third-text)] --}}
                w-full border rounded-lg pl-10 px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"
            @if ($isFocusSelect ?? null) @focus="$el.select()" @endif
            @if (!empty($isKode) && $isKode > 0) maxlength="{{ $isKode }}"
                oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, {{ $isKode }})"

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

                            parts[0] = parts[0].slice(0, {{ $maxlength ?? 255 }});

                            if (parts.length > 1) {
                                parts[1] = parts[1].slice(0, 2);
                            }

                            this.value = parts.join('.');
                        "
            @elseif (isset($numberOnly) && $numberOnly) 
                inputmode="numeric" 
                oninput="this.value = this.value.replace(/[^{{ $noZero ?? null ? 1 : 0 }}-9]/g, '').slice(0, {{ $maxlength ?? 255 }})"
            @else
                maxlength="{{ $maxlength ?? 255 }}" @endif>

        {{-- Tombol Mata (Hanya muncul jika typeString adalah 'password') --}}
        @if (($typeString ?? '') === 'password')
            <button type="button" @click="showPassword = !showPassword; inputType = showPassword ? 'text' : 'password'"
                class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1 group focus:outline-none">

                {{-- Icon Mata Terbuka --}}
                <template x-if="!showPassword">
                    <flux:icon icon="eye" variant="mini" x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon"
                        class="group-hover:text-red-500 dark:group-hover:text-red-400 transition duration-200" />
                </template>

                {{-- Icon Mata Tertutup --}}
                <template x-if="showPassword">
                    <flux:icon icon="eye-slash" variant="mini"
                        class=" text-[var(--contrast-main-text)] group-hover:text-red-500 dark:group-hover:text-red-400  transition duration-200" />
                </template>
            </button>
        @endif
    </div>

    @if ($message ?? null)
        @error($modelString)
            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
        @enderror
    @endif
</div>
