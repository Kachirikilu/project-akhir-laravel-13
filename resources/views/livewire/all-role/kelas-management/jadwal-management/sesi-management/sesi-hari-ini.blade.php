@php
    $daftarUjian = array_merge(config('app.uts_fields'), config('app.uas_fields'));
@endphp

<div wire:key="sesi-wrapper-hari-ini" class="w-full">

    <x-global.main-layout-card>



        @foreach ($sesis as $index => $s)
            @php
                $isUjian = in_array(strtoupper($s->metode ?? ''), $daftarUjian);
                $kehadiran_mhs = Auth::user()->mahasiswa
                    ? $s->kehadirans->where('mahasiswa_id', Auth::user()->mahasiswa->id)->first()
                    : null;
            @endphp

            <div 
                class="{{ $isUjian ? 'lg:col-span-2' : '' }}">
                <div wire:key="kelas-sesi-card-{{ $s->id }}" x-data="{
                    expanded: false,
                        hasLoaded: false
                }"
                    @click="expanded = !expanded; hasLoaded = true"
                    class="flex flex-col h-full flex-shrink-0 rounded-[20px] overflow-hidden border transition-all duration-200 hover:shadow-lg active:shadow-lg cursor-pointer
                            {{ $isUjian ? 'ring-1 ring-[var(--focus-color-special)] border-[var(--border-table-color-special)] bg-[var(--main-table-trans-spceial)]/50' : 'border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50' }}">

                    @php
                        if ($isUjian) {
                            $mainColor = 'bg-[var(--main-color-special)]';
                            $bgBorder = 'border-[var(--border-table-color-special)] bg-[var(--second-table-color-special)]';
                            $mainText = 'text-[var(--contrast-main-text-special)]';
                            $secondText = 'text-[var(--contrast-second-text-special)]';
                            $thirdText = 'text-[var(--contrast-third-text-special)]';

                            $focusColor = 'bg-[var(--focus-color-special)]';
                            $mainTable = 'bg-[var(--main-table-color-special)]';
                            $secondTable = 'bg-[var(--second-table-color-special)]';
                            $subTable = 'bg-[var(--sub-table-color-special)]';
                        } else {
                            $mainColor = 'bg-[var(--main-color)]';
                            $bgBorder = 'border-[var(--border-table-color)] bg-[var(--second-table-color)]';
                            $mainText = 'text-[var(--contrast-main-text]';
                            $secondText = 'text-[var(--contrast-second-text)]';
                            $thirdText = 'text-[var(--contrast-third-text)]';

                            $focusColor = 'bg-[var(--focus-color)]';
                            $mainTable = 'bg-[var(--main-table-color)]';
                            $secondTable = 'bg-[var(--second-table-color)]';
                            $subTable = 'bg-[var(--sub-table-color)]';
                        }
                    @endphp
                    {{-- ═══ HERO ═══ --}}
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-header')

                    {{-- ═══ BODY ═══ --}}
                    <div class="flex flex-1 flex-col gap-2.5 p-4" @click.stop>
                        @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-main')

                        <div x-show="expanded" x-collapse.duration.300ms>
                            @if (isset($this->dosens_by_sesi[$s->pertemuan_ke]))
                                @include(
                                    'livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-expanded',
                                    [
                                        'allTimDosen' => $this->dosens_by_sesi[$s->pertemuan_ke],
                                    ]
                                )
                            @else
                                @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-expanded-skeleton')
                            @endif
                        </div>
                    </div>

                    {{-- ═══ FOOTER: toggle hint ═══ --}}
                    @include('livewire.all-role.kelas-management.jadwal-management.sesi-management.sesi-card.sesi-card-button')
                </div>
            </div>
        @endforeach


        {{-- Slot Footer Pagination --}}
        <x-slot:footer>
            @if (Auth::user()->admin)
                @include('livewire.global.table.trash-delete', ['mx' => ''])
            @endif
        </x-slot:footer>

    </x-global.main-layout-card>
</div>
