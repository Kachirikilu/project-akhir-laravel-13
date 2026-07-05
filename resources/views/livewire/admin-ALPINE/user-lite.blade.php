<div class="p-6">
    <div class="mb-4">
        <input type="text" wire:model="search" placeholder="Cari ID atau Email..."
            class="w-full p-2 border rounded shadow-sm focus:ring focus:ring-blue-200">
    </div>

    <div class="overflow-x-auto border rounded">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">NIP/NIM</th>
                    <th class="px-4 py-2 text-left">NITK/NIDN</th>
                    <th class="px-4 py-2 text-left">NIDK</th>
                    <th class="px-4 py-2 text-left">Prodi</th>
                    <th class="px-4 py-2 text-left">Status</th>

            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->id ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->email ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->name ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->identity1 ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->identity2 ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->identity3 ?? '' }}</td>
                        <td class="text-xs sm:text-sm px-4 py-2">
                            {{ $user->admin->pr_rel?->prodi ?? ($user->dosen->pr_rel?->prodi ?? ($user->mahasiswa->pr_rel?->prodi ?? '')) }}
                        </td>
                        <td class="text-xs sm:text-sm px-4 py-2">{{ $user->status ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-10 text-center text-gray-500">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
