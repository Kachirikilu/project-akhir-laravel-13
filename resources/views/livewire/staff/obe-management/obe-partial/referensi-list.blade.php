<div class="space-y-4">
    @php
        // Mapping warna utuh agar terdeteksi oleh JIT Tailwind
        $theme = match ($colorLink) {
            'emerald' => [
                'bg' => 'bg-emerald-600',
                'shadow' => 'shadow-emerald-200',
                'icon' => 'text-emerald-300',
                'hover-border' => 'hover:border-emerald-300 dark:hover:border-emerald-800',
                'hover-bg' => 'hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10',
                'border-l' => 'border-l-emerald-600',
                'badge-bg' => 'bg-emerald-100 dark:bg-emerald-900/30',
                'badge-text' => 'text-emerald-700 dark:text-emerald-400',
                'link' => 'text-emerald-600 dark:text-emerald-400',
                'flux-badge' => 'emerald',
            ],
            'blue' => [
                'bg' => 'bg-blue-600',
                'shadow' => 'shadow-blue-200',
                'icon' => 'text-blue-300',
                'hover-border' => 'hover:border-blue-300 dark:hover:border-blue-800',
                'hover-bg' => 'hover:bg-blue-50/30 dark:hover:bg-blue-900/10',
                'border-l' => 'border-l-blue-600',
                'badge-bg' => 'bg-blue-100 dark:bg-blue-900/30',
                'badge-text' => 'text-blue-700 dark:text-blue-400',
                'link' => 'text-blue-600 dark:text-blue-400',
                'flux-badge' => 'blue',
            ],
            'red' => [
                'bg' => 'bg-red-600',
                'shadow' => 'shadow-red-200',
                'icon' => 'text-red-300',
                'hover-border' => 'hover:border-red-300 dark:hover:border-red-800',
                'hover-bg' => 'hover:bg-red-50/30 dark:hover:bg-red-900/10',
                'border-l' => 'border-l-red-600',
                'badge-bg' => 'bg-red-100 dark:bg-red-900/30',
                'badge-text' => 'text-red-700 dark:text-red-400',
                'link' => 'text-red-600 dark:text-red-400',
                'flux-badge' => 'red',
            ],
            default => [
                'bg' => 'bg-zinc-600',
                'shadow' => 'shadow-zinc-200',
                'icon' => 'text-zinc-300',
                'hover-border' => 'hover:border-zinc-300 dark:hover:border-zinc-800',
                'hover-bg' => 'hover:bg-zinc-50/30 dark:hover:bg-zinc-900/10',
                'border-l' => 'border-l-zinc-600',
                'badge-bg' => 'bg-zinc-100 dark:bg-zinc-900/30',
                'badge-text' => 'text-zinc-700 dark:text-zinc-400',
                'link' => 'text-zinc-600 dark:text-zinc-400',
                'flux-badge' => 'zinc',
            ],
        };
    @endphp
    {{-- HEADER --}}
    <div class="flex items-center gap-3">
        <div class="p-2 {{ $theme['bg'] }} rounded-lg shadow-sm {{ $theme['shadow'] }}">
            <flux:icon.book-open variant="solid" class="size-4 {{ $theme['icon'] }}" />
        </div>
        <div>
            <h3 class="font-bold text-zinc-900 dark:text-white leading-none text-sm">
                Referensi {{ $targetString ?? null }}
            </h3>
            <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1">{{ $textString }}</p>
        </div>
    </div>

    {{-- LIST AREA --}}
    <div
        class="border-2 border-dashed table-border rounded-xl p-3 bg-gray-50/30 dark:bg-neutral-800/30">

        {{-- SUBHEADER --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Daftar Referensi:</span>
            <template x-if="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}.length > 0">
                <span
                    class="text-xs px-3 py-1 bg-[var(--focus-color)] text-white rounded-full font-bold uppercase tracking-tighter"
                    x-text="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}.length + ' Referensi'"></span>
            </template>
        </div>

        {{-- LIST --}}
        <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1 scrollbar-tiny">

            <template x-if="$store.{{ $alpine ?? 'config' }}.{{ $modelString }}.length === 0">
                <div
                    class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl opacity-50">
                    <flux:icon.document-text class="size-8 mb-2" />
                    <p class="text-xs italic">Belum ada Referensi {{ $targetString ?? null }}</p>
                </div>
            </template>

            <template x-for="ref in $store.{{ $alpine ?? 'config' }}.{{ $modelString }}" :key="'ref-' + ref.id">
                <div x-data="{ expanded: false }"
                    class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm {{ $theme['hover-border'] }} transition-all">

                    <div x-on:click="expanded = !expanded"
                        class="p-3 cursor-pointer flex items-center gap-3 border-l-4 {{ $theme['hover-bg'] }} {{ $theme['border-l'] }}">
                        <span x-text="'ID' + ref.id"
                            class="text-xs font-bold px-1.5 py-0.5 rounded {{ $theme['badge-bg'] }} {{ $theme['badge-text'] }} flex-shrink-0"></span>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-bold truncate text-zinc-800 dark:text-zinc-200" x-text="ref.judul">
                            </p>
                            <p class="text-xs text-zinc-500 font-medium italic"
                                x-text="ref.penulis + ' (' + (ref.tahun || '-') + ')'"></p>
                        </div>
                        <flux:icon.chevron-down variant="micro"
                            class="text-zinc-400 transition-transform duration-200 flex-shrink-0"
                            x-bind:class="expanded ? 'rotate-180' : ''" />
                    </div>

                    <div x-show="expanded" x-collapse>
                        <div class="px-3 pb-3 pt-0 ml-1">
                            <div
                                class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-100 dark:border-zinc-700 space-y-2">
                                <p class="text-xs text-zinc-600 dark:text-zinc-400 italic leading-relaxed">
                                    <span class="font-bold {{ $theme['link'] }} not-italic">Sitasi:</span>
                                    <span
                                        x-text="ref.penulis + '. (' + ref.tahun + '). ' + ref.judul + '. ' + (ref.penerbit || '-')"></span>
                                </p>
                                <span class="flex items-center gap-1.5 text-xs {{ $theme['link'] }}">
                                    <flux:icon.building-library variant="micro" />
                                    <span x-text="ref.penerbit || '-'"></span>
                                </span>
                                <template x-if="ref.link">
                                    <a :href="ref.link" target="_blank"
                                        class="flex items-center gap-1 hover:underline text-xs font-bold {{ $theme['link'] }}">
                                        <flux:icon.link variant="micro" />
                                        <span x-text="ref.link"></span>
                                    </a>
                                </template>
                                <flux:badge color="{{ $theme['flux-badge'] }}" size="xs" x-text="ref.kode">
                                </flux:badge>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
