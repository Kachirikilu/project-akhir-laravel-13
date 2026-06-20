<template x-if="items.length > 0">
    <div
        class="mt-2 px-4 py-3 bg-[var(--focus-color)]/10 border border-[var(--focus-color)]/20 rounded-lg flex justify-between items-center">
        <span class="text-xs font-bold uppercase"
            x-text="
                            grandTotalBobot <= {{ $nilai1 }} ? '{{ $pNilai1 }}' : 
                            (grandTotalBobot < {{ $nilai2 }} ? '{{ $pNilai2 }}' : 
                            (grandTotalBobot <= {{ $nilai3 }} ? '{{ $pNilai3 }}' : 
                            '{{ $pNilai4 }}'))
                    "></span>
        <template x-if="grandTotalBobot <= {{ $nilai1 }}">
            <flux:badge color="red" size="sm" variant="pill" class="text-xs sm:text-sm">
                <span x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot > {{ $nilai1 }} && grandTotalBobot <= {{ $nilai2 }}">
            <flux:badge color="orange" size="sm" variant="pill" class="text-xs sm:text-sm">
                <span x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot > {{ $nilai2 }} && grandTotalBobot <= {{ $nilai3 }}">
            <flux:badge color="green" size="sm" variant="pill" class="text-xs sm:text-sm">
                <span x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
        <template x-if="grandTotalBobot > {{ $nilai3 }}">
            <flux:badge color="blue" size="sm" variant="pill" class="text-xs sm:text-sm">
                <span x-text="grandTotalBobot"></span>%
            </flux:badge>
        </template>
    </div>
</template>
