
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Bagan Akun (Chart of Accounts)</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Akuntansi</div>
            <div class="breadcrumb-item">Bagan Akun</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar akun perkiraan untuk pencatatan transaksi dan laporan keuangan CV Cemerlang.
            </p>

            <a href="{{ route('akun.create') }}"
               class="btn btn-primary btn-sm mb-2">
                <i class="fas fa-plus mr-1"></i>
                Tambah Akun
            </a>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Terdapat kesalahan pada proses yang dilakukan.

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL BAGAN AKUN --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-book mr-2"></i>
                    Daftar Akun Perkiraan
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="17%">Kode Akun</th>
                                <th width="30%">Nama Akun</th>
                                <th width="20%">Tipe Akun</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($akuns as $akun)
                                <tr>
                                    {{-- NOMOR URUT --}}
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- KODE AKUN --}}
                                    <td class="align-middle">
                                        <strong class="text-primary">
                                            {{ $akun->kode_akun }}
                                        </strong>
                                    </td>

                                    {{-- NAMA AKUN --}}
                                    <td class="align-middle font-weight-bold">
                                        {{ $akun->nama_akun }}
                                    </td>

                                    {{-- TIPE AKUN --}}
                                    <td class="align-middle">
                                        {{ $akun->tipe_akun }}
                                    </td>

                                    {{-- STATUS AKUN --}}
                                    <td class="text-center align-middle">
                                        @if (strtolower($akun->status) === 'aktif')
                                            <span class="badge badge-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center align-middle text-nowrap">
                                        <a href="{{ route('akun.edit', $akun->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Edit Akun">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-book fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data akun perkiraan.
                                            Silakan tambah akun baru.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER TABEL --}}
            <div class="card-footer text-muted">
                <small>
                    Total akun:
                    <strong>{{ $akuns->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

</x-admin-layout-stisla>
