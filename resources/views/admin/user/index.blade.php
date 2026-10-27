<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Data Pengguna
            </h1>
        </div>
        <a href="{{ route('user.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah User
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Cabang</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->username ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->role }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->cabang->nama ?? 'Pusat/Semua Cabang' }}</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <a href="{{ route('user.edit', $user->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>