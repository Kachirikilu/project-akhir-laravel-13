  <div class="px-4 pb-4" @click.stop>
      <button
          class="cursor-pointer flex w-full items-center justify-center gap-1.5 rounded-[11px] border-0 py-2.5 text-xs font-bold tracking-[0.02em] bg-transparent transition-all active:scale-[0.99]
                                {{ $isUjian
                                    ? 'btn-card-focus-state-special ring-1 ring-[var(--focus-color-special)]'
                                    : 'btn-card-focus-state text-[var(--focus-color)] ring-1 ring-[var(--focus-color)]' }}"
          @click="
                                if (!hasLoaded) { 
                                    $wire.loadData({{ $s->pertemuan_ke }}); 
                                    hasLoaded = true; 
                                }
                                expanded = !expanded;
                            ">
          <flux:icon name="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300"
              ::class="{ 'rotate-180': expanded }" />

          <span x-text="expanded ? 'Sembunyikan Detail' : 'Lihat Detail'"></span>
      </button>
  </div>
