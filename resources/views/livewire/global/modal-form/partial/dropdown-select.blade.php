<template x-if="items.includes({{ $x['id'] }})">
    <div class="cursor-pointer relative flex items-center justify-center">
        <flux:icon icon="check" variant="mini" class="group-hover:hidden group-active:hidden" />
        <flux:icon icon="trash" variant="mini" class="hidden group-hover:block group-active:block" />
    </div>
</template>

{{-- State: Belum Terpilih (Tampilkan Plus) --}}
<template x-if="!items.includes({{ $x['id'] }})">
    <div class="cursor-pointer flex items-center justify-center">
        <flux:icon icon="plus" variant="mini" />
    </div>
</template>
