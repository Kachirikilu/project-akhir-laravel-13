<div class="w-full min-w-full">
    {{-- ****************************************************** --}}
    {{-- 1. UPLOAD EXCEL FILE --}}
    {{-- ***********************F******************************* --}}
    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)]
            shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Upload File Excel Pengguna</h4>

        {{-- 📁 File Input --}}
        @include('livewire.global.modal-form.file-input-form', [
            'alpine' => 'user',
            'modelString' => 'excel_user_file',
            'wireKeyString' => 'excel-input-field',
            'nameXString' => 'Pilih File Excel Pengguna',
            'wireLoading' => 'parseExcelUserFile',
            'message' => $errors->first('excel_user_file'),
        ])
    </div>

    {{-- ****************************************************** --}}
    {{-- 2. TABEL INPUT HASIL PARSING --}}
    {{-- ****************************************************** --}}

    <div
        class="px-4 py-6 mt-4 bg-[var(--main-table-color)] border-[var(--border-table-color)] shadow-sm rounded-lg border space-y-4 transition-colors duration-300">

        @include('livewire.global.modal-form.search-input-form', [
            'alpine' => 'user',
            'xResults' => $prResults,
            'selectX' => 'selectPr',
            'modelString' => 'nama_pr',
        
            'idString' => 'pr_id',
            'itemsAllString' => 'pr_items',
        
            'resetXInput' => 'resetPrInput()',
            'typeXString' => 'prodi',
            'typeX2String' => 'departemen',
            'typeX3String' => 'fakultas',
        
            'nameXString' => 'Program Studi',
            'nameSearchString' => 'prNameSearch',
            'fetchString' => 'fetchPr',
            'iconString' => 'academic-cap',
            'wireLoading' => 'fetchPr',
        ])

        <h4
            class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
            Preview & Edit Data Pengguna
        </h4>

        <div class="relative">
            @if (empty($parsedUserRows))
                <div class="text-sm text-gray-500 italic" wire:loading.remove
                    wire:target="excel_user_file, parseExcelUserFile">
                    Data dari Excel akan tampil di sini setelah file diunggah.
                </div>
            @else
                <div class="w-full overflow-x-auto max-h-[55vh] overflow-y-auto border rounded-lg">

                    <table wire:loading.class="opacity-50"
                        wire:target="excel_user_file, parseExcelUserFile, removeParsedUserRow"
                        class="min-w-full border-collapse text-sm">
                        <thead class="sticky top-0 bg-gray-100 dark:bg-neutral-800 z-10">
                            <tr class="text-left">
                                <th class="px-3 py-2 border whitespace-nowrap text-center">#</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Email</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Password</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Nama</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NIP</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NITK</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NIDN</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NIDK</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NIM</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">NIK</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Kode Wly</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Thn Masuk</th>
                                {{-- <th class="px-3 py-2 border whitespace-nowrap text-center">Program Studi</th> --}}
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Role</th>
                                <th class="px-3 py-2 border whitespace-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>

                        @php
                            $kolomExcel =
                                'border bg-[var(--second-table-color)] border-[var(--border-table-color)] text-[var(--contrast-main-text)] placeholder-[var(--contrast-third-text)] px-2 py-1 border';
                        @endphp

                        <tbody class="bg-white dark:bg-neutral-800" wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="loadingUserExcel">
                            @foreach ($this->paginatedUserRows as $row)
                                @php
                                    $i = $row['_index'] ?? 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition-colors">
                                    <td class="{{ $kolomExcel }} text-center font-semibold">
                                        {{ $i + 1 }}
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="email" wire:model="parsedUserRows.{{ $i }}.email"
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none {{ isset($rowUserErrors[$i]['email']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['email']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['email'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.password"
                                            class="w-48 border rounded px-2 py-1 text-xs outline-none {{ isset($rowUserErrors[$i]['password']) ? 'border-red-500 bg-red-50' : '' }}"
                                            placeholder="Default / custom">
                                        @if (isset($rowUserErrors[$i]['password']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['password'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.name"
                                            class="w-56 border rounded px-2 py-1 text-xs outline-none {{ isset($rowUserErrors[$i]['name']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['name']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['name'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nip"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nip']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nip']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nip'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nitk"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nitk']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nitk']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nitk'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nidn"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nidn']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nidn']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nidn'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nidk"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nidk']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nidk']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nidk'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nim"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nim']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nim']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nim'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="{{ $kolomExcel }}">
                                        <input type="text" wire:model="parsedUserRows.{{ $i }}.nik"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="16"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                            class="w-40 border rounded px-2 py-1 text-xs {{ isset($rowUserErrors[$i]['nik']) ? 'border-red-500 bg-red-50' : '' }}">
                                        @if (isset($rowUserErrors[$i]['nik']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['nik'][0] }}
                                            </p>
                                        @endif
                                    </td>


                                    <td class="{{ $kolomExcel }}">
                                        <div class="relative">
                                            <select wire:model="parsedUserRows.{{ $i }}.kode_wilayah"
                                                class="w-24 border rounded pl-2 pr-4 py-1 text-xs cursor-pointer appearance-none transition-colors
                                            {{ isset($rowUserErrors[$i]['kode_wilayah']) ? 'border-red-500 bg-red-50' : 'bg-gray-50' }}
                                                text-gray-800 border-gray-300 focus:bg-white focus:ring-1 focus:ring-blue-500
                                                dark:bg-neutral-700 dark:text-gray-200 dark:border-neutral-600 dark:focus:bg-gray-600 dark:focus:ring-blue-400">
                                                <option value="IDL" class="dark:bg-neutral-800">IDL</option>
                                                <option value="PLG" class="dark:bg-neutral-800">PLG</option>
                                            </select>
                                            @if (isset($rowUserErrors[$i]['kode_wilayah']))
                                                <p class="text-red-500 text-[10px] mt-0.5">
                                                    {{ $rowUserErrors[$i]['kode_wilayah'][0] }}
                                                </p>
                                            @endif

                                            {{-- Ikon Panah Dropdown --}}
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-gray-400 dark:text-gray-500">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tahun Masuk: Input dikecilkan --}}
                                    <td class="{{ $kolomExcel }}">
                                        <input type="number"
                                            wire:model="parsedUserRows.{{ $i }}.angkatan"
                                            class="w-full border rounded px-1 py-1 text-xs text-center appearance-none {{ isset($rowUserErrors[$i]['angkatan']) ? 'border-red-500 bg-red-50' : '' }}"
                                            inputmode="numeric" pattern="[0-9]*" maxlength="4"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
                                            placeholder="YYYY">
                                        @if (isset($rowUserErrors[$i]['angkatan']))
                                            <p class="text-red-500 text-[10px] mt-0.5">
                                                {{ $rowUserErrors[$i]['angkatan'][0] }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- <td class="{{ $kolomExcel }}">
                                    <input type="text"
                                        wire:model.lazy="parsedUserRows.{{ $i }}.program_studi"
                                        class="w-40 border rounded px-2 py-1 text-xs">
                                </td> --}}

                                    {{-- Role: Diberi styling Select yang lebih jelas --}}
                                    <td class="{{ $kolomExcel }}">
                                        <div class="relative">
                                            <select wire:model="parsedUserRows.{{ $i }}.role"
                                                class="w-24 border rounded pl-2 pr-4 py-1 text-xs cursor-pointer appearance-none transition-colors
                                            {{ isset($rowUserErrors[$i]['role']) ? 'border-red-500 bg-red-50' : 'bg-gray-50' }}
                                text-gray-800 border-gray-300 focus:bg-white focus:ring-1 focus:ring-blue-500
                                dark:bg-neutral-700 dark:text-gray-200 dark:border-neutral-600 dark:focus:bg-gray-600 dark:focus:ring-blue-400">
                                                <option value="admin" class="dark:bg-neutral-800">Admin</option>
                                                <option value="dosen" class="dark:bg-neutral-800">Dosen</option>
                                                <option value="mahasiswa" class="dark:bg-neutral-800">Mahasiswa
                                                </option>
                                            </select>
                                            @if (isset($rowUserErrors[$i]['role']))
                                                <p class="text-red-500 text-[10px] mt-0.5">
                                                    {{ $rowUserErrors[$i]['role'][0] }}
                                                </p>
                                            @endif

                                            {{-- Ikon Panah Dropdown --}}
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-gray-400 dark:text-gray-500">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-2 py-1 border text-center">
                                        <button wire:click="removeParsedUserRow({{ $i }})" type="button"
                                            class="cursor-pointer text-red-500 hover:text-red-700 p-1 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-auto"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Error Baris --}}
                                @if (!empty($rowUserErrors[$i]))
                                    <tr>
                                        <td colspan="13"
                                            class="px-4 py-1 bg-red-50 text-red-600 text-[10px] border italic">
                                            ⚠️
                                            @foreach ($rowUserErrors[$i] as $fieldErrors)
                                                @foreach ($fieldErrors as $error)
                                                    {{ $error }}@if (!$loop->parent->last || !$loop->last)
                                                        |
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- 📄 Pagination Controls (Tailwind Style) --}}

                @if ($this->paginatedUserRows->hasPages())
                    <div
                        wire:target="gotoPage, previousPage, nextPage, {{ $this->paginatedUserRows->getPageName() }}">
                        {{ $this->paginatedUserRows->links('vendor.pagination.tailwind', [
                            'typeXLoading' => 'loadingUserExcel',
                        ]) }}
                    </div>
                @endif
            @endif

            @include('livewire.global.modal-form.loading-animation', [
                'wireLoading' => 'excel_user_file, parseExcelUserFile, removeParsedUserRow',
                'heightContainer' => 32,
                'textString' => 'Memproses data dari file Excel...',
            ])
        </div>

    </div>


</div>
