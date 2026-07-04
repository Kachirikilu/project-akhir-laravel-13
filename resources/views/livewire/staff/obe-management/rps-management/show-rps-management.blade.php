<div>
    <flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout x-on:close="$store.rps.pr_id_show = null"
        class="w-full md:w-[95vw] max-w-7xl h-[98vh] !p-8 scrollbar-large">

        @php
            // $r = $detailRPSData ?? [];
        @endphp

        <div class="flex flex-col xl:flex-row gap-5 my-9">

            {{-- KIRI: Tombol Export & Select Form (Lebar Lumayan) --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-2/3 order-2 xl:order-1">
                <div class="flex-shrink-0">
                    @include('livewire.global.table.export-button', [
                        'nameXString' => 'Export PDF',
                        'xString' => 'printPDFRPS($store.rps?.rps_id_show, $store.rps?.pr_id_show)',
                        'icon' => 'arrow-down-tray',
                        'isFull' => 1,
                        'valuePx' => 'px-8',
                        'valuePy' => 'py-4.5',
                        'color' => 'rose',
                        'wireLoading' => 'printPDFRPS()',
                    ])
                </div>

                <div wire:loading.class="opacity-50 pointer-events-none" wire:target="showRPS"
                    class="transition-opacity duration-200 flex-grow min-w-[250px]">
                    @php $prodisCollection = collect($prodisRPS); @endphp
                    @include('livewire.global.modal-form.select-form', [
                        'alpine' => 'rps',
                        'noLabel' => 1,
                        'modelString' => 'pr_id_show',
                        'isShowFrist' => 1,
                        'xOptions' => $prodisCollection->pluck('prodi')->toArray(),
                        'xValues' => $prodisCollection->pluck('id')->toArray(),
                        'xPilih' => $prodisCollection->pluck('kode')->toArray(),
                        'iconString' => 'academic-cap',
                        'placeholder' => 'Pilih Program Studi...',
                        'maxH' => 'max-h-180',
                    ])
                </div>
            </div>

            {{-- KANAN-ATAS: Badges --}}
            <div class="flex flex-wrap items-center justify-start xl:justify-end gap-2 w-full order-1 xl:order-2">
                <flux:badge x-show="$store.rps?.level_mk_show == 1" icon="academic-cap" color="emerald" size="lg"
                    class="px-4">
                    <span x-text="$store.rps?.kode_rps_show"></span>
                </flux:badge>
                <flux:badge x-show="$store.rps?.level_mk_show == 2" icon="book-open" color="amber" size="lg"
                    class="px-4">
                    <span x-text="$store.rps?.kode_rps_show"></span>
                </flux:badge>
                <flux:badge x-show="$store.rps?.level_mk_show == 3" icon="building-library" color="indigo"
                    size="lg" class="px-4">
                    <span x-text="$store.rps?.kode_rps_show"></span>
                </flux:badge>
                <flux:badge x-show="$store.rps?.level_mk_show == 4" icon="globe-alt" color="red" size="lg"
                    class="px-4">
                    <span x-text="$store.rps?.kode_rps_show"></span>
                </flux:badge>

                <flux:badge color="emerald" size="lg" class="px-4" x-text="$store.rps?.rps_show"></flux:badge>

                <flux:badge x-show="$store.rps?.draf_show == 0" color="green" size="lg" class="px-4"
                    icon="check-circle">Aktif</flux:badge>
                <flux:badge x-show="$store.rps?.draf_show == 1" color="red" size="lg" class="px-4"
                    icon="document-text">Draf</flux:badge>
            </div>
        </div>

        {{-- <div class="p-4 relative bg-white rounded-md border-2">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'showRPS', 'updateRPS'])
        @include('livewire.staff.obe-management.rps-management.rps-show.rps-pdf-show')
    </div> --}}

        {{-- @php
            $firstProdiId = $prodisCollection->first()->id ?? null;
        @endphp --}}

        <div class="p-4 relative bg-white rounded-md border-2" x-data="{
                    get rpsId() { return $store.rps?.rps_id_show; },
                    get prId() { return $store.rps?.pr_id_show; }
                }"
                {{-- get prId() { return $store.rps?.pr_id_show || '{{ $firstProdiId }}'; } --}}
                {{-- x-effect="
                if ($store.rps && (!$store.rps.pr_id_show || $store.rps.pr_id_show == null)) {
                    $store.rps.pr_id_show = '{{ $firstProdiId }}';
                } --}}
            ">
            <!-- Elemen Debug -->
            {{-- <div class="bg-yellow-100 p-2 text-xs text-black font-mono mb-2" x-show="true">
                DEBUG: pr_id_show = <span x-text="$store.rps?.pr_id_show ?? 'NULL'"></span> |
                frisrtProdiId Livewire {{ $firstProdiId }} |
                rps_id_show = <span x-text="$store.rps?.rps_id_show ?? 'NULL'"></span>
            </div> --}}

            <div class="flex justify-end mb-4">
                <button type="button" onclick="document.getElementById('pdf-frame').contentWindow.print()"
                    class="cursor-pointer text-sm sm:text-md flex items-center gap-2 bg-blue-600 text-white px-12 py-2 rounded shadow hover:bg-blue-700 active:bg-blue-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak PDF
                </button>
            </div>

            <div class="w-full h-[2000px] border" wire:ignore
                x-effect="
                const frame = document.getElementById('pdf-frame');
                if (rpsId && rpsId !== 'null' && rpsId !== 'undefined') {
                    let urlParts = ['{{ url('/rps/pdf-preview/') }}', rpsId];
                    if (prId && prId !== 'null' && prId !== 'undefined') {
                        urlParts.push(prId);
                    }
                    const newUrl = urlParts.join('/');
                    if (frame && frame.src !== newUrl) {
                        frame.src = newUrl;
                    }
                }
            ">
                <iframe id="pdf-frame" src="about:blank" class="w-full h-full border-none"></iframe>
            </div>
        </div>
    </flux:modal>
</div>
