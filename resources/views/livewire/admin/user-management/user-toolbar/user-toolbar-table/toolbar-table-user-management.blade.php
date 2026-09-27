<div>
    @include('livewire.global.table.text-copy', [
        'xType' => $data['identity1'],
        'typeXString' => $data['label_id1'] . ' ' . $data['role'],
    ])
    @include('livewire.admin.user-management.user-toolbar.user-toolbar-table.user-toolbar-table-main')
    @include('livewire.admin.user-management.user-toolbar.user-toolbar-table.user-toolbar-table-second')
</div>
