<div>
    @php
        $alpineState = $alpine ?? 'config';
        $isLivewireState = $isLivewire ?? null;
        $modelLivewire = "{$alpineState}_input.{$modelString}";
    @endphp

    <div x-data="{
        itemsAll: @if ($pathString ?? null) @entangle($pathString).live
    @else
        null @endif,
        @if ($isLivewireState) valueInput: @entangle($modelLivewire).live, @endif
    }"
        @if ($isLivewireState) x-effect="
            if ($store.{{ $alpineState }}?.isEdit === 0) {
                $store.{{ $alpineState }}.{{ $modelString }} =
                    valueInput !== '' && valueInput != null
                        ? valueInput
                        : '{{ $valueString ?? '' }}';
            } else {
                $store.{{ $alpineState }}.{{ $modelString }} = valueInput;
            }
        " @endif>

        @include('livewire.global.modal-form.partial.label')

        <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">

            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <flux:icon icon="{{ $iconString ?? 'variable' }}" variant="mini"
                    x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
            </div>


            <input type="text" @focus="$el.select()" readonly
                @if ($pathString ?? null) x-bind:value="itemsAll?.{{ $modelString }} || '{{ $valueString ?? '' }}'"
                @elseif($storeString ?? null)
                    x-bind:value="{{ $storeString }}?.{{ $modelString }} || '{{ $valueString ?? '' }}'"
                @elseif($modelString ?? null)
                    x-bind:value="$store.{{ $alpine }}?.{{ $modelString }} || '{{ $valueString ?? '' }}'" @endif
                placeholder="{{ $placeholder ?? '--' }}"
                class="text-xs sm:text-sm 
                focus:ring-2 focus:ring-[var(--hover-table-color)] outline-none
                bg-[var(--second-table-color)]
                table-border
                text-[var(--contrast-main-text)]
                w-full
                border
                rounded-lg
                pl-10
                px-3
                py-2
                text-center
                font-bold
            ">

        </div>
    </div>
</div>
