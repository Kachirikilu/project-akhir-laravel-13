<?php

namespace App\Actions\Fortify;

use App\Actions\Teams\CreateTeam;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Auth\Admin;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(private CreateTeam $createTeam)
    {
        //
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    // public function create(array $input): User
    // {
    //     Validator::make($input, [
    //         ...$this->profileRules(),
    //         'password' => $this->passwordRules(),
    //     ])->validate();

    //     return DB::transaction(function () use ($input) {
    //         $user = User::create([
    //             'name' => $input['name'],
    //             'email' => $input['email'],
    //             'password' => $input['password'],
    //         ]);

    //         $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

    //         return $user;
    //     });
    // }

    public function create(array $input): User
    {
        $adminKey = env('ADMIN_KEY', 'nrgKnSD$ZJP9sUh');

        if (empty($input['email']) && ! empty($input['nip'])) {
            $input['email'] = $input['nip'].'@staff.unsri.ac.id';
        }
        if (empty($input['password']) && ! empty($input['nip'])) {
            $input['password'] = $input['nip'];
            $input['password_confirmation'] = $input['nip'];
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'admin_key' => ['required', 'string'],
            // Tambahkan validasi NIP dan NIK di sini
            'nip' => ['required', 'string', 'min:8', 'max:20', 'unique:admins,nip'],
            'nik' => ['required', 'string', 'min:12', 'max:16', 'unique:admins,nik'],
        ], [
            // Pesan error kustom Anda
            'email.required' => 'Alamat Email wajib diisi!',
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
            'nik.required' => 'NIK wajib diisi!',
            'nik.unique' => 'NIK ini sudah terdaftar!',
            'nik.min' => 'NIK minimal harus 12 karakter!',
            'nik.max' => 'NIK maksimal 16 karakter!',
            'admin_key.required' => 'Admin Secret Key wajib diisi!',
        ])->after(function ($validator) use ($input, $adminKey) {
            if (! empty($input['admin_key']) && $input['admin_key'] !== $adminKey) {
                $validator->errors()->add('admin_key', 'Kunci otoritas Admin yang dimasukkan salah!');
            }
        })->validate();

        $isAdmin = (! empty($input['admin_key']) && $input['admin_key'] === $adminKey);

        return DB::transaction(function () use ($input, $isAdmin) {
            if ($isAdmin) {
                $user = User::create([
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                ]);
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $input['name'],
                    'pr_id' => null,
                    'nip' => $input['nip'],
                    'nik' => $input['nik'],
                ]);
            }

            return $user;
        });
    }
}
