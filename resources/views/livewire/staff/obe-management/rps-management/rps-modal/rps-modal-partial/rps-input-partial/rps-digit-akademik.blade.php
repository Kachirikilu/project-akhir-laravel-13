<div x-data="{
    getSuffix(tahun) {
        let ta = parseInt(tahun);
        if (!ta) return '';

        if (ta >= 3000) return String(ta);
        if (ta >= 2100) return String(ta).slice(-3);
        if (ta >= 2000) return String(ta).slice(-2);
        return String(ta);
    }
}"
    x-effect="
        if ($store.rps) {
            let ta1 = $store.rps.akademik_1;
            let ta2 = $store.rps.akademik_2;

            if (!ta1 || !ta2) {
                $store.rps.digit_akademik = '';
            } else {
                $store.rps.digit_akademik = getSuffix(ta1) + getSuffix(ta2);
            }
        }
    ">

    @include('livewire.global.modal-form.kode-input', [
        'alpine' => 'rps',
        'noLabel' => 1,
        // 'nameX2String' => 'Kode RPS',
        'modelString' => 'digit_akademik',
        'placeholder' => '--',
        'iconString' => 'variable',
    ])
</div>
