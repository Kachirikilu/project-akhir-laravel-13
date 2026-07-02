<x-global.main-layout-table :paginator="$users" :onlyAdmin="!Auth::user()->admin">

    <x-slot:sortir>
        <div x-data="{ activeTab: @entangle('filterAngkatan') }"
            class="pb-1 scrollbar-tiny flex items-center space-x-3 overflow-x-auto overflow-y-hidden w-full lg:w-auto">

            @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                'xString' => 'filterByAngkatan',
                'xFilter' => 'filterAngkatan',
                'tabFilter' => $totalSeluruhAngkatan ?? null,
                'tabString' => '',
                'tabNameString' => 'Semua',
                'icon' => 'users',
            ])
            @foreach ($angkatanFilter as $angkatan)
                @include('livewire.global.search-and-filters.partial.tab-filter-2', [
                    'xString' => 'filterByAngkatan',
                    'xFilter' => 'filterAngkatan',
                    'tabFilter' => $totalAngkatan[$angkatan] ?? 0,
                    'tabString' => $angkatan,
                    'tabNameString' => $angkatan,
                    'icon' => 'calendar-days',
                ])
            @endforeach
        </div>
    </x-slot:sortir>

    <x-slot:header>
        <tr>
            <th rowspan="2" class="table-head ">Role</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'headString' => 'NIM',
                'rowSpan' => 2,
                'isMain' => 1,
                'isCenter' => 1,
                'isSticky' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'name',
                'headString' => 'Nama',
                'rowSpan' => 2,
                'isMain' => 1,
            ])
            <th colspan="4" class="table-head-sub">
                Nilai Capaian
            </th>

            <th colspan="3" class="table-head-sub">
                Rencana Pembelajaran Semester
            </th>

            {{-- Angkatan - Autocomplete Input --}}
            @if ($filterAngkatan == '')
                @include('livewire.global.search-and-filters.table-search', [
                    'sortFieldString' => 'angkatan',
                    'modelString' => 'searchAngkatan',
                    'resetXFilter' => 'resetInputAngkatan()',
                    'wInput' => 20,
                    'numberOnly' => 1,
                    'maxLength' => 4,
                    'placeholder' => 'Tahun',
                    'rowSpan' => 2,
                    'isBorderR' => 1,
                ])
            @else
                @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'angkatan',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                    'isMain' => 1,
                ])
            @endif


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'status',
                'rowSpan' => 2,
                'isCenter' => 1,
            ])
              @include('livewire.global.table.head-table', [
                    'sortFieldString' => 'kampus',
                    'rowSpan' => 2,
                    'isCenter' => 1,
                ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'program_studi',
                'rowSpan' => 2,
            ])
            <th rowspan="2" class="table-head border-x">Aksi</th>
        </tr>

        <tr>
            <th class="table-head border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'rekap_mhs',
                'headString' => 'Nilai',
                'isCenter' => 1,
                'isBorderL' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'ipk_mhs',
                'headString' => 'IPK',
                'isCenter' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'mutu_mhs',
                'headString' => 'Mutu',
                'isCenter' => 1,
                'isMain' => 1,
            ])
            <th class="table-head border-x">Show</th>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'count_rps',
                'headString' => 'Total RPS',
                'isCenter' => 1,
                'isBorderL' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'total_sks',
                'isCenter' => 1,
                'isBorderR' => 1,
            ])


        </tr>
    </x-slot:header>


    @forelse($users as $user)
        <tr wire:key="mahasiswa-nilai-{{ $user->mahasiswa->id }}" data-mahasiswa-nilai-id="{{ $user->mahasiswa->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">
                <flux:dropdown>

                    <button class="cursor-pointer">
                        <flux:badge icon="book-open" color="cyan" size="sm">Mahasiswa</flux:badge>
                    </button>

                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])

                </flux:dropdown>
            </td>

            <td class="table-main-sticky whitespace-nowrap text-center">{{ $user->identity1 ?? '-' }}</td>


            <td class="table-second whitespace-nowrap">{{ $user->name ?? '-' }}</td>

            <td class="table-second table-border-x whitespace-nowrap">
                @if (!$user->trashed())
                    <x-button-action color="emerald"
                        href="{{ route('nilai-mahasiswa-management', [
                            'nim' => $user->mahasiswa->nim ?? null,
                        ]) }}"
                        wire:navigate>
                        <flux:icon name="document-text" class="w-3.5 h-3.5" />
                        Nilai
                    </x-button-action>
                @else
                    <code
                        class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                        unfound
                    </code>
                @endif
            </td>
            <td class="table-second table-border-l whitespace-nowrap text-center">
                {{ $user->mahasiswa->rekap_mhs ?? '0.00' }}</td>
            <td class="table-second whitespace-nowrap text-center">
                {{ $user->mahasiswa->ipk_mhs ?? '0.00' }}</td>
            <td class="table-sub table-border-l whitespace-nowrap text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.nilai-mutu-badge', [
                            'xValue' => $user->mahasiswa->mutu_mhs ?? 'E',
                        ])
                    </button>
                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>

            <td class="table-second table-border-x text-center">

                @if (!$user->trashed())
                    <x-button-action
                        @click="
                            $store.user?.reset();

                            const type = '{{ strtolower($user->role) }}';

                            $store.user?.setType(type);
                            $store.user?.setEdit(1);

                            const colors = {
                                admin: 'text-red-700 dark:text-red-400',
                                dosen: 'text-lime-700 dark:text-lime-400',
                                mahasiswa: 'text-cyan-700 dark:text-cyan-400',
                            };
                            const colors2 = {
                                admin: 'bg-red-50 dark:bg-red-950/40',
                                dosen: 'bg-lime-50 dark:bg-lime-950/40',
                                mahasiswa: 'bg-cyan-50 dark:bg-cyan-950/40',
                            };
                            $store.user?.setColor(colors[type] ?? 'text-gray-700 dark:text-gray-400', colors2[type] ?? 'bg-gray-50 dark:bg-gray-950/40');

                                $store.user?.setValueUserRPS (
                                    '{{ $user->name ?? '' }}',
                                    '{{ $user->dosen->nip ?? '' }}',
                                    '{{ $user->mahasiswa->nim ?? '' }}',
                                    '{{ $user->mahasiswa->angkatan ?? '' }}',
                                    '{{ $user->mahasiswa->count_rps ?? ($user->count_rps ?? 0) }}',
                                    '{{ $user->mahasiswa->total_sks ?? ($user->total_sks ?? 0) }}',

                                    '{{ $user->mahasiswa->rekap_mhs ?? '0.00' }}',
                                    '{{ $user->mahasiswa->ipk_mhs ?? '0.00' }}',
                                    '{{ $user->mahasiswa->mutu_mhs ?? 'E' }}',
                                    '{{ $user->pr_id ?? '' }}',
                                );
                    
                            $flux.modal('user-rps-modal').show();
                        "
                        wire:click="editUser({{ $user->id }}, {{ $withRPS ?? false }}, 1)" color="emerald"
                        wire:navigate>
                        <flux:icon name="eye" class="w-3.5 h-3.5" />
                        <span>RPS</span>
                    </x-button-action>
                @else
                    <code
                        class="font-mono text-xs bg-[var(--second-table-color)] px-1.5 py-0.5 rounded border table-border text-[var(--contrast-main-text)] italic">
                        unfound
                    </code>
                @endif

            </td>
            <td class="table-sub text-center">{{ $user->mahasiswa->count_rps ?? $user->count_rps }} RPS</td>
            <td class="table-sub table-border-r text-center">{{ $user->mahasiswa->total_sks ?? $user->total_sks }}
                SKS</td>


            <td class="table-second table-border-r text-center">{{ $user->mahasiswa->angkatan ?? 'YYYY' }}</td>

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer focus:outline-none">
                        @include('livewire.global.table.badge.kode-wilayah-badge', [
                            'xValue' => $user->wilayah,
                            'sortir' => $user->kode_wilayah,
                        ])
                    </button>

                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>

            <td class="table-second text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">
                        @include('livewire.global.table.badge.status-user-badge', [
                            'xValue' => $user->status,
                        ])
                    </button>
                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])

                </flux:dropdown>
            </td>

            <td class="table-second min-w-48">
                {{ $user->prodi ?? '-' }} ({{ $user->kode_pr ?? '---' }})</td>

            <td class="table-main text-center table-border-x">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost" size="sm" icon="ellipsis-horizontal"
                        inset="top bottom">
                    </flux:button>

                    @include('livewire.admin.user-management.user-toolbar-table', ['key' => 1])

                </flux:dropdown>
            </td>
        </tr>

    @empty
        <tr>
            <td colspan="15"
                class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Mahasiswa ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
