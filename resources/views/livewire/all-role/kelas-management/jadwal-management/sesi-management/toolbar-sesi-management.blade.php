<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['kode'],
        'typeXString' => 'Kode Sesi',
    ])


    @if (Auth::user()->mahasiswa)
        {{-- 1. Tombol Absensi dengan Pengecekan Waktu --}}
        @if (!$data['mhs_status'])
        <flux:menu.separator />
        <div x-data="{
            sekarang: '',
            mulai: '{{ $data['waktu_pelaksanaan'] }}',
            dispensasi: '{{ $data['waktu_dispensasi'] }}',
            getWaktuLokal() {
                let d = new Date();
                let tzOffset = d.getTimezoneOffset() * 60000;
                return new Date(d.getTime() - tzOffset).toISOString().slice(0, 16);
            },
            init() {
                this.sekarang = this.getWaktuLokal();
                setInterval(() => { this.sekarang = this.getWaktuLokal(); }, 10000);
            }
        }">

            <template x-if="sekarang >= mulai && sekarang <= dispensasi">

                <flux:menu.item
                    @click="
                        $store.sesi?.reset();
                        $store.sesi?.setEdit(1);
                        $store.sesi?.setColor('text-blue-700 dark:text-blue-400');
                            $store.sesi?.setValueAbsenSesi(
                                '{{ $data['id'] ?? '' }}', '{{ $data['kode_jadwal'] }}', '{{ $data['pertemuan_ke'] ?? '' }}',
                                '{{ $data['kode_scpmk'] }}',
                                '{{ $data['keterangan'] ?? null }}',
                                '{{ $data['waktu_pelaksanaan'] ?? '' }}', '{{ $data['waktu_berakhir'] ?? '' }}',
                                '{{ $data['waktu_telat'] ?? '' }}', '{{ $data['waktu_dispensasi'] ?? '' }}'
                            );
                        $flux.modal('absensi-sesi-modal').show();
                        $dispatch('open-absensi-sesi-modal', { id: {{ $data['id'] ?? 0 }}, sks: {{ Js::from($data['sks'] ?? '') }} });
                    "
                    class="!cursor-pointer !text-blue-600 dark:!text-blue-400 hover:!bg-blue-100 dark:hover:!bg-blue-900/30 active:!bg-blue-200 dark:active:!bg-blue-900 transition-colors">
                    <flux:icon name="user-plus" class="mr-2 h-4 w-4" />
                    <div class="flex justify-between items-center w-full">
                        <span>Absensi</span>
                    </div>
                </flux:menu.item>
            </template>
        </div>
        @endif
        {{-- 2. Status Absensi (Jika sudah ada data) --}}
        @if ($data['mhs_status'])
            <flux:menu.separator />
            <div class="px-2 py-2 text-xs">
                @php
                    $badgeColor = match ($data['mhs_status']) {
                        'Hadir' => 'green',
                        'Terlambat' => 'amber',
                        'Dispensasi' => 'blue',
                        'Sakit', 'Izin' => 'indigo',
                        default => 'red',
                    };
                @endphp

                <div class="flex items-center gap-2">
                    <flux:badge color="{{ $badgeColor }}" size="sm">{{ $data['mhs_status'] }}</flux:badge>
                    <span class="text-[var(--contrast-second-text)] flex items-center gap-1">
                        <flux:icon name="clock" class="w-3.5 h-3.5" />
                        {{ $data['mhs_waktu_presensi'] }} WIB
                    </span>
                </div>

                @if ($data['mhs_keterangan'])
                    <span
                        class="mt-3 text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)] block mb-1">Keterangan</span>
                    <p class="text-xs italic text-[var(--contrast-main-text)]">
                        {{ $data['mhs_keterangan'] }}</p>
                @endif
            </div>
        @else
            {{-- Opsional: Indikator jika belum absen dan waktu sudah lewat --}}
            {{-- <flux:menu.separator /> --}}
            {{-- <flux:menu.separator /> --}}

            <div class="px-2 py-2">
                <flux:badge color="zinc" size="sm" class="opacity-70">Belum Presensi</flux:badge>
            </div>
        @endif
    @elseif ($data['canAccess'])
        @if (!$data['isTrashed'])
            <flux:menu.separator />
            {{-- Tombol Edit --}}
            <flux:menu.item
                @click="
                    $store.sesi?.reset();
                    $store.sesi?.setEdit(1);
                    $store.sesi?.setColor('text-amber-700 dark:text-amber-400');

                    $store.sesi?.setValueSesi(
                        '{{ $data['jam_mulai'] ?? '' }}',
                        '{{ $data['jam_berakhir'] ?? '' }}',

                        '{{ $data['pertemuan_ke'] ?? '' }}',
                        '{{ $data['tanggal_fix'] ?? '' }}',

                        '{{ $data['sent'] ?? '' }}',
                    );
                    $flux.modal('sesi-modal').show();
                    $dispatch('open-edit-sesi-modal', { id: {{ $data['id'] }}, sks: '{{ $data['sks'] }}' });
                "
                class="!cursor-pointer !text-yellow-600 dark:!text-yellow-400 hover:!bg-yellow-100 dark:hover:!bg-yellow-900/30 active:!bg-yellow-200 dark:active:!bg-yellow-900 transition-colors">
                <flux:icon name="pencil-square" class="mr-2 h-4 w-4" />

                <div class="flex justify-between items-center w-full">
                    <span>Edit Sesi</span>
                </div>
            </flux:menu.item>
        @endif
    @endif
</div>
