{{-- ****************************************************** --}}
{{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
{{-- ****************************************************** --}}
<div
    class="px-4 py-6 mt-4 
    bg-[var(--main-table-color)] border-[var(--border-table-color)]
    shadow-sm rounded-lg border space-y-4 transition-colors duration-300">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Account Information</h4>

    {{-- 📧 Email Input --}}
    @include('livewire.global.modal-form.input-form', [
        // 'colorIcon' => $colorIcon,
        'alpine' => 'user',
        'modelString' => 'email',
        'typeString' => 'email',
        'iconString' => 'envelope',
        'placeholder' => 'contoh@domain.com',
        'message' => $errors->first('email'),
    ])

    {{-- 🔒 Password Input --}}
    <template x-if="$store.user?.isEdit == 0" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'alpine' => 'user',
            'modelString' => 'password',
            'typeString' => 'password',
            'iconString' => 'lock-closed',
            'placeholder' => 'Masukkan Password',
            'message' => $errors->first('password'),
        ])
    </template>
    <template x-if="$store.user?.isEdit == 1" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            // 'colorIcon' => $colorIcon,
            'alpine' => 'user',
            'modelString' => 'password',
            'typeString' => 'password',
            'iconString' => 'lock-closed',
            'placeholder' => 'Kosongkan jika tidak ingin diubah...',
            'message' => $errors->first('password'),
            'isRequired' => 0,
        ])
    </template>
</div>
