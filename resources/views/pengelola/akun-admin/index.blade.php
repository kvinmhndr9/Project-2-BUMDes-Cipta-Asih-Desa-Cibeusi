@extends('layouts.dashboard')

@section('title', 'Kelola Akun Admin')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fs-4 fw-semibold mb-1">Kelola Akun Admin</h4>
        <p class="text-muted small mb-0">Kelola akun petugas Admin per wisata</p>
    </div>
    <a href="{{ route('pengelola.akun-admin.create') }}" class="btn btn-primary fw-semibold px-4 rounded-pill shadow-sm">
        <i class="bi bi-person-plus-fill me-2"></i>Tambah Admin
    </a>
</div>

<div class="card shadow-sm border-0 d-none d-md-block">
    <div class="card-body p-0 p-lg-4">
        @if($admins->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted opacity-50"></i>
                <p class="text-muted mt-3 mb-0">Belum ada akun admin. Klik <strong>Tambah Admin</strong> untuk membuat akun baru.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-3" style="width: 40px;">No</th>
                        <th class="text-uppercase text-secondary small px-3">Nama</th>
                        <th class="text-uppercase text-secondary small px-3">Email</th>
                        <th class="text-uppercase text-secondary small px-3">Wisata</th>
                        <th class="text-uppercase text-secondary small px-3 text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $i => $admin)
                    <tr>
                        <td class="px-3 text-muted" data-label="No">{{ $i + 1 }}</td>
                        <td class="px-3 fw-semibold" data-label="Nama">{{ $admin->name }}</td>
                        <td class="px-3 text-muted" data-label="Email">{{ $admin->email }}</td>
                        <td class="px-3" data-label="Wisata">
                            @if($admin->wisata)
                                <span class="badge rounded-pill text-bg-success fw-medium">
                                    <i class="bi bi-geo-alt-fill me-1"></i>{{ $admin->wisata->nama }}
                                </span>
                            @else
                                <span class="badge rounded-pill text-bg-warning fw-medium">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="px-3 text-center" data-label="Aksi">
                            <div class="d-flex gap-2 justify-content-end justify-content-lg-center flex-wrap">
                                <a href="{{ route('pengelola.akun-admin.edit', $admin) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="{{ $admin->id_user }}"
                                        data-name="{{ $admin->name }}">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Tampilan Mobile (Card) --}}
<div class="d-md-none">
    @forelse($admins as $i => $admin)
    <div class="card border-0 mb-3 overflow-hidden"
         style="border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        {{-- Header --}}
        <div class="card-header border-0 py-3 px-3" style="background: linear-gradient(90deg, #00b4d8 0%, #2d6a4f 100%);">
            <h6 class="fw-bold text-white mb-0">{{ $admin->name }}</h6>
        </div>

        {{-- Body --}}
        <div class="card-body px-3 py-3">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-muted small" style="min-width:70px;">Email</span>
                <span class="small fw-medium text-end text-break">{{ $admin->email }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small" style="min-width:70px;">Wisata</span>
                <div class="text-end">
                    @if($admin->wisata)
                        <span class="badge rounded-pill text-bg-success fw-medium small">{{ $admin->wisata->nama }}</span>
                    @else
                        <span class="badge rounded-pill text-bg-warning fw-medium small">Belum ditentukan</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Aksi --}}
        <div class="card-footer border-0 bg-white px-3 pb-3 pt-0">
            <div class="d-flex gap-2">
                <a href="{{ route('pengelola.akun-admin.edit', $admin) }}"
                   class="btn btn-primary btn-sm flex-fill fw-semibold rounded-pill">
                    Ubah
                </a>
                <button type="button"
                        class="btn btn-outline-danger btn-sm flex-fill fw-semibold rounded-pill"
                        data-bs-toggle="modal"
                        data-bs-target="#modalHapus"
                        data-id="{{ $admin->id_user }}"
                        data-name="{{ $admin->name }}">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            Belum ada akun admin.<br>
            <a href="{{ route('pengelola.akun-admin.create') }}" class="text-primary text-decoration-none mt-2 d-inline-block">Tambah admin</a>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="modalHapusLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus akun admin <strong id="hapusNama"></strong>?</p>
                <p class="text-muted small mt-2 mb-0">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                        <i class="bi bi-trash-fill me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('turbo:load', function () {
    var modalEl = document.getElementById('modalHapus');
    if (!modalEl) return;
    modalEl.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        var id   = btn.getAttribute('data-id');
        var name = btn.getAttribute('data-name');
        document.getElementById('hapusNama').textContent = name;
        document.getElementById('formHapus').action = '/pengelola/akun-admin/' + id;
    });
});
</script>
@endpush
