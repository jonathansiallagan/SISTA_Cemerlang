
<x-admin-layout-stisla>
    <div class="section-header">
        <h1>Tambah Bahan Baku</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">Persediaan</div>
            <div class="breadcrumb-item">
                <a href="{{ route('bahan-baku.index') }}">Bahan Baku</a>
            </div>
            <div class="breadcrumb-item active">Tambah</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-boxes mr-2"></i>Form Bahan Baku
                </h4>
            </div>

            <form action="{{ route('bahan-baku.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Periksa kembali data berikut:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Daftarkan bahan baku terlebih dahulu sebelum mencatat pembelian.
                        Stok dan harga rata-rata akan diperbarui dari transaksi pembelian.
                    </div>
                    
                    <div class="form-group">
                        <label for="kode_bahan">Kode Bahan</label>
                        <input
                            type="text"
                            name="kode_bahan"
                            id="kode_bahan"
                            class="form-control"
                            value="{{ $kodeBahan }}"
                            readonly
                        >
                        <small class="text-muted">
                            Kode bahan dibuat otomatis oleh sistem.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="nama_bahan">Nama Bahan <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="nama_bahan"
                            id="nama_bahan"
                            class="form-control @error('nama_bahan') is-invalid @enderror"
                            value="{{ old('nama_bahan') }}"
                            placeholder="Contoh: Kertas Art Paper"
                            maxlength="255"
                            required
                        >
                        @error('nama_bahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="spesifikasi">Spesifikasi</label>
                        <input
                            type="text"
                            name="spesifikasi"
                            id="spesifikasi"
                            class="form-control @error('spesifikasi') is-invalid @enderror"
                            value="{{ old('spesifikasi') }}"
                            placeholder="Contoh: 260 gsm, ukuran A3"
                            maxlength="255"
                        >
                        @error('spesifikasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="satuan">Satuan <span class="text-danger">*</span></label>
                        <select
                            name="satuan"
                            id="satuan"
                            class="form-control @error('satuan') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Pilih Satuan --</option>
                            @foreach (['lembar', 'rim', 'pcs', 'roll', 'meter', 'm2', 'kg', 'liter', 'botol', 'pak'] as $satuan)
                                <option value="{{ $satuan }}" @selected(old('satuan') === $satuan)>
                                    {{ ucfirst($satuan) }}
                                </option>
                            @endforeach
                        </select>
                        @error('satuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select
                            name="status"
                            id="status"
                            class="form-control @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-right">
                    <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Simpan Bahan Baku
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout-stisla>
