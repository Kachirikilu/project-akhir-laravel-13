<div x-data="{
    itemsAll: @if ($pathString ?? null) @entangle($pathString).live
            @else
                null @endif
}"
    x-effect="
        if($store.{{ $alpine ?? 'config' }}?.isEdit === 0){
            @if ($valueString ?? null)
                $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '{{ $valueString }}';
            @else
                $store.{{ $alpine ?? 'config' }}.{{ $modelString }} = '';
            @endif
        }
    
    "
>

    @include('livewire.global.modal-form.partial.label')

    <div class="relative {{ $noLabel ?? false ? '' : 'mt-1' }}">

        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <flux:icon icon="{{ $iconString ?? 'variable' }}" variant="mini"
                x-bind:class="$store.{{ $alpine ?? 'config' }}?.colorIcon" />
        </div>


        <input type="text" readonly {{-- LIVEWIRE ENTANGLE --}}
            @if ($pathString ?? null) x-bind:value="
                    itemsAll?.{{ $modelString }}
                "

            {{-- ALPINE VARIABLE --}}
            @elseif($storeString ?? null)

                x-bind:value="
                    {{ $storeString }}
                    ?.{{ $modelString }}
                "

            @elseif($modelString ?? null)

                @if ($valueString ?? null)
                    value="{{ $valueString }}"
                @else
                    x-bind:value="
                    $store.{{ $alpine }}
                    ?.{{ $modelString }}
                " @endif
            @endif
        placeholder="{{ $placeholder ?? '--' }}"
        class="text-xs text-sm 
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
