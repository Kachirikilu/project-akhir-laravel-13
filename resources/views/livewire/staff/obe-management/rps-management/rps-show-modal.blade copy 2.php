<flux:modal name="rps-detail-modal" wire:model="detailRPSModal" x-data flyout
    class="w-full md:w-[95vw] max-w-7xl h-[98vh] !p-8 scrollbar-large">

    @php
        $r = $detailRPSData ?? [];
    @endphp

    <div class="flex items-center justify-between my-9">

        {{-- KIRI: Badge nama & status --}}
        <div class="flex items-center gap-2">
            <flux:button @click="$wire.printPDFRPS($store.{{ $alpineKey ?? 'rps?.rps_id_show' }} ?? null)" icon="printer"
                size="sm"
                class="!cursor-pointer px-6 !text-rose-600 dark:!text-rose-400 !bg-rose-50 hover:!bg-rose-100 dark:!bg-rose-950/20 dark:hover:!bg-rose-900/30 !border-rose-200/60 dark:!border-rose-800/40 transition-all duration-200">
                <span>Export RPS</span>
                <flux:icon wire:loading wire:target="printPDFRPS" name="arrow-path"
                    class="animate-spin h-4 w-4 ml-3 dark:!text-rose-600" />
            </flux:button>

            {{-- @include('livewire.global.table.export-button', [
                'nameXString' => 'Export Excel Mahasiswa',
                'xClick' => "\$wire.exportExcel()",
                'xString' => 'exportExcel',
                'isFull' => 1,
                'color' => 'green',
            ]) --}}
            {{-- @include('livewire.global.table.export-button', [
                'nameXString' => 'Export RPS',
                'xString' => "printPDFRPS(\$store.{{ $alpineKey ?? 'rps?.rps_id_show' }} ?? null)",
                'valuePx' => 'px-6',
                'isFull' => 1,
                'isTextMd' => 1,
                'color' => 'rose',
            ]) --}}

            @if ($isEdit ?? true)
                @if (!$this->showRPSModal)
                    <flux:button
                        @click="
                    $store.rps?.setEdit(1);
                    $store.rps?.setFlyout(true);
                    $store.rps?.setColor('text-emerald-700 dark:text-emerald-400');
                    $store.rps?.setValueRPS(
                        '{{ $r['kode_blok'] ?? null }}',
                        '{{ $r['deskripsi'] ?? null }}',
                        '{{ $r['mk_id'] ?? null }}',
                        '{{ $r['kode_mk'] ?? null }}',
                        '{{ $r['nama_mk'] ?? null }}',
                        '{{ $r['akademik'] ?? null }}',
                        '{{ $r['is_draf'] ?? null }}',
                        '{{ $r['count_scpmk'] ?? null }}',
                        '{{ $r['bobot_uts'] ?? null }}',
                        '{{ $r['bobot_uas'] ?? null }}',
                        '{{ $r['total_bobot'] ?? null }}'
                    );
                    $flux.modal('rps-modal').show();
                    $wire.editRPS($store.rps?.id ?? null)
                "
                        wire:loading.attr="disabled" wire:target="showRPS, editRPS" icon="pencil-square" size="sm"
                        class="!cursor-pointer px-6 !text-yellow-600 dark:!text-yellow-400 !bg-yellow-50 hover:!bg-yellow-100 dark:!bg-yellow-950/20 dark:hover:!bg-yellow-900/30 !border-yellow-200/60 dark:!border-yellow-800/40 transition-all duration-200">
                        <span>Edit RPS</span>
                        <flux:icon wire:loading wire:target="showRPS, editRPS" name="arrow-path"
                            class="animate-spin h-4 w-4 ml-3 dark:!text-yellow-600" />
                    </flux:button>
                @endif
            @endif
        </div>

        {{-- KANAN: Tombol --}}
        <div wire:loading.class="opacity-0" wire:target="showRPS" class="flex items-center gap-2">

            @switch($r['level_mk'] ?? null)
                @case(1)
                    <flux:badge icon="academic-cap" color="emerald" size="lg" class="px-4">{{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @case(2)
                    <flux:badge icon="book-open" color="amber" size="lg" class="px-4">{{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @case(3)
                    <flux:badge icon="building-library" color="indigo" size="lg" class="px-4">
                        {{ $r['kode_rps'] ?? '-' }}
                    </flux:badge>
                @break

                @default
                    <flux:badge icon="globe-alt" color="red" size="lg" class="px-4">
                        {{ $r['kode_rps'] ?? 'YYYY-0X-XXXZZZZ' }}
                    </flux:badge>
            @endswitch
            <flux:badge color="emerald" size="lg" class="px-4">
                {{ $r['nama_rps'] ?? 'Rencana Pembelajaran Semester' }}
            </flux:badge>

            @if (($r['is_draf'] ?? 1) == 0)
                <flux:badge color="green" size="lg" class="px-4">Aktif</flux:badge>
            @else
                <flux:badge color="yellow" size="lg" class="px-4">Draf</flux:badge>
            @endif
        </div>


    </div>


    <div class="p-4 relative bg-white rounded-md border-2">
        @include('livewire.global.modal-form.loading-animation', ['wireLoading' => 'showRPS', 'updateRPS'])
        <div class="flex justify-end mb-4 no-print">
            {{-- <button onclick="window.print()" onclick="printModal()"
                class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak ke PDF
            </button> --}}
            <button onclick="printPDF()"
                class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                Cetak ke PDF
            </button>

<script>
    function printPDF() {
        // 1. Ambil konten dari elemen yang dimaksud
        const content = document.getElementById('printable-area').innerHTML;
        
        if (!content || content.trim() === "") {
            alert("Data PDF tidak ditemukan!");
            return;
        }

        // 2. Buat iframe secara dinamis
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        document.body.appendChild(iframe);

        // 3. Tulis konten ke iframe
        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
            <html>
                <head>
                    <script src="https://cdn.tailwindcss.com"><\/script>
                    <style>
                        body { font-family: "Times New Roman", Times, serif; color: black; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        .rps-table th, .rps-table td { border: 1px solid black !important; padding: 8px; }
                        @page { size: A4 portrait; margin: 1cm; }
                    </style>
                </head>
                <body>${content}</body>
            </html>
        `);
        doc.close();

        // 4. Beri waktu agar Tailwind & konten dimuat
        setTimeout(() => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            
            // Bersihkan setelah selesai
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        }, 800);
    }
</script>
            <style>
                /* CSS saat TIDAK print (Tampilan layar) */
                .printing-modal>*:not(#printable-area) {
                    display: none !important;
                }

                /* CSS SAAT PRINT */
                @media print {
                    @page {
                        size: A4 portrait;
                        margin: 1cm;
                    }

                    /* Sembunyikan semua elemen body */
                    body>* {
                        display: none !important;
                    }

                    /* Hanya tampilkan printable-area */
                    #printable-area,
                    #printable-area * {
                        display: block !important;
                        visibility: visible !important;
                    }

                    /* Reset posisi agar menempel di pojok kiri atas kertas */
                    #printable-area {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100% !important;
                        margin: 0 !important;
                        padding: 0 !important;
                        box-shadow: none !important;
                    }

                    /* Pastikan tabel tetap mengikuti aturan print */
                    table {
                        page-break-inside: auto;
                        width: 100% !important;
                    }

                    tr {
                        page-break-inside: avoid;
                    }

                    thead {
                        display: table-header-group;
                    }
                }
            </style>
<div id="printable-area" style="display: none;">
    @include('livewire.staff.obe-management.rps-management.rps-show.rps-pdf-show')
</div>
        </div>
</flux:modal>
