<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Cabang</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Data Cabang</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar cabang percetakan CV Cemerlang.
            </p>

            <a href="{{ route('cabang.create') }}"
               class="btn btn-primary btn-sm mb-2">
                <i class="fas fa-plus mr-1"></i>
                Tambah Cabang
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

        {{-- TABEL DATA CABANG --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-store mr-2"></i>
                    Daftar Cabang
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama Cabang</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th class="text-center">Status Cabang</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($cabangs as $cabang)
                                <tr>
                                    {{-- NOMOR URUT --}}
                                    <td class="text-center text-muted align-middle">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- NAMA CABANG --}}
                                    <td class="font-weight-bold align-middle">
                                        {{ $cabang->nama }}
                                    </td>

                                    {{-- ALAMAT --}}
                                    <td class="align-middle">
                                        {{ $cabang->alamat ?: '-' }}
                                    </td>

                                    {{-- NOMOR HP --}}
                                    <td class="align-middle text-nowrap">
                                        {{ $cabang->no_hp ?: '-' }}
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="text-center align-middle">
                                        @if (strtolower($cabang->status) === 'aktif')
                                            <span class="badge badge-success">
                                                {{ $cabang->status }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ $cabang->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center align-middle text-nowrap">
                                        <a href="{{ route('cabang.edit', $cabang->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Edit Cabang">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-store fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data cabang. Silakan tambah cabang baru.
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