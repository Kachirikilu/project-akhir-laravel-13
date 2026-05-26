<div x-data="{}"
    x-effect="
                if ($store.mk) {
                    let sem = parseInt($store.mk.semester);
                    let blok = parseInt($store.mk.kode_blok);

                    if (!sem) {
                        $store.mk.digit_semester = '';
                    } else if (blok === 0) {
                        $store.mk.digit_semester = Math.ceil(sem / 2).toString() + '0';
                    } else {
                        let tahun = Math.ceil(sem / 2);
                        let tipe = (sem % 2 !== 0) ? '1' : '2';
                        $store.mk.digit_semester = tahun.toString() + tipe;
                    }
                }
            ">

    @include('livewire.global.modal-form.kode-input', [
        'alpine' => 'mk',
        'nameXString' => 'Digit Semester',
        'modelString' => 'digit_semester',
        'placeholder' => '--',
        'iconString' => 'variable',
    ])
</div>
