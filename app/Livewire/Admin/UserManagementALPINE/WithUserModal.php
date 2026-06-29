<?php

namespace App\Livewire\Admin\UserManagement;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Models\Akademik\RPS;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\Membership;
use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

trait WithUserModal
{
    use HasErrorCount;
    use HasToast;
    use WithDosenSearchFilters;

    public $showUserModal = false;

    public $showUserRPSModal = false;

    public $showUserExcelModal = false;

    public $isEditingUser = false;

    public $roleType;

    public $selected_id_user;

    public $pr_id_2;

    public $user_rps_items_list = [];

    public $user_rps_modal_page = 3;

    public $user_rps_id;

    protected $user_rps_modal_paginator;

    public $isFlyoutUser = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8',
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|max:20',
        'nitk' => 'nullable|string|max:20',
        'nidn' => 'nullable|string|max:20',
        'nidk' => 'nullable|string|max:20',
        'nim' => 'required|string|max:20',
        'angkatan' => 'required|integer',
        'pr_id' => 'required|integer|exists:prodis,id',
    ];

    // public function updatedShowUserModal($value)
    // {
    //     if (! $value) {
    //         $this->isEditingUser = false;
    //     }
    //     $this->syncFlyoutPreStates();
    // }

    public function addUser($role)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingUser = false;
        $this->roleType = $role;

        if ($role == 'excel') {
            $this->showUserExcelModal = true;
            $this->showUserModal = false;
        } else {
            $this->showUserModal = true;
            $this->showUserExcelModal = false;
        }
        $this->showUserRPSModal = false;
        $this->updatedPrNameSearch($this->prNameSearch);
    }

    public function editUser($id, $withRPS = false, $isRPS = false)
    {
        if ($isRPS) {
            if (! $this->AuthCheck('staff')) {
                return;
            }
        } else {
            if (! $this->AuthCheck()) {
                return;
            }
        }

        $this->resetInputUser();
        $this->resetValidation();

        $this->resetErrorBag();

        if ($isRPS) {
            $this->showUserRPSModal = true;
            $this->showUserModal = false;
        } else {
            $this->showUserModal = true;
            $this->showUserRPSModal = false;
        }
        $this->showUserExcelModal = false;

        $this->isEditingUser = true;

        try {
            $user = User::with(['admin', 'dosen', 'mahasiswa'])->findOrFail($id);
            $this->selected_id_user = $user->id;

            $this->pr_id = $user->pr_id;
            if (! $isRPS) {
                $this->pr_id_2 = $user->pr_id;
                $this->pr_items = $this->itemsPr($user->admin?->pr_rel ?? $user->dosen?->pr_rel ?? $user->mahasiswa?->pr_rel);
                $this->prNameSearch = $user->prodi;
            }

            if ($user->dosen && $withRPS) {
                $this->user_rps_id = $user->dosen->id;
                $this->resetPage('user_rps_modal_page');
                $this->loadDosenRPSPagination();
            }

            if ($user->mahasiswa && $withRPS) {
                $this->user_rps_id = $user->mahasiswa->id;
                $this->resetPage('user_rps_modal_page');
                $this->loadMahasiswaRPSPagination();
            }

            $this->roleType = strtolower($user->role);
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    private function loadDosenRPSPagination()
    {
        if (empty($this->user_rps_id)) {
            return;
        }

        $dosen = Dosen::find($this->user_rps_id);

        if (! $dosen) {
            return;
        }

        $rps = RPS::whereHas('tim_dosens.dosens', function ($query) use ($dosen) {
            $query->where('dosens.id', $dosen->id);
        })->paginate($this->user_rps_modal_page, ['*'], 'user_rps_modal_page');

        $this->user_rps_items_list = $this->mapRPS($rps);
        $this->user_rps_modal_paginator = $rps;
    }

    private function loadMahasiswaRPSPagination()
    {
        if (empty($this->user_rps_id)) {
            return;
        }

        $mahasiswa = Mahasiswa::find($this->user_rps_id);

        if (! $mahasiswa) {
            return;
        }

        $rps = RPS::query()
            ->whereIn('id', function ($query) use ($mahasiswa) {
                $query->select('rps_id')
                    ->from('nilai_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->whereNotNull('rps_id')
                    ->distinct();
            })
            ->paginate(
                $this->user_rps_modal_page,
                ['*'],
                'user_rps_modal_page'
            );

        $this->user_rps_items_list = $this->mapRPS($rps);
        $this->user_rps_modal_paginator = $rps;
    }

    public function updatedDosenRPSModalPage($page)
    {
        $this->loadDosenRPSPagination();
    }

    public function updatedMahasiswaRPSModalPage($page)
    {
        $this->loadMahasiswaRPSPagination();
    }

    private function inputModalUser($isEditingUser, $data, $role)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if (empty($data['no_hp_back']) && ! empty($data['no_hp'])) {
            $data['kode_no_hp'] = null;
            $noHPLengkap = null;
        } elseif ($data['kode_no_hp'] && $data['no_hp_back']) {
            $data['no_hp'] = $data['kode_no_hp'].$data['no_hp_back'];
            $data['no_hp'] = str_replace([' ', '-', '+', '/'], '', $data['no_hp']);
        }

        if (empty($data['password']) && ! $isEditingUser) {
            if ($role === 'admin' || $role === 'dosen') {
                $data['password'] = $data['nip'];
            } elseif ($role === 'mahasiswa') {
                $data['password'] = $data['nim'];

            }
        }

        if (empty($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = null;
        }

        $rules = [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->selected_id_user),
            ],
            'password' => $isEditingUser ? 'nullable|min:8' : 'required|min:8',
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:20',
            'nitk' => 'nullable|string|max:20',
            'nidn' => 'nullable|string|max:20',
            'nidk' => 'nullable|string|max:20',
            'nim' => 'nullable|string|max:20',
            'nik' => 'required|string|min:12|max:16',
        ];

        /* ===================== ADMIN ===================== */
        if ($role === 'admin') {

            $rules['nip'] = [
                'required',
                $this->uniqueRule('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nitk'] = [
                'nullable',
                $this->uniqueRule('admins', 'nitk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',
                $this->uniqueRule('admins', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['kode_wilayah'] = [
                'required',
                Rule::in(['IDL', 'PLG']),
            ];

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Produktif)
                    'Tugas Belajar',          // Kuning (Transisi/Sementara)
                    'Mutasi',                 // Kuning (Transisi/Sementara)
                    'Cuti Luar Tanggungan',   // Kuning (Transisi/Sementara)
                    'Resign',                 // Orange (Keluar Prosedural)
                    'Pensiun',                // Orange (Keluar Prosedural)
                    'Diberhentikan',          // Merah (Masalah/Sanksi)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];

        }

        /* ===================== DOSEN ===================== */
        elseif ($role === 'dosen') {

            $rules['nip'] = [
                'required',
                $this->uniqueRule('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nidn'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidn'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nidk'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',
                $this->uniqueRule('dosens', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Produktif)
                    'Tugas Belajar',          // Kuning (Transisi/Studi)
                    'Izin Belajar',           // Kuning (Transisi/Studi)
                    'Cuti Sabatika',          // Kuning (Transisi/Riset)
                    'Alih Tugas',             // Orange (Perubahan Jabatan)
                    'Resign',                 // Orange (Keluar Prosedural)
                    'Pensiun',                // Orange (Keluar Prosedural)
                    'Diberhentikan',          // Merah (Masalah/Sanksi)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        /* ===================== MAHASISWA ===================== */
        elseif ($role === 'mahasiswa') {

            $rules['nim'] = [
                'required',
                $this->uniqueRule('mahasiswas', 'nim'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',

                $this->uniqueRule('mahasiswas', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
            ];

            $rules['kode_wilayah'] = [
                'required',
                Rule::in(['IDL', 'PLG']),
            ];

            $rules['angkatan'] =
                'required|integer|min:1960|max:'.date('Y');

            $rules['status'] = [
                'required',
                Rule::in([
                    'Aktif',                  // Hijau (Aktif Kuliah)
                    'Lulus',                  // Biru (Output Positif)
                    'Cuti',                   // Kuning (Jeda Resmi)
                    'Pindah',                 // Kuning (Transisi Keluar)
                    'Non-Aktif',              // Orange (Masalah Administrasi)
                    'Mengundurkan Diri',      // Orange (Keluar Prosedural)
                    'Drop Out',               // Merah (Masalah Akademik/Sanksi)
                    'Hilang',                 // Merah (Tanpa Kabar/Ghaib)
                    'Meninggal Dunia',         // Merah (Permanen)
                ]),
            ];
        }

        $rules['no_hp'] = [
            'nullable',
            'min:11',
            'max:15',
        ];

        $rules['jenis_kelamin'] = [
            'required',
            Rule::in([
                'Laki-laki',
                'Perempuan',
            ]),
        ];

        $rules['agama'] = [
            'required',
            Rule::in([
                'Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya',
            ]),
        ];

        $rules['tempat_lahir'] = [
            'nullable',
            'max:255',
        ];

        $rules['tanggal_lahir'] = [
            'nullable',
            'date',
        ];

        $rules['pr_id'] = 'required|exists:prodis,id';

        $validator = Validator::make($data, $rules, $this->validationMessagesUser());

        $validator->after(function ($validator) use ($data, $role) {

            if ($role === 'admin') {
                if (! empty($data['nip']) && ! empty($data['nitk']) && $data['nip'] === $data['nitk'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nitk',
                        'NITK tidak boleh memiliki nilai yang sama dengan NIP!'
                    );
                }

            } elseif ($role === 'dosen') {
                if (! empty($data['nip']) && ! empty($data['nidn']) && $data['nip'] === $data['nidn'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidn',
                        'NIDN tidak boleh memiliki nilai yang sama dengan NIP dan NIDK!'
                    );
                }

                if (! empty($data['nip']) && ! empty($data['nidk']) && $data['nip'] === $data['nidk'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );
                }

                if (! empty($data['nidn']) && ! empty($data['nidk']) && $data['nidn'] === $data['nidk'] && $data['nidn'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );
                }
            }

        });

        return $validator->validate();
    }

    private function uniqueRule(string $table, string $column)
    {
        return $this->selected_id_user
            ? Rule::unique($table, $column)->ignore($this->selected_id_user, 'user_id')
            : Rule::unique($table, $column);
    }

    public function saveUser($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }

        $data['pr_id'] = $this->pr_id;
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        try {
            $validated = $this->inputModalUser(false, $data, $this->roleType);

            DB::transaction(function () use ($validated) {

                // 1. Buat User Baru
                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                if ($this->roleType !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($this->roleType == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($this->roleType !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'];
                }

                $dosen = null;

                $data = [
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($this->roleType === 'admin') {
                    Admin::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ]));
                } elseif ($this->roleType === 'dosen') {
                    $dosen = Dosen::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ]));
                } elseif ($this->roleType === 'mahasiswa') {
                    Mahasiswa::create(array_merge($data, [
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'kode_wilayah' => $kodeWly,
                    ]));
                }

                $team = Team::forceCreate([
                    'name' => explode(' ', $validated['name'])[0]."'s Team",
                    'is_personal' => true,
                ]);

                $user->forceFill(['current_team_id' => $team->id])->save();

                Membership::create([
                    'team_id' => $team->id,
                    'user_id' => $user->id,
                    'role' => 'owner',
                ]);

                if (! empty($this->showTimDosenModal) && $dosen) {
                    if (! isset($this->dosen_id_array) || ! is_array($this->dosen_id_array)) {
                        $this->dosen_id_array = [];
                    }
                    if (! isset($this->dosen_items_array) || ! is_array($this->dosen_items_array)) {
                        $this->dosen_items_array = [];
                    }
                    if (! in_array($dosen->id, $this->dosen_id_array)) {
                        $this->dosen_id_array[] = $dosen->id;
                        $this->dosen_items_array[] = $this->itemsDosen($dosen);
                    }

                    $isKetua = collect($this->dosen_items_array)
                        ->contains(fn ($item) => $item['is_ketua'] === true);
                    if (! $isKetua && count($this->dosen_items_array) > 0) {
                        $lastIndex = array_key_last($this->dosen_items_array);
                        $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
                        $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
                    }
                }

            });

            $this->toast(message: ucfirst($this->roleType), isAkun: true);
            $this->resetInputUser();

            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
            $this->showUserRPSModal = false;
        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
            $this->showUserRPSModal = false;
        }
    }

    public function updateUser($data)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        if ((empty($data['pr_id']) && $this->pr_id !== $this->pr_id_2) ||
            ($this->pr_id == $this->pr_id_2) || ($this->pr_id !== $this->pr_id_2)) {
            $data['pr_id'] = $this->pr_id;
        }
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        try {
            $validated = $this->inputModalUser(true, $data, $this->roleType);

            DB::transaction(function () use ($validated) {

                $user = User::findOrFail($this->selected_id_user);
                $user->update(['email' => $validated['email']]);

                if ($validated['password']) {
                    $user->update(['password' => Hash::make($validated['password'])]);
                }

                if ($this->roleType !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($this->roleType == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($this->roleType !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'];
                }

                $model = match ($this->roleType) {
                    'admin' => $user->admin,
                    'dosen' => $user->dosen,
                    'mahasiswa' => $user->mahasiswa,
                };

                $data = [
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($this->roleType === 'admin') {
                    $data += [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ];
                } elseif ($this->roleType === 'dosen') {
                    $data += [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ];
                } elseif ($this->roleType === 'mahasiswa') {
                    $data += [
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'kode_wilayah' => $kodeWly,
                    ];
                }

                $model->update($data);
            });

            $this->toast(message: ucfirst($this->roleType), type: 'update', isAkun: true);
            $this->dispatch('refresh-data-user');

            $this->showUserModal = false;
            $this->showUserRPSModal = false;
            if (Auth::id() === $this->selected_id_user) {
                $this->dispatch('profile-updated');
            }
            $this->resetInputUser();

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserDelete = false;
        }
    }

    public function validationMessagesUser()
    {
        return [
            'email.required' => 'Alamat email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email ini sudah terdaftar di sistem!',
            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
            'name.required' => 'Nama lengkap wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'nip.required' => 'NIP wajib diisi untuk Admin dan Dosen!',
            'nip.unique' => 'NIP ini sudah terdaftar!',
            'nip.max' => 'NIP maksimal 20 karakter!',
            'nitk.unique' => 'NITK ini sudah terdaftar!',
            'nitk.max' => 'NITK maksimal 20 karakter!',
            'nidn.unique' => 'NIDN ini sudah terdaftar!',
            'nidn.max' => 'NIDN maksimal 20 karakter!',
            'nidk.unique' => 'NIDK ini sudah terdaftar!',
            'nidk.max' => 'NIDK maksimal 20 karakter!',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'nim.max' => 'NIM maksimal 20 karakter!',
            'nik.required' => 'NIK wajib diisi!',
            'nik.unique' => 'NIK ini sudah terdaftar!',
            'nik.min' => 'NIK minimal harus 12 karakter!',
            'nik.max' => 'NIK maksimal 16 karakter!',
            'no_hp.min' => 'Nomor Telepon minimal harus 11 digit!',
            'no_hp.max' => 'Nomor Telepon maksimal 15 digit!',
            'angkatan.required' => 'Tahun angkatan wajib diisi!',
            'angkatan.integer' => 'Tahun angkatan harus berupa angka!',
            'angkatan.min' => 'Tahun angkatan tidak boleh kurang dari tahun 1960!',
            'angkatan.max' => 'Tahun angkatan tidak boleh melebihi tahun sekarang!',
            'pr_id.required' => 'Program Studi wajib dipilih!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak valid!',
            'excel_user_file.required' => 'File Excel Data Pengguna wajib diunggah!',
            'excel_user_file.array' => 'Format unggahan tidak valid!',
            'excel_user_file.*.file' => 'Salah satu file Excel Data Pengguna harus berupa file yang valid!',
            'excel_user_file.*.mimes' => 'Setiap file Excel Data Pengguna harus berformat .xlsx atau .xls!',
            'excel_user_file.*.max' => 'Ukuran masing-masing file tidak boleh lebih dari 27 MB!',
            'kode_wilayah.required' => 'Kode Wilayah untuk Admin & Mahasiswa wajib dipilih!',
            'kode_wilayah.in' => "Kode Wilayah hanya boleh 'IDL' & 'PLG'!",
            'status.required' => 'Status pengguna wajib dipilih!',
            'status.in' => 'Status yang dipilih tidak sesuai dengan kategori yang diizinkan!',

            'tempat_lahir.max' => 'Tempat Lahir tidak boleh lebih dari 255 karakter!',
            'tanggal_lahir.date' => 'Format Tanggal Lahir tidak valid!',
            'agama.required' => 'Agama wajib diisi!',
            'agama.in' => 'Agama yang dipilih tidak sesuai dengan kategori yang diizinkan!',
            'jenis_kelamin.required' => 'Gender wajib diisi!',
            'jenis_kelamin.in' => 'Gender hanya boleh Laki-laki atau Perempuan!',
        ];
    }

    public function getUserErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'email',
                'password',
            ]),
            2 => $this->getErrorCount([
                'name',
                'nik',
                'jenis_kelamin',
                'agama',
            ]),
            3 => $this->getErrorCount([
                'nip',
                'nitk',
                'nidn',
                'nidk',
                'nim',
            ]),
            4 => $this->getErrorCount([
                'kode_wilayah',
                'pr_id',
                'angkatan',
                'status',
            ]),
            5 => $this->getErrorCount([
                'tempat_lahir',
                'tanggal_lahir',
                'no_hp',
            ]),
            6 => $this->getErrorCount([]),
        ];
    }

    public function resetInputUser(
        // $keepProdi = false
    ) {
        $fields = [
            'selected_id_user',
            'pr_id', 'pr_id_2', 'prNameSearch',
            // 'email', 'password', 'name', 'nip', 'nitk',
            // 'nidn', 'nidk', 'nim', 'angkatan',
            'roleType',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
