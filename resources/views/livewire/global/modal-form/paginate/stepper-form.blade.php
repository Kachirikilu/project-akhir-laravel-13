<div
    class="mt-4 pt-4 bg-[var(--main-table-color)]
           border table-border
           text-[var(--contrast-main-text)]
           mb-2 p-4 rounded-lg shadow-md">
    <div class="border-b table-border
                flex items-center justify-between">

        {{-- 🔹 BACK --}}
        <button type="button" @click="step = Math.max(1, step - 1)" :disabled="step === 1"
            class="relative px-4 py-2 text-sm font-medium
                    flex items-center gap-2 transition group"
            x-bind:class="step === 1 ?
                'opacity-50 cursor-not-allowed text-[var(--contrast-second-text)] hover:text-[var(--focus-color)]' :
                'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)] cursor-pointer'">
            <span>←</span>
            <span>Back</span>

            {{-- 🔥 UNDERLINE --}}
            <span
                class="absolute bottom-0 left-0 w-full h-[2px]
                        transform origin-left transition-all duration-300"
                :class="step === 1 ?
                    'scale-x-0 bg-gray-300' :
                    'scale-x-0 group-hover:scale-x-100 bg-[var(--focus-color)]'"></span>
        </button>

        {{-- 🔹 INDICATOR --}}
        <div class="cursor-pointer text-xs text-[var(--contrast-second-text)] hover:text-[var(--focus-color)]">
            Step <span x-text="step"></span> / {{ $maxStep }}
        </div>

        {{-- 🔹 NEXT --}}
        <button type="button" @click="step = Math.min({{ $maxStep }}, step + 1)" :disabled="step === {{ $maxStep }}"
            class="relative px-4 py-2 text-sm font-medium
                    flex items-center gap-2 transition group"
            x-bind:class="step === {{ $maxStep }} ?
                'opacity-50 cursor-not-allowed text-[var(--contrast-second-text)] hover:text-[var(--focus-color)]' :
                'text-[var(--contrast-second-text)] hover:text-[var(--focus-color)] cursor-pointer'">
            <span>Next</span>
            <span>→</span>

            {{-- 🔥 UNDERLINE --}}
            <span
                class="absolute bottom-0 left-0 w-full h-[2px]
                        transform origin-left transition-all duration-300"
                :class="step === {{ $maxStep }} ?
                    'scale-x-0 bg-gray-300' :
                    'scale-x-0 group-hover:scale-x-100 bg-[var(--focus-color)]'">
            </span>
        </button>

    </div>
</div>
