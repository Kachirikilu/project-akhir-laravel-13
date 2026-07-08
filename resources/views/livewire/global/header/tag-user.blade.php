<div class="border-b-2 border-[var(--border-wadah-color)] pb-8 mt-3 mb-12 mx-4 sm:mx-6 md:mx-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Bagian Identitas -->
        <div class="flex-1 min-w-0">
            <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[var(--contrast-main-text)] truncate">
                {{ Auth::user()->name }}
            </h1>
            
            <p class="text-sm text-[var(--contrast-third-text)] mt-1 font-medium flex flex-wrap items-center gap-x-2">
                <span>{{ Auth::user()->label_id1 }}: {{ Auth::user()->identity1 }}</span>
                <span class="opacity-40">•</span>
                <span>{{ Auth::user()->prodi ?? 'Tidak Terdaftar' }} ({{ Auth::user()->kode_pr ?? 'XXX' }})</span>
            </p>
        </div>
        
        <!-- Badge Desktop (Muncul di kanan atas pada layar lebar) -->
        <div class="hidden md:block shrink-0">
            <div class="pr2 flex items-center gap-2 pl-3 pr-4 py-1.5 rounded-lg bg-[var(--main-pop-up-color)] border border-[var(--border-table-color)]">
                <flux:icon name="user-circle" class="w-4 h-4 text-[var(--focus-color)]" />
                <span class="text-xs font-semibold text-[var(--contrast-second-text)] uppercase">{{ Auth::user()->role }}</span>
            </div>
        </div>
    </div>

    <!-- Baris Nomor & Role (Samping-menyamping) -->
    <div class="mt-4 flex items-center gap-4 text-xs">
        @if (Auth::user()->no_wa_full)
            <div class="flex items-center gap-2 text-[var(--contrast-third-text)]">
                <flux:icon name="hashtag" class="w-4 h-4 text-[var(--focus-color)]" />
                <span class="font-mono">{{ Auth::user()->no_wa_full }}</span>
            </div>
        @endif
        
        <!-- Role khusus mobile (Muncul di samping nomor) -->
        <div class="md:hidden flex items-center gap-1.5 px-2 py-0.5 rounded bg-[var(--main-pop-up-color)] border border-[var(--border-table-color)]">
            <flux:icon name="user-circle" class="w-3 h-3 text-[var(--focus-color)]" />
            <span class="text-[10px] font-semibold text-[var(--contrast-second-text)] uppercase pr-1">{{ Auth::user()->role }}</span>
        </div>
    </div>
</div>