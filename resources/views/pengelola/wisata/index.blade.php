@extends('layouts.dashboard')

@section('title', 'Kelola Tempat Wisata')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <h4 class="fs-4 fw-semibold mb-0">Kelola Tempat Wisata &amp; Harga Tiket</h4>
    <a href="{{ route('pengelola.wisata.create') }}" class="btn btn-primary d-inline-flex align-items-center fw-medium">
        <i class="bi bi-plus-lg me-2"></i> Tambah Wisata
    </a>
</div>

{{-- Tampilan Desktop (Tabel) --}}
<div class="card shadow-sm border-0 d-none d-md-block">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-4 py-3">Wisata</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Harga Tiket</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Jam Buka</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Hari Buka</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Deskripsi</th>
                        <th class="text-uppercase text-secondary small px-4 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($wisata as $w)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-medium text-dark">{{ $w->nama }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($w->hasCamping())
                                <div class="small">Kunjungan: Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}</div>
                                <div class="small">Camping: Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}</div>
                            @else
                                Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 small">
                            @if($w->jam_buka && $w->jam_tutup)
                                <span class="text-dark fw-medium">
                                    {{ substr($w->jam_buka, 0, 5) }} &ndash; {{ substr($w->jam_tutup, 0, 5) }} WIB
                                </span>
                            @else
                                <span class="text-muted fst-italic">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3" style="min-width: 180px;">
                            @php $hariBuka = $w->hari_buka ?? []; @endphp
                            @if(count($hariBuka) > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($hariBuka as $hari)
                                        <span class="badge text-bg-light border small fw-normal">{{ Str::limit($hari, 3, '') }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted fst-italic small">Setiap hari</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 small text-muted">{{ Str::limit($w->deskripsi, 40) }}</td>
                        <td class="px-4 py-3 text-end text-nowrap">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('pengelola.wisata.edit', $w) }}" class="btn btn-sm btn-outline-primary fw-medium">Ubah</a>
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger fw-medium btn-hapus"
                                    data-nama="{{ $w->nama }}"
                                    data-action="{{ route('pengelola.wisata.destroy', $w) }}">
                                    Hapus
                                </button>
                            </div>
                        </td>
                     </tr>
                     @empty
                     <tr>
                         <td colspan="4" class="px-4 py-5 text-center text-muted">
                             Belum ada data wisata. <a href="{{ route('pengelola.wisata.create') }}" class="text-primary text-decoration-none">Tambah wisata</a>
                         </td>
                     </tr>
                     @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tampilan Mobile (Card) --}}
<div class="d-md-none">
    @forelse($wisata as $w)
    <div class="card border-0 mb-3 overflow-hidden wisata-card"
         style="border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: box-shadow 0.2s;">
        {{-- Header Card --}}
        <div class="card-header border-0 py-3 px-3" style="background: linear-gradient(90deg, #00b4d8 0%, #2d6a4f 100%);">
            <h6 class="fw-bold text-white mb-0">{{ $w->nama }}</h6>
        </div>

        {{-- Body Info --}}
        <div class="card-body px-3 py-3">
            {{-- Baris: Harga --}}
            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                <span class="text-muted small" style="min-width:90px;">Harga Tiket</span>
                <div class="text-end fw-medium small">
                    @if($w->hasCamping())
                        <div>Kunjungan: Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}</div>
                        <div>Camping: Rp {{ number_format($w->harga_camping_efektif, 0, ',', '.') }}</div>
                    @else
                        Rp {{ number_format($w->harga_tiket, 0, ',', '.') }}
                    @endif
                </div>
            </div>

            {{-- Baris: Jam Buka --}}
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-muted small" style="min-width:90px;">Jam Buka</span>
                <span class="small fw-medium text-end">
                    @if($w->jam_buka && $w->jam_tutup)
                        {{ substr($w->jam_buka, 0, 5) }} &ndash; {{ substr($w->jam_tutup, 0, 5) }} WIB
                    @else
                        <span class="text-muted fst-italic">Tidak diatur</span>
                    @endif
                </span>
            </div>

            {{-- Baris: Hari Buka --}}
            <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                <span class="text-muted small" style="min-width:90px;">Hari Buka</span>
                <div class="text-end">
                    @php $hariBuka = $w->hari_buka ?? []; @endphp
                    @if(count($hariBuka) > 0)
                        <div class="d-flex flex-wrap gap-1 justify-content-end">
                            @foreach($hariBuka as $hari)
                                <span class="badge text-bg-light border fw-normal" style="font-size:0.7rem;">{{ Str::limit($hari, 3, '') }}</span>
                            @endforeach
                        </div>
                    @else
                        <span class="small text-muted">Setiap hari</span>
                    @endif
                </div>
            </div>

            {{-- Deskripsi --}}
            @if($w->deskripsi)
            <p class="small text-muted mb-0">{{ Str::limit($w->deskripsi, 90) }}</p>
            @endif
        </div>

        {{-- Footer Aksi --}}
        <div class="card-footer border-0 bg-white px-3 pb-3 pt-0">
            <div class="d-grid gap-2 d-flex">
                <a href="{{ route('pengelola.wisata.edit', $w) }}"
                   class="btn btn-primary btn-sm flex-fill fw-semibold rounded-pill">
                    Ubah
                </a>
                <button type="button"
                    class="btn btn-outline-danger btn-sm flex-fill fw-semibold rounded-pill btn-hapus"
                    data-nama="{{ $w->nama }}"
                    data-action="{{ route('pengelola.wisata.destroy', $w) }}">
                    Hapus
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center text-muted py-5">
            Belum ada data wisata.<br>
            <a href="{{ route('pengelola.wisata.create') }}" class="text-primary text-decoration-none mt-2 d-inline-block">Tambah wisata</a>
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width:64px;height:64px;">
                        <i class="bi bi-trash3 text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Wisata?</h5>
                <p class="text-muted mb-4">Data wisata <strong id="namaWisata"></strong> akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapus" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-medium rounded-pill">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('namaWisata').textContent = this.dataset.nama;
        document.getElementById('formHapus').action = this.dataset.action;
        new bootstrap.Modal(document.getElementById('modalHapus')).show();
    });
});
</script>
@endpush
@endsection
