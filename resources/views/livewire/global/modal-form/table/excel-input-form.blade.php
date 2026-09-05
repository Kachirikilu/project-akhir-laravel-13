@php
    $widthStyle = isset($minW) ? "min-width: {$minW}px;" : 'min-width: 120px;';

    $bgClass = isset($message)
        ? 'bg-red-50 dark:bg-red-950/30 text-red-600'
        : ((isset($isReadonly) && $isReadonly) || (isset($isDark) && $isDark)
            ? 'bg-gray-100 dark:bg-neutral-800'
            : 'bg-white dark:bg-neutral-600');

    $isLive = $isLive ?? false;
    $wireModelAttr = isset($wireModel) && $wireModel 
        ? ($isLive ? "wire:model.live={$wireModel}" : "wire:model={$wireModel}") 
        : '';
@endphp

<td class="{{ $bgClass }} {{ $isSticky ?? null ? 'lg:sticky lg:left-0 lg:z-10' : '' }} p-0 border border-gray-200 dark:border-neutral-700 align-top">
    <div class="flex flex-col h-full min-h-[34px] w-full">
        <div class="grid w-full items-center" style="{{ $widthStyle }}">

            {{-- Elemen bayangan untuk autowidth cell --}}
            <span class="invisible col-start-1 row-start-1 px-3 py-2 text-xs sm:text-sm whitespace-nowrap">
                {{ isset($xHeadString) ? $xHeadString . ' ' : '' }}{{ $model ?? '' }}
            </span>

            @if (isset($isSelect) && $isSelect)

                <select {!! $wireModelAttr !!} {{ isset($isReadonly) && $isReadonly ? 'disabled' : '' }}
                    class="{{ $bgClass }} col-start-1 row-start-1 w-full h-full border-0 rounded-none px-3 py-2 text-xs sm:text-sm outline-none">

                    {{-- Placeholder Opsional --}}
                    @if (isset($placeholder))
                        <option value="">{{ $placeholder }}</option>
                    @endif

                    @foreach ($xOptions ?? [] as $index => $option)
                        @php
                            $val = isset($xValues[$index]) ? $xValues[$index] : $option;
                        @endphp
                        <option value="{{ $val }}">
                            {{ $option }}
                        </option>
                    @endforeach

                </select>

            @else

                {{-- Kontainer Input dengan Prefix Head --}}
                <div class="col-start-1 row-start-1 flex items-center w-full h-full">
                    
                    @if (isset($xHeadString) && $xHeadString)
                        <span class="pl-3 pr-1 text-xs sm:text-sm font-medium text-gray-500 dark:text-neutral-400 select-none whitespace-nowrap">
                            {{ $xHeadString }}
                        </span>
                    @endif

                    <input type="{{ $inputType ?? 'text' }}"
                        {!! $wireModelAttr !!}
                        {{ isset($isReadonly) && $isReadonly ? 'readonly' : '' }}
                        @if ($modelAlpine ?? null) :value="{{ $modelAlpine }}" @endif
                        @if (isset($numberOnly) && $numberOnly) 
                            inputmode="numeric"
                            oninput="
                                this.value = this.value
                                    .replace(/[^{{ $noZero ?? null ? 1 : 0 }}-9]/g, '')
                                    .slice(0, {{ $maxLength ?? 255 }})
                            " 
                        @endif
                        class="{{ $bgClass }} w-full h-full border-0 rounded-none {{ isset($xHeadString) && $xHeadString ? 'pl-0 pr-3' : 'px-3' }} py-2 text-xs sm:text-sm outline-none cursor-text select-text">
                </div>

            @endif
        </div>

        @if (isset($message) && is_array($message) && isset($message[0]))
            <p class="text-xs sm:text-sm text-red-500 text-[10px] px-2 py-0.5 bg-red-50 dark:bg-red-950/30 border-t border-red-200 whitespace-nowrap">
                {{ $message[0] }}
            </p>
        @endif
    </div>
</td>