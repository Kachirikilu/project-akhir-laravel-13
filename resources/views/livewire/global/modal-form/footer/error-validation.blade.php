@if (($show ?? true) && $errors->any())
    <div
        class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 rounded-xl shadow-sm transition-colors duration-300">
        <div class="flex items-center gap-2 mb-3">
            <flux:icon name="exclamation-triangle" variant="mini" class="text-red-700 dark:text-red-400" />
            <h4 class="font-bold text-red-700 dark:text-red-400 text-xs sm:text-sm  uppercase tracking-wider">
                Ada beberapa kesalahan:
            </h4>
        </div>

        <div class="space-y-2">
            @foreach ($errors->all() as $error)
                <div class="flex items-start gap-3">
                    <div class="mt-1.5 h-1.5 w-1.5 rounded-full bg-red-400 dark:bg-red-500 shrink-0"></div>
                    <p class="text-xs sm:text-sm text-sm text-red-600 dark:text-red-300 leading-relaxed">
                        {{ $error }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@endif
