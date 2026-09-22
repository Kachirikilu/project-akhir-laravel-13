<div wire:key="view-card-sesi-hari-ini">

    @php
        $showMore = $showMore ?? false;
        $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));
    @endphp

    <x-global.main-layout-card>

        @foreach ($sesis as $index => $s)
            @php
                $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                $isPastDate =
                    !empty($s->tanggal) &&
                    \Carbon\Carbon::parse($s->tanggal)->isPast() &&
                    !\Carbon\Carbon::parse($s->tanggal)->isToday();

                $kehadiran_mhs = Auth::user()->mahasiswa
                    ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                    : null;

                // 1. Ekstraksi Sufiks & Variabel Statis (Berdasarkan $isUjian saja)
                $suf = $isUjian ? '-special' : '';

                $borderTable = "border-[var(--border-table-color{$suf})]";
                $mainText = "text-[var(--contrast-main-text{$suf})]";
                $secondText = "text-[var(--contrast-second-text{$suf})]";
                $thirdText = "text-[var(--contrast-third-text{$suf})]";

                $focusColor = "bg-[var(--focus-color{$suf})]";
                $mainTable = "bg-[var(--main-table-color{$suf})]";
                $secondTable = "bg-[var(--second-table-color{$suf})]";
                $subTable = "bg-[var(--sub-table-color{$suf})]";

                $btnBase = 'transition-all duration-200 hover:z-10 active:z-10';

                if ($isUjian) {
                    if ($isPastDate) {
                        $focusDiv =
                            'dark:border-[var(--border-table-color-special)]/42 border-[var(--border-table-color-special)]';
                        $focusButton =
                            $btnBase .
                            ' hover:bg-[var(--focus-color-special)]/80 hover:text-[var(--main-text-special)] active:bg-[var(--focus-color-special)]/80 active:text-[var(--main-text-special)] dark:hover:bg-[var(--focus-color-special)]/24 dark:active:bg-[var(--focus-color-special)]/24 text-[var(--focus-color-special)] ring-[var(--focus-color)]/64';
                        $mainColor = 'dark:bg-[var(--main-color-special)]/24 bg-[var(--main-color-special)]/72';
                    } else {
                        $focusDiv =
                            'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/64';
                        $focusButton =
                            $btnBase .
                            ' hover:bg-[var(--focus-color-special)] hover:text-[var(--main-text-special)] active:bg-[var(--focus-color-special)] active:text-[var(--main-text-special)] text-[var(--focus-color-special)] ring-[var(--focus-color-special)]';
                        $mainColor = 'bg-[var(--main-color-special)]';
                    }
                } else {
                    if ($isPastDate) {
                        $focusDiv = 'dark:border-[var(--border-table-color)]/42 border-[var(--border-table-color)]';
                        $focusButton =
                            $btnBase .
                            ' hover:bg-[var(--focus-color)]/84 hover:text-[var(--main-text)] active:bg-[var(--focus-color)]/84 active:text-[var(--main-text)] dark:hover:bg-[var(--focus-color)]/24 dark:active:bg-[var(--focus-color)]/24 text-[var(--focus-color)] ring-[var(--focus-color)]/64';
                        $mainColor = 'dark:bg-[var(--main-color)]/24 bg-[var(--main-color)]/72';
                    } else {
                        $focusDiv = 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/64';
                        $focusButton =
                            $btnBase .
                            ' hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] active:bg-[var(--focus-color)] active:text-[var(--main-text)] text-[var(--focus-color)] ring-[var(--focus-color)]';
                        $mainColor = 'bg-[var(--main-color)]';
                    }
                }
            @endphp

            <div class="{{ $isUjian ? 'lg:col-span-2' : '' }}">
                <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{
                    expanded: false,
                    hasLoaded: false
                }"
                    @click="expanded = !expanded; hasLoaded = true"
                    class="{{ $focusDiv }} flex flex-col h-auto flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg">
                    {{-- ═══ HERO ═══ --}}
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-header')

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-main')

                        <div x-show="expanded" x-collapse.duration.300ms>
                            @if (isset($this->dosens_by_sesi[$s->pertemuan_ke]))
                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-expanded',
                                    [
                                        'allTimDosen' => $this->dosens_by_sesi[$s->pertemuan_ke],
                                    ]
                                )
                            @else
                                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-expanded-skeleton')
                            @endif
                        </div>
                    </div>

                    {{-- ═══ FOOTER: toggle hint ═══ --}}
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-table-card.sesi-card-partial.sesi-card-button')
                </div>
            </div>
        @endforeach

    </x-global.main-layout-card>
</div>
