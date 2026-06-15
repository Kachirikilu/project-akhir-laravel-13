{{-- Header Section --}}
<div class="mb-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ $backUrl ?? route('kelas-management') }}" wire:navigate
            class="p-2 rounded-full hover:bg-[var(--hover-table-color)] transition-colors">
            <flux:icon name="arrow-left" class="h-6 w-6 text-[var(--contrast-second-text)]" />
        </a>
        <div>
            <h2 class="text-2xl font-bold text-[var(--contrast-second-text)]">{{ $kelas->kelas }}</h2>
            <p class="text-[var(--contrast-main-text)] opacity-70">Manajemen {{ $subHead }} dan Detail untuk
                {{ $mainHead }} ini</p>
        </div>
    </div>

    {{-- Grid Informasi Utama Kelas --}}
    <div
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-9 bg-[var(--second-pop-up-color)] p-6 rounded-xl border table-border shadow-sm">
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Kode
                {{ $mainHead }}</span>
            <span class="text-lg font-semibold text-[var(--focus-color)]">{{ $mainKode ?? '---' }}</span>

            @if ($subLabel ?? false)
                <span class="text-xs text-[var(--contrast-main-text)] opacity-70">{{ $subLabel ?? '---' }}</span>
            @endif
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Mata
                Kuliah</span>
            <span class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kelas->mk ?? '-----' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
                {{ $kelas->kode_mk ?? '---' }}
                <strong class="px-2">|</strong>
                Sem {{ $kelas->semester ?? '-' }}</span>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">Program
                Studi</span>
            <span class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kelas->prodi ?? '-' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">{{ $kelas->kode_pr ?? '---' }}
                <strong class="px-2">|</strong>
                {{ $kelas->pr_rel->fakultas_fk ?? '----' }}
            </span>
        </div>
        <div class="flex flex-col gap-1">
            <span class="text-xs uppercase tracking-wider text-[var(--contrast-main-text)] opacity-60 font-bold">RPS /
                SKS</span>
            <span class="text-lg font-semibold text-[var(--contrast-second-text)]">{{ $kode_rps_url ?? '---' }}</span>
            <span class="text-xs text-[var(--contrast-main-text)] opacity-70">
                {{ $kelas->sks ?? '-' }} SKS
                <strong class="px-2">|</strong>
                {{ $kelas->sks_text ?? '-' }}</span>
        </div>

        @if ($alpine == 'sesi')
            @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.absensi-header')
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-3 my-4">

        <flux:button
            @click="
                $store.{{ $alpine ?? 'jadwal' }}?.resetShow();
                $store.{{ $alpine ?? 'jadwal' }}?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.{{ $alpine ?? 'jadwal' }}?.setShowRPS(
                        '{{ $kelas->rps_id ?? '' }}',
                    );

                    $flux.modal('rps-detail-modal').show();
            "
            wire:click="showRPS({{ $kelas->rps_id }})" icon="eye" size="sm"
            class="!cursor-pointer px-6 !text-cyan-600 dark:!text-cyan-400 !bg-cyan-50 hover:!bg-cyan-100 dark:!bg-cyan-950/20 dark:hover:!bg-cyan-900/30 !border-cyan-200/60 dark:!border-cyan-800/40 transition-all duration-200">
            <span>Show RPS</span>
        </flux:button>


        <div class="shrink-0">
            @include('livewire.global.table.export-button', [
                'nameXString' => 'Print RPS',
                'xString' => "printPDFRPS($kelas->rps_id)",
                'valuePx' => 6,
                'isFull' => 1,
                'isTextMd' => 1,
                'color' => 'rose',
            ])
        </div>

        @if ($alpine == 'jadwal' && (Auth::user()?->admin || Auth::user()?->dosen))
            <flux:button
                @click="
                    $store.kelas?.reset();
                    $store.kelas?.setEdit(1);

                    $store.kelas?.setColor('text-emerald-700 dark:text-emerald-400');

                    $store.kelas?.setValueKelas(
                        '{{ $kelas->kode ?? '' }}',
                        '{{ $kelas->kelas ?? '' }}',
                        '{{ $kelas->deskripsi_kelas ?? '' }}',

                        '{{ $kelas->pr_id ?? '' }}',
                        '{{ $kelas->kode_pr ?? '' }}',
                        '{{ $kelas->prodi ?? '' }}',
                        '{{ $kelas->pr_rel?->departemen_dp ?? '' }}',
                        '{{ $kelas->pr_rel?->fakultas_fk ?? '' }}',

                        '{{ $kelas->rps_id ?? '' }}',
                        '{{ $x->kode_rps ?? '' }}',
                        '{{ $x->rps_rel?->rps ?? '' }}',
                        '{{ $x->rps_rel?->sks_full ?? '' }}',
                        '{{ $x->wajib_text ?? '' }}',
                        '{{ $x->rps_rel?->draf_full ?? '' }}',
                    );

                    $flux.modal('kelas-modal').show();
                "
                wire:click="editKelas({{ $kelas->id }})" icon="pencil-square" size="sm"
                class="!cursor-pointer px-6 !text-yellow-600 dark:!text-yellow-400 !bg-yellow-50 hover:!bg-yellow-100 dark:!bg-yellow-950/20 dark:hover:!bg-yellow-900/30 !border-yellow-200/60 dark:!border-yellow-800/40 transition-all duration-200">
                <span>Edit Kelas</span>
            </flux:button>
        @endif

        @if (Auth::user()?->admin || Auth::user()?->dosen)
            @if ($alpine == 'sesi')
                <flux:button
                    @click="
                    $store.jadwal?.reset();
                    $store.jadwal?.setEdit(1);

                    $store.jadwal?.setColor('text-amber-700 dark:text-amber-400');

                    $store.jadwal?.setValueJadwal(
                        '{{ $jadwal->label_kelas ?? '' }}',
                        '{{ $jadwal->kode_wilayah ?? '' }}',

                        '{{ $jadwal->hari_pelaksanaan ?? '' }}',
                        '{{ $jadwal->jam_mulai ?? '' }}',
                        '{{ $jadwal->jam_berakhir ?? '' }}',
                        '{{ $jadwal->tanggal_mulai ?? '' }}',
                        '{{ $jadwal->tanggal_berakhir ?? '' }}',

                        '{{ $jadwal->kapasitas ?? '' }}',
                        '{{ $jadwal->password ?? '' }}',
                    );

                    $flux.modal('jadwal-modal').show();
                "
                    wire:click="editJadwal({{ $jadwal_id_url }})" icon="pencil-square" size="sm"
                    class="!cursor-pointer px-6 !text-yellow-600 dark:!text-yellow-400 !bg-yellow-50 hover:!bg-yellow-100 dark:!bg-yellow-950/20 dark:hover:!bg-yellow-900/30 !border-yellow-200/60 dark:!border-yellow-800/40 transition-all duration-200">
                    <span>Edit Jadwal</span>
                </flux:button>
            @endif

            <flux:button
                @click="
                    $store.sesi?.reset();
                    $store.sesi?.setEdit(0);
                    $store.sesi?.setColor('text-green-700 dark:text-green-400', 'file:bg-green-600 hover:file:bg-green-700 dark:file:bg-green-500 dark:hover:file:bg-green-600');
                    {{-- $wire.addUser('excel'); --}}
                    $flux.modal('nilai-excel-modal').show();
                "
                icon="printer" size="sm"
                class="!cursor-pointer px-6 !text-emerald-600 dark:!text-emerald-400 !bg-emerald-50 hover:!bg-emerald-100 dark:!bg-emerald-950/20 dark:hover:!bg-emerald-900/30 !border-emerald-200/60 dark:!border-emerald-800/40 transition-all duration-200">
                <span>Import Nilai</span>
            </flux:button>
            <div class="shrink-0">
                @include('livewire.global.table.export-button', [
                    'nameXString' => 'Export Nilai',
                    'xString' => 'exportNilaiExcel()',
                    'valuePx' => 6,
                    'isTextMd' => 1,
                    'isNoPb' => 1,
                ])
                <script>
                    document.addEventListener('livewire:init', () => {
                        Livewire.on('trigger-next-download', () => {
                            setTimeout(() => {
                                @this.call('downloadNextInQueue');
                            }, 200);
                        });
                    });
                </script>
            </div>
        @endif

    </div>
</div>
