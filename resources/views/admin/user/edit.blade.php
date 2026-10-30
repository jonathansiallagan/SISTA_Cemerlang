<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Ubah Data Pengguna</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="Admin Pusat" {{ $user->role == 'Admin Pusat' ? 'selected' : '' }}>Admin Pusat</option>
                        <option value="Admin Cabang" {{ $user->role == 'Admin Cabang' ? 'selected' : '' }}>Admin Cabang</option>
                        <option value="Customer Service" {{ $user->role == 'Customer Service' ? 'selected' : '' }}>Customer Service</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penempatan Cabang</label>
                    <select name="cabang_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">-- Pusat (Tidak ada cabang spesifik) --</option>
                        @foreach($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" {{ $user->cabang_id == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">Perbarui Pengguna</button>
                <a href="{{ route('user.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg font-medium">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>