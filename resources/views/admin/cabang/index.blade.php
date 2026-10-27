<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Data Cabang
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola daftar cabang percetakan CV Cemerlang.
            </p>
        </div>
        <a href="{{ route('cabang.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Cabang
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
                    <th class="px-6 py-4">No</th>
                    <th class="px-6 py-4">Nama Cabang</th>
                    <th class="px-6 py-4">Alamat</th>
                    <th class="px-6 py-4">No. HP</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($cabangs as $index => $cabang)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $cabang->nama }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $cabang->alamat }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $cabang->no_hp }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                            {{ $cabang->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a
                            href="{{ route('cabang.edit', $cabang->id) }}"
                            class="text-blue-500 hover:text-blue-700 mx-1"
                            title="Edit Cabang"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        Belum ada data cabang. Silakan tambah cabang baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>