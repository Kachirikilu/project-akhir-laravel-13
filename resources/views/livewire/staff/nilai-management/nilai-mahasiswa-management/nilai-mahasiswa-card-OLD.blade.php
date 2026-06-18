   <x-global.main-layout-card :paginator="$periodes">

       <x-slot:sortir>
           @include('livewire.global.table.head-sortir', [
               'sortFieldString' => 'semester',
           ])
           @include('livewire.global.table.head-sortir', [
               'sortFieldString' => 'ip_semester',
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

       @forelse($periodes as $p)
           <div wire:key="nilai-periode-{{ Str::slug($p->akademik . '-' . $p->ganjil_genap) }}"
               data-nilai-periode-id="{{ $p->semester }}"
               class="flex flex-col rounded-[20px] overflow-hidden border border-[var(--border-table-color)] bg-[var(--main-table-trans)]/50 transition-all duration-200 hover:shadow-lg">

               {{-- ═══ HERO ═══ --}}
               <div class="flex flex-col gap-3 p-[18px] bg-[var(--main-color)]">
                   <div class="flex items-start justify-between gap-2">
                       <flux:dropdown>
                           <button
                               class="inline-flex items-center gap-1.5 rounded-lg border border-white/20 bg-white/10 px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.07em] text-white/75 transition-colors hover:bg-white/20 focus:outline-none cursor-pointer">
                               <flux:icon name="academic-cap" class="w-3 h-3" />
                               Semester {{ $p->semester ?? '-' }}
                           </button>
                       </flux:dropdown>
                   </div>

                   {{-- Nama Mata Kuliah --}}
                   <p class="text-[15px] font-bold leading-[1.35] tracking-[0.24em] text-[var(--main-text)]">
                       {{ $p->ganjil_genap }} - {{ $p->akademik }}
                   </p>

                   {{-- Sub info: nama kelas + prodi --}}
                   <div class="flex flex-wrap items-center gap-2">
                       <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                           <flux:icon name="users" class="w-3 h-3" />
                           {{ $nim ?? '-' }}
                       </span>
                       <span class="h-[3px] w-[3px] flex-shrink-0 rounded-full bg-[var(--main-text)]/30"></span>
                       <span class="inline-flex items-center gap-1 text-[11px] font-medium text-[var(--main-text)]/65">
                           <flux:icon name="academic-cap" class="w-3 h-3" />
                            {{ $mahasiswa->pr_rel->prodi }}
                       </span>
                   </div>
               </div>

               {{-- ═══ BODY ═══ --}}
               <div class="flex flex-1 flex-col gap-2.5 p-4">

                   {{-- Baris RPS --}}
                   <div
                       class="flex w-full items-center gap-1.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-4 py-2 text-left transition-colors focus:outline-none cursor-pointer">
                       <flux:icon name="document-text" class="w-3.5 h-3.5 text-[var(--contrast-third-text)]" />
                       <span
                           class="text-[10px] font-bold uppercase tracking-[0.06em] text-[var(--contrast-third-text)]">Total
                           SKS: {{ $p->total_sks }} SKS</span>
                       <span class="ml-auto text-xs font-semibold text-[var(--contrast-main-text)]">

                       </span>
                   </div>

                   {{-- Stat boxes --}}
                   <div class="grid grid-cols-3 gap-1.5">

                       <div
                           class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                           <span
                               class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">Nilai</span>
                           <span
                               class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $p->nilai_semester ?? '-' }}</span>
                       </div>

                       <div
                           class="py-3 flex flex-col items-center gap-0.5 rounded-[10px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] px-1.5 py-2 text-center">
                           <span
                               class="text-[9px] font-bold uppercase tracking-[0.07em] text-[var(--contrast-third-text)]">IP
                               Semester</span>
                           <span
                               class="text-base font-bold leading-none text-[var(--contrast-main-text)]">{{ $p->ip_semester ?? '-' }}</span>
                       </div>


                       @include(
                           'livewire.staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.nilai-mutu',
                           ['value' => $p->mutu_semester]
                       )

                   </div>
               </div>

               {{-- ═══ FOOTER ═══ --}}
               <div class="px-4 pb-4 flex items-center gap-1.5">
                   <button
                       class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-b-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent text-[var(--focus-color)] ring-1 ring-[var(--focus-color)] hover:z-10 hover:bg-[var(--focus-color)] hover:text-[var(--main-text)] transition-all active:scale-[0.99]"
                       href="{{ route('rps-mahasiswa-management', [
                           'nim' => $this->nim,
                           'ganjil_genap' => $p->ganjil_genap,
                           'akademik' => str_replace('/', '-', $p->akademik),
                       ]) }}"
                       wire:navigate>
                       <flux:icon name="eye" class="w-3.5 h-3.5" />
                       <span>Lihat Detail Nilai</span>
                   </button>

               </div>

           </div>
       @empty
           <div
               class="col-span-full text-center p-12 rounded-xl border border-dashed table-border bg-[var(--main-table-trans)]">
               <flux:icon name="information-circle" class="mx-auto h-8 w-8 text-[var(--contrast-second-text)] mb-2" />
               <p class="text-sm text-[var(--contrast-second-text)]">Tidak ada riwayat Indeks Prestasi Semester
                   ditemukan!
               </p>
           </div>
       @endforelse

   </x-global.main-layout-card>
