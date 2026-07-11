<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Kolom :attribute harus disetujui!',
    'accepted_if' => 'Kolom :attribute harus disetujui ketika :other bernilai :value!',
    'active_url' => 'Kolom :attribute harus berupa URL yang valid!',
    'after' => 'Kolom :attribute harus berupa tanggal setelah :date!',
    'after_or_equal' => 'Kolom :attribute harus berupa tanggal setelah atau sama dengan :date!',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf!',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_)!',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka!',
    'any_of' => 'Kolom :attribute tidak valid!',
    'array' => 'Kolom :attribute harus berupa array!',
    'ascii' => 'Kolom :attribute hanya boleh berisi karakter ASCII, huruf, angka, dan simbol!',
    'before' => 'Kolom :attribute harus berupa tanggal sebelum :date!',
    'before_or_equal' => 'Kolom :attribute harus berupa tanggal sebelum atau sama dengan :date!',
    'between' => [
        'array' => 'Kolom :attribute harus memiliki antara :min hingga :max item!',
        'file' => 'Ukuran :attribute harus antara :min hingga :max kilobyte!',
        'numeric' => 'Nilai :attribute harus antara :min hingga :max!',
        'string' => 'Kolom :attribute harus terdiri dari :min hingga :max karakter!',
    ],
    'boolean' => 'Kolom :attribute harus bernilai benar atau salah!',
    'can' => 'Kolom :attribute berisi nilai yang tidak diizinkan!',
    'confirmed' => 'Konfirmasi :attribute tidak sesuai!',
    'contains' => 'Kolom :attribute belum berisi nilai yang diperlukan!',
    'current_password' => 'Kata sandi yang dimasukkan tidak benar!',
    'date' => 'Kolom :attribute harus berupa tanggal yang valid!',
    'date_equals' => 'Kolom :attribute harus berupa tanggal yang sama dengan :date!',
    'date_format' => 'Format :attribute harus sesuai dengan :format!',
    'decimal' => 'Kolom :attribute harus memiliki :decimal angka di belakang koma!',
    'declined' => 'Kolom :attribute harus ditolak!',
    'declined_if' => 'Kolom :attribute harus ditolak ketika :other bernilai :value!',
    'different' => 'Kolom :attribute harus berbeda dengan :other!',
    'digits' => 'Kolom :attribute harus terdiri dari :digits digit!',
    'digits_between' => 'Kolom :attribute harus terdiri dari antara :min hingga :max digit!',
    'dimensions' => 'Dimensi gambar pada :attribute tidak valid!',
    'distinct' => 'Kolom :attribute memiliki nilai yang duplikat!',
    'doesnt_contain' => 'Kolom :attribute tidak boleh mengandung salah satu dari: :values!',
    'doesnt_end_with' => 'Kolom :attribute tidak boleh diakhiri dengan salah satu dari: :values!',
    'doesnt_start_with' => 'Kolom :attribute tidak boleh diawali dengan salah satu dari: :values!',
    'email' => 'Kolom :attribute harus berupa Alamat Email yang valid!',
    'encoding' => 'Kolom :attribute harus menggunakan encoding :encoding!',
    'ends_with' => 'Kolom :attribute harus diakhiri dengan salah satu dari: :values!',
    'enum' => 'Nilai yang dipilih pada :attribute tidak valid!',
    'exists' => 'Nilai yang dipilih pada :attribute tidak valid!',
    'extensions' => 'Kolom :attribute harus memiliki salah satu ekstensi berikut: :values!',
    'file' => 'Kolom :attribute harus berupa file!',
    'filled' => 'Kolom :attribute wajib diisi!',
    'gt' => [
        'array' => 'Kolom :attribute harus memiliki lebih dari :value item!',
        'file' => 'Ukuran :attribute harus lebih besar dari :value kilobyte!',
        'numeric' => 'Nilai :attribute harus lebih besar dari :value!',
        'string' => 'Kolom :attribute harus lebih dari :value karakter!',
    ],
    'gte' => [
        'array' => 'Kolom :attribute harus memiliki minimal :value item!',
        'file' => 'Ukuran :attribute harus lebih besar atau sama dengan :value kilobyte!',
        'numeric' => 'Nilai :attribute harus lebih besar atau sama dengan :value!',
        'string' => 'Kolom :attribute harus terdiri dari minimal :value karakter!',
    ],
    'hex_color' => 'Kolom :attribute harus berupa kode warna heksadesimal yang valid!',
    'image' => 'Kolom :attribute harus berupa gambar!',
    'in' => 'Nilai yang dipilih pada :attribute tidak valid!',
    'in_array' => 'Kolom :attribute harus terdapat di dalam :other!',
    'in_array_keys' => 'Kolom :attribute harus mengandung setidaknya salah satu kunci berikut: :values!',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat!',
    'ip' => 'Kolom :attribute harus berupa alamat IP yang valid!',
    'ipv4' => 'Kolom :attribute harus berupa alamat IPv4 yang valid!',
    'ipv6' => 'Kolom :attribute harus berupa alamat IPv6 yang valid!',
    'json' => 'Kolom :attribute harus berupa string JSON yang valid!',
    'list' => 'Kolom :attribute harus berupa daftar (list)!',
    'lowercase' => 'Kolom :attribute harus menggunakan huruf kecil!',
    'lt' => [
        'array' => 'Kolom :attribute harus memiliki kurang dari :value item!',
        'file' => 'Ukuran :attribute harus kurang dari :value kilobyte!',
        'numeric' => 'Nilai :attribute harus kurang dari :value!',
        'string' => 'Kolom :attribute harus kurang dari :value karakter!',
    ],
    'lte' => [
        'array' => 'Kolom :attribute tidak boleh memiliki lebih dari :value item!',
        'file' => 'Ukuran :attribute harus kurang dari atau sama dengan :value kilobyte!',
        'numeric' => 'Nilai :attribute harus kurang dari atau sama dengan :value!',
        'string' => 'Kolom :attribute harus terdiri dari maksimal :value karakter!',
    ],
    'mac_address' => 'Kolom :attribute harus berupa alamat MAC yang valid!',
    'max' => [
        'array' => 'Kolom :attribute tidak boleh memiliki lebih dari :max item!',
        'file' => 'Ukuran :attribute tidak boleh lebih dari :max kilobyte!',
        'numeric' => 'Nilai :attribute tidak boleh lebih dari :max!',
        'string' => 'Kolom :attribute harus terdiri dari maksimal :max karakter!',
    ],
    'max_digits' => 'Kolom :attribute tidak boleh terdiri dari lebih dari :max digit!',
    'mimes' => 'Kolom :attribute harus berupa file dengan tipe: :values!',
    'mimetypes' => 'Kolom :attribute harus berupa file dengan tipe: :values!',
    'min' => [
        'array' => 'Kolom :attribute harus memiliki minimal :min item!',
        'file' => 'Ukuran :attribute minimal :min kilobyte!',
        'numeric' => 'Nilai :attribute minimal :min!',
        'string' => 'Kolom :attribute harus terdiri dari minimal :min karakter!',
    ],
    'min_digits' => 'Kolom :attribute harus terdiri dari minimal :min digit!',
    'missing' => 'Kolom :attribute tidak boleh diisi!',
    'missing_if' => 'Kolom :attribute tidak boleh diisi ketika :other bernilai :value!',
    'missing_unless' => 'Kolom :attribute tidak boleh diisi kecuali :other bernilai :value!',
    'missing_with' => 'Kolom :attribute tidak boleh diisi ketika :values tersedia!',
    'missing_with_all' => 'Kolom :attribute tidak boleh diisi ketika semua :values tersedia!',
    'multiple_of' => 'Kolom :attribute harus merupakan kelipatan dari :value!',
    'not_in' => 'Nilai yang dipilih pada :attribute tidak valid!',
    'not_regex' => 'Format :attribute tidak valid!',
    'numeric' => 'Kolom :attribute harus berupa angka!',
    'password' => [
        'letters' => 'Kolom :attribute harus mengandung minimal satu huruf!',
        'mixed' => 'Kolom :attribute harus mengandung minimal satu huruf besar dan satu huruf kecil!',
        'numbers' => 'Kolom :attribute harus mengandung minimal satu angka!',
        'symbols' => 'Kolom :attribute harus mengandung minimal satu simbol!',
        'uncompromised' => ':attribute yang diberikan ditemukan dalam kebocoran data. Silakan gunakan :attribute yang berbeda!',
    ],
    'present' => 'Kolom :attribute harus tersedia!',
    'present_if' => 'Kolom :attribute harus tersedia ketika :other bernilai :value!',
    'present_unless' => 'Kolom :attribute harus tersedia kecuali :other bernilai :value!',
    'present_with' => 'Kolom :attribute harus tersedia ketika :values tersedia!',
    'present_with_all' => 'Kolom :attribute harus tersedia ketika semua :values tersedia!',
    'prohibited' => 'Kolom :attribute tidak boleh diisi!',
    'prohibited_if' => 'Kolom :attribute tidak boleh diisi ketika :other bernilai :value!',
    'prohibited_if_accepted' => 'Kolom :attribute tidak boleh diisi ketika :other disetujui!',
    'prohibited_if_declined' => 'Kolom :attribute tidak boleh diisi ketika :other ditolak!',
    'prohibited_unless' => 'Kolom :attribute tidak boleh diisi kecuali :other termasuk dalam :values!',
    'prohibits' => 'Kolom :attribute tidak mengizinkan kolom :other diisi!',
    'regex' => 'Format :attribute tidak valid!',
    'required' => 'Kolom :attribute wajib diisi!',
    'required_array_keys' => 'Kolom :attribute harus mengandung entri untuk: :values!',
    'required_if' => 'Kolom :attribute wajib diisi ketika :other bernilai :value!',
    'required_if_accepted' => 'Kolom :attribute wajib diisi ketika :other disetujui!',
    'required_if_declined' => 'Kolom :attribute wajib diisi ketika :other ditolak!',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other termasuk dalam :values!',
    'required_with' => 'Kolom :attribute wajib diisi ketika :values tersedia!',
    'required_with_all' => 'Kolom :attribute wajib diisi ketika semua :values tersedia!',
    'required_without' => 'Kolom :attribute wajib diisi ketika :values tidak tersedia!',
    'required_without_all' => 'Kolom :attribute wajib diisi ketika tidak satu pun dari :values tersedia!',
    'same' => 'Kolom :attribute harus sama dengan :other!',
    'size' => [
        'array' => 'Kolom :attribute harus memiliki :size item!',
        'file' => 'Ukuran :attribute harus :size kilobyte!',
        'numeric' => 'Nilai :attribute harus :size!',
        'string' => 'Kolom :attribute harus terdiri dari :size karakter!',
    ],
    'starts_with' => 'Kolom :attribute harus diawali dengan salah satu dari: :values!',
    'string' => 'Kolom :attribute harus berupa teks!',
    'timezone' => 'Kolom :attribute harus berupa zona waktu yang valid!',
    'unique' => ':attribute sudah digunakan!',
    'uploaded' => ':attribute gagal diunggah!',
    'uppercase' => 'Kolom :attribute harus menggunakan huruf besar!',
    'url' => 'Kolom :attribute harus berupa URL yang valid!',
    'ulid' => 'Kolom :attribute harus berupa ULID yang valid!',
    'uuid' => 'Kolom :attribute harus berupa UUID yang valid!',

    /*
|--------------------------------------------------------------------------
| Baris Bahasa Validasi Kustom
|--------------------------------------------------------------------------
|
| Di sini Anda dapat menentukan pesan validasi khusus untuk atribut tertentu
| menggunakan format "attribute.rule". Dengan cara ini, Anda dapat dengan
| mudah menentukan pesan khusus untuk aturan validasi tertentu.
|
*/

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
