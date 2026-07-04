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

    // public $pr_id_2;

    public $user_rps_items_list = [];

    public $user_rps_modal_page = 3;

    public $user_rps_id;

    protected $user_rps_modal_paginator;

    // public $isFlyoutUser = false;

    public $user_input = [
        'role' => '',
        // 'email' => '',
        'password' => '',
        'name' => '',
        'nip' => '',
        'nitk' => '',
        'nidn' => '',
        'nidk' => '',
        'nim' => '',
        'nik' => '',
        'status' => '',
        'angkatan' => '',
        'kode_wilayah' => '',
        'jenis_kelamin' => '',
        'agama' => '',
        'kode_no_hp' => '+62',
        'no_hp_back' => '',
        'no_hp' => '',
        'tanggal_lahir' => '',
        'tempat_lahir' => '',
    ];

    protected $rules = [
        'email' => 'required|email',
        'password' => 'nullable|min:8',
        'name' => 'required|string|max:255',
        'nip' => 'nullable|string|min:8|max:20',
        'nitk' => 'nullable|string|min:8|max:20',
        'nidn' => 'nullable|string|min:8|max:20',
        'nidk' => 'nullable|string|min:8|max:20',
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
        $colors = [
            'admin' => 'text-red-700 dark:text-red-400',
            'dosen' => 'text-lime-700 dark:text-lime-400',
            'mahasiswa' => 'text-cyan-700 dark:text-cyan-400',
        ];
        $color = $colors[$role] ?? 'text-gray-700 dark:text-gray-400';
        $this->dispatch('prepare-add-user-modal', type: $role, color: $color);
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

            // $this->user_input['email'] = $user->email;
            $this->user_input['name'] = $user->name;
            $this->user_input['nik'] = $user->nik;
            $this->user_input['status'] = $user->status;
            $this->user_input['tempat_lahir'] = $user->tmt_lahir;
            $this->user_input['tanggal_lahir'] = $user->tanggal_lahir;
            $this->user_input['agama'] = $user->agama;
            $this->user_input['jenis_kelamin'] = $user->gender;
            $this->user_input['role'] = $user->role;

            $formattedPhone = $this->formatNomorHP($user->no_hp);
            $this->user_input['no_hp_back'] = $formattedPhone['no_hp_back'];
            $this->user_input['kode_no_hp'] = $formattedPhone['kode_no_hp'];

            $role = strtolower($user->role);

            if ($role == 'admin') {
                $this->user_input['nip'] = $user->admin->nip ?? null;
                $this->user_input['nitk'] = $user->admin->nitk ?? null;
                $this->user_input['kode_wilayah'] = $user->admin->kode_wilayah ?? null;
            } elseif ($role == 'dosen') {
                $this->user_input['nip'] = $user->dosen->nip ?? null;
                $this->user_input['nidn'] = $user->dosen->nidn ?? null;
                $this->user_input['nidk'] = $user->dosen->nidk ?? null;
            } elseif ($role == 'mahasiswa') {
                $this->user_input['nim'] = $user->mahasiswa->nim ?? null;
                $this->user_input['angkatan'] = $user->mahasiswa->angkatan ?? null;
                $this->user_input['kode_wilayah'] = $user->mahasiswa->kode_wilayah ?? null;
            }

            $this->pr_id = $user->pr_id;
            if (! $isRPS) {
                // $this->pr_id_2 = $user->pr_id;
                // $this->pr_items = $this->itemsPr($user->admin?->pr_rel ?? $user->dosen?->pr_rel ?? $user->mahasiswa?->pr_rel);
                $this->prNameSearch = $user->prodi;
                $this->fetchPr($this->prNameSearch);
                // $this->updatedPrNameSearch($this->prNameSearch);
            }
            $this->roleType = strtolower($user->role);

            if ($withRPS) {
                // if ($user->dosen) {
                //     $this->user_rps_id = $user->dosen->id;
                //     // $this->loadDosenRPSPagination();
                // }
                // if ($user->mahasiswa) {
                //     $this->user_rps_id = $user->mahasiswa->id;
                //     // $this->loadMahasiswaRPSPagination();
                // }
                $this->user_rps_id = $user->role_id;
                $this->resetPage('user_rps_modal_page');
                $this->loadUserRPSPagination();
            }


            // dump($this->pr_id);
            // $this->syncUserStore($user, $formattedPhone['no_hp_back'] ?? '');
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    //    public function syncUserStore($user, $noHpBack = '')
    //     {
    //         $this->dispatch('sync-user-store',
    //             email: $user->email ?? '',
    //         );
    //     }

    // public function syncUserStore($user, $noHpBack = '')
    // {
    //     $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
    //     $type = strtolower($user->role);
    //     $colors = [
    //         'admin' => 'text-red-700 dark:text-red-400',
    //         'dosen' => 'text-lime-700 dark:text-lime-400',
    //         'mahasiswa' => 'text-cyan-700 dark:text-cyan-400',
    //     ];
    //     $color = $colors[$type] ?? 'text-gray-700 dark:text-gray-400';

    //     $this->dispatch('sync-user-store',
    //         type: $type,
    //         color: $color,
    //         email: $user->email ?? '',
    //         password: '',
    //         name: $user->name ?? '',
    //         nip: $user->admin->nip ?? ($user->dosen->nip ?? ''),
    //         nitk: $user->admin->nitk ?? '',
    //         nidn: $user->dosen->nidn ?? '',
    //         nidk: $user->dosen->nidk ?? '',
    //         nim: $user->mahasiswa->nim ?? '',
    //         nik: $user->nik ?? '',
    //         angkatan: $user->mahasiswa->angkatan ?? '',
    //         status: $user->status ?? '',
    //         prId: $user->pr_id ?? '',
    //         kodePr: $user->kode_pr ?? '',
    //         prodi: $user->prodi ?? '',
    //         departemen: $detail?->pr_rel?->departemen_dp ?? '',
    //         fakultas: $detail?->pr_rel?->fakultas_fk ?? '',
    //         wilayah: $user->kode_wilayah ?? '',
    //         rps: $user->mahasiswa?->count_rps ?? 0,
    //         sks: $user->mahasiswa?->total_sks ?? 0,
    //         rekap: $user->mahasiswa?->rekap_mhs ?? 0.00,
    //         index: $user->mahasiswa?->index_mhs ?? 0.00,
    //         mutu: $user->mahasiswa?->mutu_mhs ?? 'E',
    //         jk: $user->gender ?? '',
    //         agama: $user->agama ?? '',
    //         tmtLahir: $user->tmt_lahir ?? '',
    //         tglLahir: $user->tanggal_lahir ?? '',
    //         noHP: $noHpBack ?? ''
    //     );
    // }

    private function loadUserRPSPagination()
    {
        if (empty($this->user_rps_id)) {
            return;
        }

        $role = strtolower($this->roleType);
        $rpsQuery = RPS::query();

        if ($role === 'dosen') {
            $dosen = Dosen::find($this->user_rps_id);
            if (! $dosen) {
                return;
            }

            $rpsQuery->whereHas('tim_dosens.dosens', function ($query) use ($dosen) {
                $query->where('dosens.id', $dosen->id);
            });

        } elseif ($role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::find($this->user_rps_id);
            if (! $mahasiswa) {
                return;
            }

            $rpsQuery->whereIn('id', function ($query) use ($mahasiswa) {
                $query->select('rps_id')
                    ->from('nilai_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->whereNotNull('rps_id')
                    ->distinct();
            });
        } else {
            // Handle jika role tidak valid
            return;
        }

        // Eksekusi pagination
        $rps = $rpsQuery->paginate(
            $this->user_rps_modal_page,
            ['*'],
            'user_rps_modal_page'
        );

        // Update state komponen
        $this->user_rps_items_list = $this->mapRPS($rps);
        $this->user_rps_modal_paginator = $rps;
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

    // private function loadMahasiswaRPSPagination()
    // {
    //     if (empty($this->user_rps_id)) {
    //         return;
    //     }

    //     $mahasiswa = Mahasiswa::find($this->user_rps_id);

    //     if (! $mahasiswa) {
    //         return;
    //     }

    //     $rps = RPS::query()
    //         ->whereIn('id', function ($query) use ($mahasiswa) {
    //             $query->select('rps_id')
    //                 ->from('nilai_mahasiswa')
    //                 ->where('mahasiswa_id', $mahasiswa->id)
    //                 ->whereNotNull('rps_id')
    //                 ->distinct();
    //         })
    //         ->paginate(
    //             $this->user_rps_modal_page,
    //             ['*'],
    //             'user_rps_modal_page'
    //         );

    //     $this->user_rps_items_list = $this->mapRPS($rps);
    //     $this->user_rps_modal_paginator = $rps;
    // }

    public function updatedUserRPSModalPage($page)
    {
        $this->loadUserRPSPagination();
        // $this->loadDosenRPSPagination();
        // $this->loadMahasiswaRPSPagination();
    }

    private function inputModalUser($isEditingUser, $data, $role)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        // dd($data['no_hp_back']);
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
            'nip' => 'nullable|string|min:8|max:20',
            'nitk' => 'nullable|string|min:8|max:20',
            'nidn' => 'nullable|string|min:8|max:20',
            'nidk' => 'nullable|string|min:8|max:20',
            'nim' => 'nullable|string|min:8|max:20',
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

    public function formatNomorHP($noHP)
    {
        $result = [
            'kode_no_hp' => '+62',
            'no_hp_back' => '',
        ];

        if (blank($noHP)) {
            return $result;
        }
        $noHP = trim($noHP);
        $countryCodes = [
            '62',
            '65',
            '60',
            '1',
            '44',
            '81',
            '82',
            '86',
        ];
        if (str_starts_with($noHP, '+')) {
            $digits = preg_replace('/\D/', '', $noHP);
            foreach ($countryCodes as $code) {
                if (str_starts_with($digits, $code)) {
                    $result['kode_no_hp'] = '+'.$code;
                    $cleaned = substr($digits, strlen($code));
                    break;
                }
            }
            $cleaned ??= $digits;
        } else {
            $cleaned = preg_replace('/\D/', '', $noHP);
            if (str_starts_with($cleaned, '0')) {
                $cleaned = substr($cleaned, 1);
            } elseif (str_starts_with($cleaned, '62')) {
                $cleaned = substr($cleaned, 2);
            }
        }
        if (preg_match('/^(\d{1,3})(\d{1,4})?(\d{1,5})?(\d+)?$/', $cleaned, $matches)) {
            array_shift($matches);
            $result['no_hp_back'] = implode(' - ', array_filter($matches));
        } else {
            $result['no_hp_back'] = $cleaned;
        }

        return $result;
    }

    private function uniqueRule(string $table, string $column)
    {
        return $this->selected_id_user
            ? Rule::unique($table, $column)->ignore($this->selected_id_user, 'user_id')
            : Rule::unique($table, $column);
    }

    public function saveUser($dataAlpine)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $data = array_merge($this->user_input, $dataAlpine);
        $data['pr_id'] = $this->pr_id;
        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        $role = strtolower($this->roleType);
        try {
            $validated = $this->inputModalUser(false, $data, $role);

            DB::transaction(function () use ($validated, $role) {

                // 1. Buat User Baru
                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                if ($role !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($role == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($role !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'] ?? null;
                }

                $dosen = null;

                $data = [
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'] ?? null,

                    'agama' => $validated['agama'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($role === 'admin') {
                    Admin::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ]));
                } elseif ($role === 'dosen') {
                    $dosen = Dosen::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ]));
                } elseif ($role === 'mahasiswa') {
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

                // if (! empty($this->showTimDosenModal) && $dosen) {
                //     if (! isset($this->dosen_id_array) || ! is_array($this->dosen_id_array)) {
                //         $this->dosen_id_array = [];
                //     }
                //     if (! isset($this->dosen_items_array) || ! is_array($this->dosen_items_array)) {
                //         $this->dosen_items_array = [];
                //     }
                //     if (! in_array($dosen->id, $this->dosen_id_array)) {
                //         $this->dosen_id_array[] = $dosen->id;
                //         $this->dosen_items_array[] = $this->itemsDosen($dosen);
                //     }

                //     $isKetua = collect($this->dosen_items_array)
                //         ->contains(fn ($item) => $item['is_ketua'] === true);
                //     if (! $isKetua && count($this->dosen_items_array) > 0) {
                //         $lastIndex = array_key_last($this->dosen_items_array);
                //         $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
                //         $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
                //     }
                // }

                if (($this->parent == 'tim-dosen' || $this->parent == 'tim_dosen') && $dosen) {
                    $this->dispatch('dosen-created-tim-dosen', id: $dosen->id);
                }

            });

            $this->toast(message: ucfirst($this->roleType), isAkun: true);
            $this->resetInputUser();

            $this->dispatch('refresh-data-user');
            $this->dispatch('refresh-stats-user');
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

    public function updateUser($dataAlpine)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $data = array_merge($this->user_input, $dataAlpine);
        $data['pr_id'] = $this->pr_id;
        // if ((empty($data['pr_id']) && $this->pr_id !== $this->pr_id_2) ||
        //     ($this->pr_id == $this->pr_id_2) || ($this->pr_id !== $this->pr_id_2)) {
        //     $data['pr_id'] = $this->pr_id;
        // }

        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }
        $role = strtolower($this->roleType);
        try {
            $validated = $this->inputModalUser(true, $data, $role);

            DB::transaction(function () use ($validated, $role) {

                $user = User::findOrFail($this->selected_id_user);
                $user->update(['email' => $validated['email']]);

                if ($validated['password']) {
                    $user->update(['password' => Hash::make($validated['password'])]);
                }

                if ($role !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($role == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($role !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'];
                }

                $model = match ($role) {
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

                if ($role === 'admin') {
                    $data += [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ];
                } elseif ($role === 'dosen') {
                    $data += [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ];
                } elseif ($role === 'mahasiswa') {
                    $data += [
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'kode_wilayah' => $kodeWly,
                    ];
                }

                $model->update($data);
            });
            $roleType = ucfirst($role);
            $labelIdentity1;
            if ($role == 'admin' || $role == 'dosen') {
                $labelIdentity1 = "NIP {$validated['nip']}";
            } elseif ($role == 'mahasiswa') {
                $labelIdentity1 = "NIM {$validated['nim']}";
            }

            $this->toast(message: "{$roleType} {$labelIdentity1} dengan Email {$validated['email']}", type: 'update', isAkun: true);
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
            'nip.min' => 'NIP minimal 8 karakter!',
            'nip.max' => 'NIP maksimal 20 karakter!',
            'nitk.unique' => 'NITK ini sudah terdaftar!',
            'nitk.min' => 'NITK minimal 8 karakter!',
            'nitk.max' => 'NITK maksimal 20 karakter!',
            'nidn.unique' => 'NIDN ini sudah terdaftar!',
            'nidn.min' => 'NIDN minimal 8 karakter!',
            'nidn.max' => 'NIDN maksimal 20 karakter!',
            'nidk.unique' => 'NIDK ini sudah terdaftar!',
            'nidk.min' => 'NIDK minimal 8 karakter!',
            'nidk.max' => 'NIDK maksimal 20 karakter!',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'nim.min' => 'NIM minimal 8 karakter!',
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
            'user_input',
            'selected_id_user',
            'pr_id', 'prNameSearch',
            // 'pr_id', 'pr_id_2', 'prNameSearch',
            // 'email', 'password', 'name', 'nip', 'nitk',
            // 'nidn', 'nidk', 'nim', 'angkatan',
            'roleType',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
