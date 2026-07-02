<x-global.main-layout-table :paginator="$ref">

    <x-slot:header>
        <tr>
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'id',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])


            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'kode',
                'isMain' => 1,
                'isCenter' => 1,
                'rowSpan' => 2,
                'isSticky' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'citation',
                'rowSpan' => 2,
                'isBorderR' => 1,
            ])

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'judul',
                'rowSpan' => 2,
                'isBorderR' => 1,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'penulis',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'penerbit',
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'tahun',
                'isMain' => 1,
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'link',
                'rowSpan' => 2,
            ])

            <th rowspan="2" class="table-head border-x">Aksi</th>

            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'created_at',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])
            @include('livewire.global.table.head-table', [
                'sortFieldString' => 'updated_at',
                'isCenter' => 1,
                'rowSpan' => 2,
            ])

        </tr>
    </x-slot:header>


    @forelse($ref as $r)
        <tr wire:key="{{ $switchTable }}-{{ $r->id }}" data-{{ $switchTable }}-id="{{ $r->id }}"
            class="table-border hover:bg-[var(--hover-table-color)] active:bg-[var(--hover-table-color)]/90 transition-colors duration-200">

            <td class="table-second text-center">{{ $r->id }}</td>

            <td class="table-main-sticky text-center">
                <flux:dropdown>
                    <button class="cursor-pointer">

                        <flux:badge icon="book-open" color="orange" size="sm">{{ $r->kode ?? '---' }}
                        </flux:badge>
                    </button>
                    @include('livewire.staff.obe-management.ref-management.ref-toolbar-table', ['key' => 1])
                </flux:dropdown>
            </td>
            <td class="table-second table-border-r min-w-100">{{ $r->citation ?? '-' }}</td>
            <td class="table-second table-border-r min-w-84">{{ $r->judul ?? '-' }}</td>
            <td class="table-second min-w-48">{{ $r->penulis ?? '-' }}</td>
            <td class="table-sub min-w-48">{{ $r->penerbit ?? '-' }}</td>
            <td class="table-main text-center">{{ $r->tahun ?? '-' }}</td>
            <td class="table-second min-w-48">
                @if ($r->link)
                    <a href="{{ $r->link }}" target="_blank"
                        class="flex items-center gap-1 hover:underline active:underline text-xs font-bold text-blue-600 dark:text-blue-400">
                        <flux:icon.link variant="micro" /> <span>{{ $r->link ?? '-' }}</span>
                    </a>
                @else
                    -
                @endif
                </template>

            </td>

            <td class="table-main text-center">
                <flux:dropdown>
                    <flux:button class="cursor-pointer" variant="ghost"
                        size="sm" icon="ellipsis-horizontal" inset="top bottom">
                    </flux:button>
                    @include('livewire.staff.obe-management.ref-management.ref-toolbar-table', ['key' => 2])
                </flux:dropdown>
            </td>


            <td class="table-second whitespace-nowrap text-center">{{ $r->created_day ?? '-' }}</td>
            <td class="table-second whitespace-nowrap text-center">{{ $r->updated_day ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11" class="text-[var(--contrast-second-text)] px-6 py-4 text-center">
                Tidak ada data Referensi ditemukan!
            </td>
        </tr>
    @endforelse

    </x-admin.global.table.main-layout-table>
