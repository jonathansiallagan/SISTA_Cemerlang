<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Bagan Akun (Chart of Accounts)</h1>
        </div>
        <a href="{{ route('akun.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Akun
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
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Akun</th>
                    <th class="px-6 py-4">Tipe</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($akuns as $akun)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-blue-600">{{ $akun->kode_akun }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $akun->nama_akun }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $akun->tipe_akun }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="{{ $akun->status == 'aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            {{ $akun->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <a href="{{ route('akun.edit', $akun->id) }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('akun.destroy', $akun->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        Belum ada data akun perkiraan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>