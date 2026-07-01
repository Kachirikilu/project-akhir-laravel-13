{{-- ****************************************************** --}}
{{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Informasi Akun</h4>

    {{-- 📧 Email Input --}}
    @include('livewire.global.modal-form.input-form', [
        'alpine' => 'user',
        // 'isLivewire' => 1,
        'modelString' => 'email',
        'typeString' => 'email',
        'iconString' => 'envelope',
        'placeholder' => 'contoh@domain.com',
        'message' => $errors->first('email'),
    ])

    {{-- 🔒 Password Input --}}
    <template x-if="$store.user?.typeModal == 'admin' || $store.user?.typeModal == 'dosen'" x-cloak>
        <template x-if="$store.user?.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'isLivewire' => 1,
                'modelString' => 'password',
                'typeString' => 'password',
                'iconString' => 'lock-closed',
                'placeholder' => 'Default: NIP',
                'message' => $errors->first('password'),
                'isRequired' => 0,
            ])
        </template>
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        <template x-if="$store.user?.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'isLivewire' => 1,
                'modelString' => 'password',
                'typeString' => 'password',
                'iconString' => 'lock-closed',
                'placeholder' => 'Default: NIM',
                'message' => $errors->first('password'),
                'isRequired' => 0,
            ])
        </template>
    </template>

    <template x-if="$store.user?.isEdit == 1" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'isLivewire' => 1,
            'modelString' => 'password',
            'typeString' => 'password',
            'iconString' => 'lock-closed',
            'placeholder' => 'Kosongkan jika tidak ingin diubah...',
            'message' => $errors->first('password'),
            'isRequired' => 0,
        ])
    </template>
</div>
