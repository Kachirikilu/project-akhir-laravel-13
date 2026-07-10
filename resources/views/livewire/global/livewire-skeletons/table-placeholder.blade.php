<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">

    {{-- toolbar --}}
    <div class="flex flex-wrap items-center gap-2 mt-2 mb-6 animate-pulse">
        <div class="h-8 w-48 sm:w-64 bg-gray-300 dark:bg-gray-700 rounded-md mr-4"></div>
        <div class="ml-auto">
            <div class="h-9 w-36 bg-gray-300 dark:bg-gray-700 rounded-lg"></div>
        </div>
    </div>
    {{-- toolbar --}}


    {{-- switch-table --}}
    <div>
        <div
            class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-2 p-4 rounded-lg shadow-md border animate-pulse">
            <div class="table-border border-b gap-4 flex items-end">
                <div class="min-w-0 flex-1">
                    <div class="flex space-x-4 pb-1 w-full">
                        @for ($i = 0; $i < 4; $i++)
                            <div class="px-2 py-2">
                                <div class="h-6 w-24 bg-gray-300 dark:bg-gray-700 rounded"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="shrink-0 mb-1">
                    <div class="h-9 w-24 bg-gray-300 dark:bg-gray-700 rounded-lg"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- switch-table --}}

    {{-- search-and-filters --}}
    <div
        class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] mb-6 p-4 rounded-lg shadow-md border animate-pulse">
        <div class="table-border flex items-end justify-between border-b mb-4 gap-4">
            <div class="min-w-0 flex-1 overflow-hidden">
                <div class="flex space-x-4 pb-1">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="px-2 py-2">
                            <div class="h-5 w-24 bg-gray-300 dark:bg-gray-700 rounded"></div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="shrink-0 flex items-center">
                <div class="h-8 w-16 bg-gray-300 dark:bg-gray-700 rounded-md"></div>
                <div class="h-4 w-10 bg-gray-300 dark:bg-gray-700 rounded ml-2"></div>
            </div>
        </div>
        <div class="grid grid-cols-1 grid-rows-1 relative isolate z-40 animate-pulse">
            <div class="col-start-1 row-start-1 w-full grid grid-cols-1 sm:grid-cols-7 gap-x-3 gap-y-2 items-center">
                <div class="sm:col-span-7 relative">
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <div class="h-4 w-4 bg-gray-400 dark:bg-gray-600 rounded-full"></div>
                        </div>
                        <div class="w-full h-10 bg-gray-300 dark:bg-gray-700 rounded-lg shadow-sm border table-border">
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-1 gap-1">
                            <div class="h-8 w-16 bg-gray-400 dark:bg-gray-600 rounded-md"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- search-and-filters --}}

<div
    class="bg-[var(--main-table-color)]/70 border-[var(--border-table-color)]/20 table-border text-[var(--contrast-main-text)] shadow-lg rounded-lg overflow-hidden animate-pulse">
    <div class="scrollbar-x-large overflow-auto max-h-[1000px]">
        <table class="min-w-full divide-y">
            <!-- Header Placeholder -->
            <thead class="bg-[var(--main-table-color)] table-border">
                <tr>
                    @for ($i = 0; $i < 5; $i++)
                        {{-- Sesuaikan jumlah kolom --}}
                        <th class="px-6 py-4">
                            <div class="h-12 w-20 bg-gray-300 dark:bg-gray-700 rounded"></div>
                        </th>
                    @endfor
                </tr>
            </thead>

            <!-- Body Placeholder (5 Baris) -->
            <tbody class="bg-[var(--second-table-color)] table-border divide-y">
                @for ($row = 0; $row < 5; $row++)
                    <tr>
                        @for ($col = 0; $col < 5; $col++)
                            <td class="px-6 py-4">
                                <div class="h-12 w-full bg-gray-300 dark:bg-gray-700 rounded"></div>
                            </td>
                        @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Footer Placeholder -->
    <div class="p-4 border-t table-border">
        <div class="h-8 w-full bg-gray-300 dark:bg-gray-700 rounded"></div>
    </div>
</div>


</div>
