<x-global.main-layout-card :paginator="$periodeNilai">

    <x-slot:sortir>
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'semester',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'nilai_index',
        ])
        @include('livewire.global.table.head-sortir', [
            'sortFieldString' => 'total_sks',
        ])
    </x-slot:sortir>

    <x-slot:search>
        <div class="w-full md:w-96 xl:w-108">
            @include('livewire.global.search-and-filters.main-search', [
                'placeholder' => 'Cari Nilai Semester...',
                'isLive' => 1,
                'isBorder' => 2,
            ])
        </div>
    </x-slot:search>

    @forelse($periodeNilai as $p)
        <div wire:key="periode-{{ $p->akademik }}-{{ $p->ganjil_genap }}"
            class="relative flex flex-col justify-between p-5 rounded-xl border border-[var(--border-table-color)] bg-[var(--main-table-trans)] shadow-sm hover:shadow-md transition-all duration-200">

            <div class="flex items-start justify-between gap-2 pb-3 border-b border-[var(--border-table-color)]/60">
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs font-bold text-[var(--focus-color)] uppercase tracking-wider">
                        Semester {{ $p->semester }}
                    </span>
                    <h3 class="font-bold text-sm text-[var(--contrast-main-text)] leading-snug">
                        {{ ucfirst($p->ganjil_genap) }} — {{ $p->akademik }}
                    </h3>
                </div>
                <span class="p-1.5 rounded-lg bg-[var(--focus-color)]/10 text-[var(--focus-color)]">
                    <flux:icon name="academic-cap" class="w-4 h-4" />
                </span>
            </div>
            <div class="flex-1 py-4 flex flex-col justify-center">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-[var(--contrast-main-text)] tracking-tight">
                        {{ number_format($p->ips, 2) }}
                    </span>
                    <span class="text-xs font-medium text-[var(--contrast-second-text)]">
                        IPS (IP Semester)
                    </span>
                </div>

                <p class="text-xs font-medium text-[var(--contrast-third-text)] mt-2 flex items-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Total Beban: <strong class="text-[var(--contrast-main-text)]">{{ $p->total_sks ?? 0 }} SKS</strong>
                </p>
            </div>
            <div
                class="pt-3 border-t border-[var(--border-table-color)]/40 -mx-5 -mb-5 p-4 bg-[var(--second-table-trans)] rounded-b-xl flex justify-end">
                <flux:button variant="filled" size="sm" icon-after="chevron-right" class="cursor-pointer"
                    href="{{ route('nilai-rps-mahasiswa', [
                        'ganjil_genap' => $p->ganjil_genap,
                        'akademik' => str_replace('/', '-', $p->akademik),
                    ]) }}"
                    wire:navigate>
                    Lihat Detail Nilai
                </flux:button>
            </div>

        </div>
    @empty
        {{-- Tampilan saat data kosong --}}
        <div
            class="col-span-full text-center p-12 rounded-xl border border-dashed border-[var(--border-table-color)] bg-[var(--main-table-trans)]">
            <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
            <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada riwayat indeks prestasi semester ditemukan!
            </p>
        </div>
    @endforelse

</x-global.main-layout-card>
