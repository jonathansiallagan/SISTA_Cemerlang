<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Pengguna Baru</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('user.store') }}" method="POST">
            
            @if ($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-4"
                    role="alert"
                    style="border-radius: 12px; border: 1px solid #fecaca;
                            background: #fef2f2; color: #991b1b; padding: 14px 16px;">

                    <i class="fas fa-exclamation-circle mt-1"></i>

                    <div>
                        <strong>Gagal menyimpan pengguna</strong>
                        <div class="small mt-1">
                            {{ $errors->first() }}
                        </div>
                    </div>
                </div>
            @endif

            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="Admin Pusat">Admin Pusat</option>
                        <option value="Admin Cabang">Admin Cabang</option>
                        <option value="Customer Service">Customer Service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penempatan Cabang</label>
                    <select name="cabang_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Pusat (Tidak ada cabang spesifik) --</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">Simpan Pengguna</button>
        </form>
    </div>
</x-admin-layout>