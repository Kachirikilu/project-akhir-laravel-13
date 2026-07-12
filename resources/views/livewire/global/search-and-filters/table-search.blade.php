<th rowspan="{{ $rowSpan ?? 1 }}" colspan="{{ $colspan ?? 1 }}"
    class="p-6 {{ $isSubHeader ?? false ? 'bg-gray-100/50' : '' }}
        {{ $isBorderL ?? false ? 'border-l' : '' }}
        {{ $isBorderR ?? false ? 'border-r' : '' }}
        {{ $pTop ?? false ? 'pt-' . $pTop . ' pb-6' : 'py-6' }}
        bg-[var(--main-table-color)] table-border relative border-b
        
        {{ ($isSticky ?? false) 
            ? 'sticky left-0 border-x z-30' 
            : '' 
        }}

        {{ (!($isSticky ?? false)) && (($isBorderX ?? false) || ($isMain ?? false)) ? 'border-x' : '' }}
    ">
    <div class="flex flex-col gap-1 items-center">

        @include('livewire.global.table.head-table', [
            'sortFieldString' => $sortFieldString,
            'headString' => $headString ?? null,
            'withTh' => 0,
        ])

        <div x-data="{ value: @entangle($modelString) }" class="sm:col-span-4 relative w-fit">
            <div class="relative">

                <input x-model="value" wire:model.live.debounce.300ms="{{ $modelString }}" type="text"
                    placeholder="{{ $placeholder }}"
                    @if (isset($withSimbol) && $withSimbol) inputmode="text"
                        oninput="
                            let val = this.value.replace(/,/g, '.');
                            val = val.replace(/[^0-9.><=≥≤]/g, '');
                            let operator = '';
                            let number = val;

                            const match = val.match(/^(>=|<=|=>|=<|>|<|=|≥|≤)/);
                            if (match) {
                                operator = match[0]
                                    .replace('=>', '>=')
                                    .replace('=<', '<=');
                                number = val.substring(match[0].length);
                            }

                            number = number.replace(/[^0-9.]/g, '');
                            let parts = number.split('.');

                            if (parts.length > 2) {
                                number = parts[0] + '.' + parts.slice(1).join('');
                                parts = number.split('.');
                            }

                            parts[0] = parts[0].slice(0, {{ $maxLength ?? 255 }});
                            if (parts.length > 1) {
                                parts[1] = parts[1].slice(0, 2);
                            }

                            this.value = operator + parts.join('');
                        "
                    @elseif (isset($floatOnly) && $floatOnly)
                    {{-- @if (isset($floatOnly) && $floatOnly) --}}
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
                    @elseif (isset($numberOnly) && $numberOnly) 
                        inputmode="numeric" 
                        oninput="this.value = this.value.replace(/[^{{ $noZero ?? null ? 1 : 0 }}-9]/g, '').slice(0, {{ $maxLength ?? 255 }})"
                    @else
                        maxLength="{{ $maxLength ?? 255 }}" @endif
                    class="placeholder-shown:pr-2 mt-1 text-[10px] w-{{ $wInput ?? '13' }} border-gray-300 dark:border-neutral-700 rounded-md focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1 shadow-sm block">

                {{-- Tombol Reset --}}
                @include('livewire.global.search-and-filters.partial.reset-button', [
                    'xShow' => 'value',
                    'xClick' => "value = ''",
                    'xWire' => $resetXFilter,
                    'xSize' => 3,
                    'xPr' => 1,
                ])

            </div>
        </div>

    </div>

</th>
