<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['identity1'],
        'typeXString' => $data['label_id1'] . ' ' . $data['role'],
    ])
    @include('livewire.admin.user-management.user-toolbar-table-main-partial')
    @include('livewire.admin.user-management.user-toolbar-table-partial')
</div>
