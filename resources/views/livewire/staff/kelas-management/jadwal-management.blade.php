<div class="py-6 sm:px-6 sm:py-10 sm:bg-[var(--wadah-color)] sm:shadow-sm rounded-xl">
    
    @include('livewire.staff.kelas-management.jadwal-management.jadwal-header', [
        'mainKode' => $kelas->kode ?? '-',
        'mainHead' => 'Kelas',
        'subHead' => 'Jadwal Kelas'
    ])

    @include('livewire.staff.obe-management.rps-management.rps-show-modal', ['alpineKey' => 'jadwal?.rps_id_show', 'isEdit' => 0])

    @include('livewire.staff.kelas-management.jadwal-management.jadwal-toolbar')
    @include('livewire.staff.kelas-management.jadwal-management.jadwal-table')

    @include('livewire.staff.kelas-management.jadwal-management.jadwal-modal-form')
</div>

