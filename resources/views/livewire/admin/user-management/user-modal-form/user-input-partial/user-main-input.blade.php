{{-- ****************************************************** --}}
{{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-lg font-medium border-b pb-2 mb-6">
        Informasi Akun</h4>

    {{-- 📧 Email Input --}}
    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            // 'isLivewire' => 1,
            'modelString' => 'email',
            'typeString' => 'email',
            'iconString' => 'envelope',
            'placeholder' => 'Default: nip@staff.unsri.ac.id',
            'message' => $errors->first('email'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            // 'isLivewire' => 1,
            'modelString' => 'email',
            'typeString' => 'email',
            'iconString' => 'envelope',
            'placeholder' => 'Default: nip@lecture.unsri.ac.id',
            'message' => $errors->first('email'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            // 'isLivewire' => 1,
            'modelString' => 'email',
            'typeString' => 'email',
            'iconString' => 'envelope',
            'placeholder' => 'Default: nim@student.unsri.ac.id',
            'message' => $errors->first('email'),
            'isRequired' => 0,
        ])
    </template>

    {{-- 🔒 Password Input --}}
    <template x-if="$store.user?.typeModal == 'admin' || $store.user?.typeModal == 'dosen'" x-cloak>
        <template x-if="$store.user?.isEdit == 0" x-cloak>
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'isLivewire' => 1,
                'modelString' => 'password',
                'typeString' => 'password',
                'showPassword' => 1,
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
                'showPassword' => 1,
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
            'showPassword' => 1,
            'placeholder' => 'Kosongkan jika tidak ingin diubah...',
            'message' => $errors->first('password'),
            'isRequired' => 0,
        ])
    </template>
</div>
