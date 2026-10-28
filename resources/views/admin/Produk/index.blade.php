
<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Produk &amp; Layanan</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Produk &amp; Layanan</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="row align-items-center mb-3">
            <div class="col-md-7">
                <p class="text-muted mb-0">
                    Data Produk &amp; Layanan
                </p>
            </div>

            <div class="col-md-5 text-md-right mt-1 mt-md-0">
                <a href="{{ route('produk.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}

                <button type="button" class="close"
                        data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif


    {{-- TABEL PRODUK --}}
    <div class="card">
        <div class="card-header bg-primary py-2">
            <h4 class="text-white mb-0">
                <i class="fas fa-box mr-2"></i>
                Data Produk &amp; Layanan
            </h4>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sista">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Nama Produk/Layanan</th>
                            <th>Satuan</th>
                            <th class="text-right">Harga Jual</th>
                            <th class="text-right">HPP</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($produks as $produk)
                            <tr>
                                {{-- NOMOR URUT --}}
                                <td class="text-center text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- NAMA PRODUK --}}
                                <td class="font-weight-bold">
                                    {{ $produk->nama_produk }}
                                </td>

                                {{-- SATUAN --}}
                                <td>
                                    {{ $produk->satuan }}
                                </td>

                                {{-- HARGA JUAL --}}
                                <td class="text-right text-nowrap font-weight-bold text-success">
                                    Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                                </td>

                                {{-- HPP --}}
                                <td class="text-right text-nowrap">
                                    Rp {{ number_format($produk->hpp, 0, ',', '.') }}
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('produk.edit', $produk->id) }}"
                                    class="btn btn-primary btn-sm"
                                    title="Edit Produk">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('produk.destroy', $produk->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                title="Hapus Produk">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <p class="mb-0">
                                        Belum ada data produk.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    </div>
</x-admin-layout-stisla>
