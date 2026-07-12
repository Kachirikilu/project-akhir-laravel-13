{{-- <div x-show="expanded" x-collapse class="flex flex-col gap-2"> --}}
<div class="flex flex-col gap-2">
    @php
        $pertemuan = $s->pertemuan_ke;
        $s->dosens_collection = $allTimDosen->filter(function ($dosen) use ($pertemuan) {
            return in_array($pertemuan, $dosen['pertemuan_ke']);
        });
    @endphp
    {{-- Sub-CPMK & Bobot detail --}}
    <div
        class="rounded-[10px] border {{ $bgBorder }} px-4 py-3 flex flex-col gap-2">
        <div class="flex items-center justify-between gap-2">
            <span
                class="text-[10px] font-bold uppercase tracking-[0.06em] {{ $thirdText }}">Sub-CPMK</span>
            <flux:dropdown>
                <button class="cursor-pointer focus:outline-none">
                    <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                        {{ $s->kode_scpmk ?? '---' }}</flux:badge>
                </button>
                @include(
                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-toolbar-table',
                    ['key' => 5]
                )
            </flux:dropdown>
        </div>
        <div class="flex items-center justify-between text-xs {{ $secondText }}">
            <span>Waktu Tugas</span>
            <span class="font-semibold {{ $mainText }}">{{ $s->w_tugas ?? 0 }}
                menit</span>
        </div>
        <div class="flex items-center justify-between text-xs {{ $secondText }}">
            <span>Mandiri</span>
            <span class="font-semibold {{ $mainText }}">{{ $s->w_mandiri ?? 0 }}
                menit</span>
        </div>
    </div>

    {{-- Deskripsi Tugas --}}
    <div class="rounded-[10px] border {{ $bgBorder }} px-4 py-3">
        <span
            class="text-[10px] font-bold uppercase tracking-[0.06em] {{ $thirdText }} block mb-1.5">Deskripsi
            Tugas / Evaluasi</span>
        <p class="text-xs leading-relaxed {{ $mainText }}">
            {{ $s->tugas ?? 'Tidak ada deskripsi tugas spesifik untuk sesi ini.' }}
        </p>
    </div>

    {{-- Tambahkan ini di view --}}

    <div class="rounded-[10px] border {{ $bgBorder }} px-4 py-3">
        <span class="text-[10px] font-bold uppercase tracking-[0.06em] {{ $thirdText }} block mb-1.5">
            Referensi
        </span>
        @php
            $referensiList = $s->referensi_sesi ?? collect();
        @endphp
        @forelse($referensiList as $refs)
            <div class="text-xs {{ $mainText }} flex items-start gap-2">
                <div class="{{ $referensiList->count() > 1 ? 'indent-[-15px] pl-[15px]' : '' }} mb-1">

                    @if ($referensiList->count() > 1)
                        <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                    @endif
                    <span>{{ $refs->citation }}</span>
                    @if ($refs->link)
                        <a href="{{ $refs->link }}" target="_blank"
                            class="inline-flex items-center ml-2 hover:opacity-70 transition-opacity {{ $theme['link'] ?? 'text-blue-600' }}">
                            <flux:icon.link variant="micro" />
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-xs text-zinc-400 italic">Tidak ada data Referensi</div>
        @endforelse
    </div>

    <div class="rounded-[10px] border {{ $bgBorder }} px-4 py-3">
        <span class="text-[10px] font-bold uppercase tracking-[0.06em] {{ $thirdText }} block mb-1.5">
            Dosen Pengajar
        </span>
        @php
            $hasSesiDosen = isset($s->dosens_collection) && $s->dosens_collection->isNotEmpty();
            $pengajar_collection = $hasSesiDosen ? $s->dosens_collection : $tim_dosen->flatMap->dosens;
            $pengajar_collection = collect($pengajar_collection)->map(function ($d) {
                return (object) [
                    'name' => $d->name ?? ($d['name'] ?? 'Tanpa Nama'),
                    'nip' => $d->nip ?? ($d['nip'] ?? '-'),
                    'is_ketua' => isset($d->pivot) ? (bool) $d->pivot->is_ketua : (bool) ($d['is_ketua'] ?? false),
                ];
            });
        @endphp

        @forelse($pengajar_collection as $dosen)
            <div class="text-xs {{ $mainText }} flex items-center gap-2">
                <div class="{{ $pengajar_collection->count() > 1 ? 'indent-[-15px] pl-[15px]' : '' }} mb-1">

                    @if ($pengajar_collection->count() > 1)
                        <span class="mr-[5px]">{{ $loop->iteration }}.</span>
                    @endif

                    {{ $dosen->name }}

                    @if ($dosen->is_ketua)
                        <span class="ml-2 px-1.5 py-0.5 text-[9px] font-semibold bg-blue-100 text-blue-700 rounded">
                            KETUA
                        </span>
                    @endif

                    <br>NIP: {{ $dosen->nip }}
                </div>
            </div>
        @empty
            <div class="text-xs text-zinc-400 italic">Tidak ada data Dosen</div>
        @endforelse
    </div>

    {{-- Tombol Absensi (Mahasiswa) --}}
    @if (Auth::user()->mahasiswa)
        <div x-data="{
            sekarang: '',
            mulai: '{{ $s->waktu_pelaksanaan }}',
            dispensasi: '{{ $s->waktu_dispensasi }}',
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
                <x-button-action color="blue" size="sm" class="w-full justify-center"
                    @click="
                                                $store.sesi?.setEdit(0);
                                                $store.sesi?.setColor('text-blue-700 dark:text-blue-400');
                                                $flux.modal('absensi-sesi-modal').show();
                                                $store.sesi?.setValueAbsenSesi(
                                                    '{{ $s->id ?? '' }}', '{{ $jadwal->kode }}', '{{ $s->pertemuan_ke ?? '' }}', '{{ $s->kode_scpmk }}',
                                                    '{{ $kehadiran_mhs->keterangan ?? null }}',
                                                    '{{ $s->waktu_pelaksanaan ?? '' }}', '{{ $s->waktu_berakhir ?? '' }}',
                                                    '{{ $s->waktu_telat ?? '' }}', '{{ $s->waktu_dispensasi ?? '' }}'
                                                );
                                                $flux.modal('absensi-sesi-modal').show();
                                                $dispatch('open-absensi-sesi-modal');
                                            ">
                    <flux:icon name="user-plus" class="w-3.5 h-3.5" />
                    <span>Absensi</span>
                </x-button-action>
            </template>
        </div>

        {{-- Status Absen Mahasiswa --}}
        @if ($kehadiran_mhs)
            @php
                $badgeColor = match ($kehadiran_mhs->status) {
                    'Hadir' => 'green',
                    'Terlambat' => 'amber',
                    'Dispensasi' => 'blue',
                    'Sakit', 'Izin' => 'indigo',
                    default => 'red',
                };
            @endphp
            <div
                class="rounded-[10px] border {{ $bgBorder }} px-2.5 py-2.5 flex items-center justify-between gap-2">
                <flux:badge color="{{ $badgeColor }}" size="sm" inset-top-bottom>
                    {{ $kehadiran_mhs->status }}</flux:badge>
                <span class="text-xs {{ $secondText }} flex items-center gap-1">
                    <flux:icon name="clock" class="w-3.5 h-3.5" />
                    {{ $kehadiran_mhs->waktu_presensi?->format('H:i') }} WIB
                </span>
            </div>
            @if ($kehadiran_mhs->keterangan)
                <div
                    class="rounded-[10px] border {{ $bgBorder }} px-2.5 py-2">
                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.07em] {{ $thirdText }} block mb-1">Keterangan</span>
                    <p class="text-xs italic {{ $mainText }}">
                        {{ $kehadiran_mhs->keterangan }}</p>
                </div>
            @endif
        @else
            <flux:badge color="zinc" size="sm" class="font-mono opacity-70 px-4">Belum
                Presensi
            </flux:badge>
        @endif
    @endif
</div>
