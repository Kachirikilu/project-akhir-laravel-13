<div class="flex flex-col w-full">
    <div
        class="mt-4 flex items-center justify-between gap-3 p-2 pl-6 pr-4 bg-[var(--second-pop-up-color)] border table-border rounded-xl shadow-sm">

        <span class="text-sm font-semibold text-[var(--contrast-main-text)]">
            {{ $kelas->kode }}
        </span>

        <flux:button type="button" x-on:click="
                $store.jadwal?.reset();
                $store.jadwal?.setValueLeftJadwal(
                    '{{ $kelas->kode ?? '' }}',
                    '{{ $jadwal->label_extra ?? '' }}',
                );
                $flux.modal('left-jadwal-modal').show();
                $dispatch('open-left-jadwal-modal', { jadwal_id: {{ $jadwal->id }} });
            "
            variant="ghost"
            class="cursor-pointer text-red-600 hover:text-red-700 dark:hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
            <flux:icon name="arrow-right-start-on-rectangle" class="h-4 w-4" />
            Keluar Kelas</span>
        </flux:button>

    </div>
</div>
