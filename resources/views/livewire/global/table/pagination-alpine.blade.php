<div x-show="totalFilteredItems > perPage"
    class="mt-4 mb-4 px-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

    {{-- INFO --}}
    <div>
        <p class="text-sm leading-5 text-[var(--contrast-second-text)]">
            Menampilkan
            <span class="font-bold text-[var(--contrast-main-text)]"
                x-text="Math.min(((currentPage - 1) * perPage) + 1, totalFilteredItems)">
            </span>
            sampai
            <span class="font-bold text-[var(--contrast-main-text)]"
                x-text="Math.min(currentPage * perPage, totalFilteredItems)">
            </span>
            dari
            <span class="font-bold text-[var(--contrast-main-text)]" x-text="totalFilteredItems">
            </span>
            hasil
        </p>
    </div>

    {{-- PAGINATION --}}
    <div>
        <span class="relative z-0 inline-flex overflow-hidden rounded-md shadow-sm">

            {{-- PREVIOUS --}}
            <button type="button" @click="if(currentPage > 1) currentPage--" :disabled="currentPage <= 1"
                class="inline-flex items-center px-2 py-2 text-sm font-medium leading-5 border transition ease-in-out duration-150
                                cursor-pointer bg-white border-gray-300 text-gray-500 
                                enabled:hover:text-gray-400
                                dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300
                                disabled:opacity-50 disabled:cursor-not-allowed
                                rounded-l-md">

                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>


            {{-- CURRENT PAGE --}}
            <span
                class="inline-flex items-center justify-center min-w-10 px-3 py-2 -ml-px
                                text-center text-sm font-bold text-white
                                border border-[var(--main-color)]
                                bg-[var(--main-color)]
                                leading-5">
                <span x-text="currentPage"></span>
            </span>

            {{-- TOTAL PAGE --}}
            <span
                class="inline-flex items-center justify-center min-w-10 px-3 py-2 -ml-px
                                text-center text-sm font-medium
                                border border-[var(--border-table-color)]
                                bg-[var(--main-table-trans)]
                                text-[var(--contrast-main-text)]
                                leading-5">
                / <span x-text="totalPages"></span>
            </span>

            {{-- NEXT --}}
            <button type="button" @click="if(currentPage < totalPages) currentPage++"
                :disabled="currentPage >= totalPages"
                class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium leading-5 border transition ease-in-out duration-150
                                cursor-pointer bg-white border-gray-300 text-gray-500 
                                enabled:hover:text-gray-400
                                dark:bg-neutral-800 dark:border-neutral-600 dark:text-gray-300
                                disabled:opacity-50 disabled:cursor-not-allowed
                                rounded-r-md">

                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </span>
    </div>
</div>
