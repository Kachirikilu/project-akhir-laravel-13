<div
    class="{{ $mb ?? '' }} col-span-6 flex flex-col items-center justify-center py-7 px-6 rounded-2xl bg-[var(--main-table-trans)]/50 backdrop-blur-sm transition-all animate-in fade-in duration-500 border-2 border-dashed border-[var(--focus-color)]/30">
    <div class="bg-[var(--focus-color)] p-4 rounded-full mb-4">
        <x-heroicon-o-calendar-days class="w-8 h-8 text-white" />
    </div>
    <h3 class="text-lg font-semibold text-[var(--contrast-main-text)] mb-1">
        Jadwal Kosong Hari Ini
    </h3>
    <p class="text-sm text-[var(--contrast-second-text)] max-w-xs text-center">
        Sepertinya tidak ada sesi pertemuan kelas yang dijadwalkan untuk hari ini.
    </p>
</div>
