
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Pengguna</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Manajemen Pengguna</div>
            <div class="breadcrumb-item">Data Pengguna</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola data pengguna dan hak akses sistem informasi akuntansi CV Cemerlang.
            </p>

            <a href="{{ route('user.create') }}"
               class="btn btn-primary btn-sm mb-2">
                <i class="fas fa-plus mr-1"></i>
                Tambah User
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

        {{-- TABEL DATA PENGGUNA --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-users mr-2"></i>
                    Daftar Pengguna
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="22%">Nama</th>
                                <th width="18%">Username</th>
                                <th width="15%">Role</th>
                                <th width="25%">Cabang</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    {{-- NOMOR URUT --}}
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- NAMA --}}
                                    <td class="align-middle font-weight-bold">
                                        {{ $user->name }}
                                    </td>

                                    {{-- USERNAME --}}
                                    <td class="align-middle">
                                        {{ $user->username ?? '-' }}
                                    </td>

                                    {{-- ROLE --}}
                                    <td class="align-middle">
                                        @php
                                            $role = strtolower($user->role ?? '');
                                        @endphp

                                        @if ($role === 'admin pusat')
                                            <span class="badge badge-primary">
                                                {{ $user->role }}
                                            </span>
                                        @elseif ($role === 'admin cabang')
                                            <span class="badge badge-info">
                                                {{ $user->role }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ $user->role ?? '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- CABANG --}}
                                    <td class="align-middle">
                                        {{ $user->cabang->nama ?? 'Pusat/Semua Cabang' }}
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center align-middle text-nowrap">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('user.edit', $user->id) }}"
                                               class="btn btn-primary btn-sm mr-1"
                                               title="Edit Pengguna">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form
                                                action="{{ route('user.destroy', $user->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus Pengguna">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data pengguna.
                                            Silakan tambah pengguna baru.
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
                    Total pengguna:
                    <strong>{{ $users->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

</x-admin-layout-stisla>
