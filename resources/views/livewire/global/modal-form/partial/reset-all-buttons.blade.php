<flux:modal name="reset-confirm-modal-{{ $idString }}" class="min-w-[22rem] space-y-6">
    <div class="space-y-2">
        <flux:heading size="lg">Hapus Semua Pilihan?</flux:heading>
        <flux:subheading>Tindakan ini akan mengosongkan semua daftar data {{ $nameXString }} yang
            sudah Anda pilih sebelumnya!</flux:subheading>
    </div>

    <div class="flex gap-2 justify-end">
        <flux:modal.close>
            <flux:button variant="ghost" class="cursor-pointer">Batal</flux:button>
        </flux:modal.close>

        <flux:button variant="danger" class="cursor-pointer"
            @click="clearAllItems(); Flux.modal('reset-confirm-modal-{{ $idString }}').close()">
            Ya, Hapus Semua
        </flux:button>
    </div>
</flux:modal>

<button type="button" x-show="(items?.length || 0) > 0" @click="resetItems()"
    class="cursor-pointer text-[10px] uppercase font-bold px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded-full transition-all flex items-center gap-1">
    <flux:icon.trash variant="micro" />
    Reset All
</button>
