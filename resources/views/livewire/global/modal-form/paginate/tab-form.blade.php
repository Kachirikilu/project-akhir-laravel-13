<div class="bg-[var(--main-table-color)] border table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md">
    <div class="border-b table-border flex flex-col-reverse">

        {{-- 🌟 Perbaikan pada baris ini: Ditambahkan w-full dan class tiny-scrollbar yang benar --}}
        <div class="w-full flex space-x-4 overflow-x-auto pb-1 scrollbar-tiny">

            @foreach ($tabs as $i => $label)
                <button type="button" @click="step = {{ $i }}"
                    class="text-xs sm:text-sm relative cursor-pointer px-2 py-2 font-medium rounded-t-lg transition duration-200 whitespace-nowrap group focus:outline-none hover:text-[var(--focus-color)] active:text-[var(--focus-color)]/90"
                    :class="step === {{ $i }} ? 'text-[var(--focus-color)]' : 'text-[var(--contrast-second-text)]'">
                    <div class="flex items-center gap-1">
                        <span>{{ $label }}</span>
                        @if ($errorsCount[$i] > 0)
                            <span class="ml-1 inline-flex items-center justify-center font-bold text-white bg-red-500 rounded-full min-w-[18px] h-[18px] px-1">
                                {{ $errorsCount[$i] }}
                            </span>
                        @endif
                    </div>
                    <span x-cloak
                        class="absolute bottom-0 left-0 w-full h-[2px] transform origin-left transition-all duration-300 ease-in-out bg-[var(--focus-color)]"
                        :class="step === {{ $i }} ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100 group-active:scale-x-100'"></span>

                </button>

            @endforeach

        </div>

    </div>
</div>