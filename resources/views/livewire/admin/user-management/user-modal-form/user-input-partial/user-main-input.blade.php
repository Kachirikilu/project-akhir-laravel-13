{{-- ****************************************************** --}}
{{-- 1. ACCOUNT INFORMATION (EMAIL & PASSWORD) --}}
{{-- ****************************************************** --}}
<div class="form-container">
    <h4
        class="text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium border-b pb-2 mb-6">
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
                // 'isLivewire' => 1,
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
                // 'isLivewire' => 1,
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
            // 'isLivewire' => 1,
            'modelString' => 'password',
            'typeString' => 'password',
            'showPassword' => 1,
            'placeholder' => 'Kosongkan jika tidak ingin diubah...',
            'message' => $errors->first('password'),
            'isRequired' => 0,
        ])
    </template>

    @php
        $user = Auth::user();

        $tingkatMe = (int) ($user->admin?->tingkat ?? ($user->dosen?->tingkat ?? 5));
        $isMe = (int) $selected_id_user === (int) $user->id;

        $canAccess = !$isMe && ($tingkatMe <= ($tingkatType ?? 5));
    @endphp
    <template x-if="$store.user?.typeModal == 'admin'" x-cloak>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            // 'isLivewire' => 1,
            'nameXString' => 'Tingkat Admin',
            'modelString' => 'tingkat',
            'xOptions' => [
                'Admin Program Studi',
                'Admin Departemen',
                'Admin Fakultas',
                'Admin ' . config('app.univ'),
            ],
            'xValues' => [4, 3, 2, 1],
            'iconString' => 'shield-check',
            'placeholder' => 'Defau: Admin Program Studi',
            'message' => $errors->first('tingkat'),
            'isReadonly' => $canAccess ? 0 : 1,
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'dosen'" x-cloak>
        @include('livewire.global.modal-form.select-form', [
            'alpine' => 'user',
            // 'isLivewire' => 1,
            'nameXString' => 'Tingkat Dosen',
            'modelString' => 'tingkat',
            'xOptions' => [
                'Dosen Umum',
                'Dosen Program Studi',
                'Dosen Departemen',
                'Dosen Fakultas',
                'Dosen ' . config('app.univ'),
            ],
            'xValues' => [5, 4, 3, 2, 1],
            'iconString' => 'shield-check',
            'placeholder' => 'Defau: Dosen Umum',
            'message' => $errors->first('tingkat'),
            'isRequired' => 0,
        ])
    </template>
    <template x-if="$store.user?.typeModal == 'mahasiswa'" x-cloak>
        @include('livewire.global.modal-form.input-form', [
            'alpine' => 'user',
            'value' => 'Mahasiswa',
            // 'isLivewire' => 1,
            'noEntangle' => 1,
            'nameXString' => 'Role',
            'modelString' => 'tingkat_text',
            'iconString' => 'shield-check',
            'isRequired' => 0,
            'isReadonly' => 1,
        ])
    </template>

</div>
