<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] table-border
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

    <div class="flex justify-between items-center border-b border-[var(--contrast-second-text)] pb-2 mb-6">

        <h4 class="text-[var(--contrast-main-text)] text-lg font-medium">
            Histori Nilai Mahasiswa</h4>
    </div>

    <div class="relative">


        @include('livewire.global.modal-form.loading-animation', [
            'wireLoading' => 'editNilai, updateNilai',
        ])

        <div class="space-y-4">


            <div class="space-y-4" x-data="{
                // start dan end diambil dari step modal (Livewire info)
                start: {{ $indexStart ?? 0 }},
                end: {{ $indexEnd ?? 16 }},
            
                // Fungsi cek apakah index pertemuan ini harus muncul di step sekarang
                isShown(index) {
                    // index - 1 karena start & end berbasis index array JavaScript (0-15)
                    return (index - 1) >= this.start && (index - 1) < this.end;
                },
            
                // Fungsi untuk mengambil data detail pertemuan dari store berdasarkan index (1-16)
                getMeetData(index) {
                    return {
                        cpmk: $store.nilai['cpmk_' + index] || '---',
                        scpmk: $store.nilai['scpmk_' + index] || '---',
                        metode: $store.nilai['metode_' + index] || ''
                    };
                }
            }">
                @for ($targetIndex = 1; $targetIndex <= 16; $targetIndex++)
                    @php
                        // Sesuaikan index array PHP untuk keperluan display message error bawaan Livewire jika ada
                        $indexphp = $targetIndex - 1;
                    @endphp

                    <div x-show="isShown({{ $targetIndex }})" x-data="{ item: getMeetData({{ $targetIndex }}) }" {{-- Re-evaluate data item jika store nilai berubah --}}
                        x-effect="item = getMeetData({{ $targetIndex }})"
                        class="p-3 border border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 flex flex-col mb-4">

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[var(--focus-color)]">
                                Pertemuan Ke-{{ $targetIndex }}
                            </span>
                            <span class="flex flex-wrap gap-2 sm:gap-3">
                                <flux:badge icon="academic-cap" color="fuchsia" size="sm">
                                    <span x-text="item.scpmk"></span>
                                </flux:badge>
                                <flux:badge icon="academic-cap" color="sky" size="sm">
                                    <span x-text="item.cpmk"></span>
                                </flux:badge>
                                <template x-if="item.metode">
                                    <div class="flex items-center">
                                        <div x-show="item.metode === 'Teori'">
                                            <flux:badge icon="book-open" color="emerald" size="sm"
                                                variant="{{ $variant ?? 'pill' }}">Teori</flux:badge>
                                        </div>

                                        <div x-show="['Praktik', 'Responsi'].includes(item.metode)">
                                            <flux:badge icon="beaker" color="cyan" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>

                                        <div x-show="['Tugas', 'Logbook'].includes(item.metode)">
                                            <flux:badge icon="pencil-square" color="blue" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>

                                        <div x-show="['UTS', 'Evaluasi Awal', 'Kuis'].includes(item.metode)">
                                            <flux:badge icon="clipboard-document-check" color="amber" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>

                                        <div x-show="['UAS', 'Evaluasi Akhir', 'Laporan Akhir'].includes(item.metode)">
                                            <flux:badge icon="document-check" color="orange" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>

                                        <div
                                            x-show="['Hasil Proyek', 'Hasil Projek', 'Portofolio'].includes(item.metode)">
                                            <flux:badge icon="light-bulb" color="indigo" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>

                                        <div x-show="item.metode === 'Kerja Praktek'">
                                            <flux:badge icon="briefcase" color="violet" size="sm"
                                                variant="{{ $variant ?? 'pill' }}">Kerja Praktek</flux:badge>
                                        </div>

                                        <div x-show="item.metode === 'Skripsi'">
                                            <flux:badge icon="academic-cap" color="fuchsia" size="sm"
                                                variant="{{ $variant ?? 'pill' }}">Skripsi</flux:badge>
                                        </div>

                                        <div x-show="item.metode === 'Aktivitas Partisipasif'">
                                            <flux:badge icon="user-group" color="rose" size="sm"
                                                variant="{{ $variant ?? 'pill' }}">Aktivitas Partisipasif</flux:badge>
                                        </div>

                                        <div x-show="item.metode === 'Mandiri'">
                                            <flux:badge icon="user" color="slate" size="sm"
                                                variant="{{ $variant ?? 'pill' }}">Mandiri</flux:badge>
                                        </div>

                                        <div
                                            x-show="![
                                                'Teori', 'Praktik', 'Responsi', 'Tugas', 'Logbook', 'UTS', 'Evaluasi Awal', 'Kuis', 
                                                'UAS', 'Evaluasi Akhir', 'Laporan Akhir', 'Hasil Proyek', 'Hasil Projek', 'Portofolio', 
                                                'Kerja Praktek', 'Skripsi', 'Aktivitas Partisipasif', 'Mandiri'
                                            ].includes(item.metode)">
                                            <flux:badge icon="information-circle" color="zinc" size="sm"
                                                variant="{{ $variant ?? 'pill' }}" x-text="item.metode"></flux:badge>
                                        </div>
                                    </div>
                                </template>
                            </span>
                        </div>

                        <div class="grid sm:grid-cols-4 space-y-4 sm:space-y-0 gap-4 mt-3">
                            <div class="sm:col-span-4 w-full">
                                <div class="grid grid-cols-4 gap-3">

                                    <div class="col-span-3 sm:col-span-3">
                                        @include('livewire.global.modal-form.input-form', [
                                            'alpine' => 'nilai',
                                            'isLivewire' => 0,
                                            'modelString' => 'nilai_' . $targetIndex,
                                            'nameXString' => 'Nilai',
                                            // 'floatOnly' => 1,
                                            'maxValue' => 100,
                                            'readonly' => (Auth::user()->admin || Auth::user()->dosen) ? 0 : 1,
                                            'iconString' => 'chart-bar',
                                            'placeholder' => 'Masukkan Nilai...',
                                            'isRequired' => 0,
                                            'message' => $errors->first("nilai_$indexphp"),
                                        ])
                                    </div>

                                    <div class="col-span-1 sm:col-span-1">
                                        @include('livewire.global.modal-form.input-form', [
                                            'alpine' => 'nilai',
                                            'isLivewire' => 0,
                                            'modelString' => 'bobot_persen_' . $targetIndex,
                                            'readonly' => 1,
                                            'nameXString' => 'Bobot',
                                            'iconString' => 'scale',
                                            'placeholder' => 'Bobot...',
                                            'isRequired' => 0,
                                            'message' => $errors->first("bobot_$indexphp"),
                                        ])
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>



        </div>

    </div>


</div>
