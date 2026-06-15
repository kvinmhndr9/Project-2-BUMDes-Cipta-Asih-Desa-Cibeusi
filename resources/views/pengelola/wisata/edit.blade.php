@extends('layouts.app')

@section('title', 'Ubah Tempat Wisata')

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.wisata.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Wisata
    </a>
</div>

<h4 class="fs-4 fw-semibold mb-4">Ubah Tempat Wisata</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('pengelola.wisata.update', $wisata) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-medium">Nama Wisata <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $wisata->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="harga_tiket" class="form-label fw-medium">Harga Tiket Kunjungan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_tiket" id="harga_tiket" value="{{ old('harga_tiket', $wisata->harga_tiket) }}" min="0" class="form-control @error('harga_tiket') is-invalid @enderror" required>
                        @error('harga_tiket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga_camping" class="form-label fw-medium">Harga Tiket Camping (Rp) <span class="text-muted fw-normal small">— biarkan 0 jika tidak ada opsi camping</span></label>
                        <input type="number" name="harga_camping" id="harga_camping" value="{{ old('harga_camping', $wisata->harga_camping ?? 0) }}" min="0" class="form-control @error('harga_camping') is-invalid @enderror" placeholder="Contoh: 25000">
                        @error('harga_camping')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="stok" class="form-label fw-medium">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $wisata->stok) }}" min="0" class="form-control @error('stok') is-invalid @enderror" required>
                        @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-medium">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $wisata->deskripsi) }}</textarea>
                        @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="gambar" class="form-label fw-medium">Ganti Gambar Utama (Sampul)</label>
                        @if($wisata->gambar_url)
                        <div class="mb-2">
                            <img src="{{ $wisata->gambar_url }}" alt="{{ $wisata->nama }}" class="rounded shadow-sm" style="height: 120px; width: auto; object-fit: cover;" id="current-gambar">
                            <div class="form-text mt-1 text-muted small">Gambar saat ini.</div>
                        </div>
                        @endif
                        <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg" class="form-control @error('gambar') is-invalid @enderror" onchange="openGlobalCrop(this, { previewContainerId: 'preview-gambar-baru', aspectRatio: 16/9 })">
                        <div class="form-text">Maks. 10MB. Format: jpeg, png, jpg.</div>
                        
                        <div class="mt-2 d-none" id="preview-container-baru">
                            <p class="small text-primary fw-medium mb-1">Pratinjau Gambar Baru:</p>
                            <img id="preview-gambar-baru" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                        @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    {{-- ── Jam Operasional & Tanggal Tutup ──────────────────── --}}
                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3 text-primary"><i class="bi bi-clock me-2"></i>Jam Operasional & Tanggal Tutup</h6>
                    <p class="text-muted small mb-3">Pengunjung <strong>tidak dapat memesan tiket</strong> pada hari/tanggal yang ditandai tutup. Biarkan kosong jika wisata buka setiap hari sepanjang tahun.</p>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-medium small">Jam Buka</label>
                            <input type="time" name="jam_buka" class="form-control" value="{{ old('jam_buka', $wisata->jam_buka ? \Carbon\Carbon::parse($wisata->jam_buka)->format('H:i') : '') }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-medium small">Jam Tutup</label>
                            <input type="time" name="jam_tutup" class="form-control" value="{{ old('jam_tutup', $wisata->jam_tutup ? \Carbon\Carbon::parse($wisata->jam_tutup)->format('H:i') : '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium small">Hari Buka</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                            @php
                                $hariBuka = old('hari_buka', $wisata->hari_buka ?? []);
                                $checked  = in_array($hari, (array) $hariBuka);
                            @endphp
                            <div class="form-check form-check-inline me-0">
                                <input class="form-check-input" type="checkbox" name="hari_buka[]"
                                       id="hari_{{ $hari }}" value="{{ $hari }}"
                                       {{ $checked ? 'checked' : '' }}>
                                <label class="form-check-label small" for="hari_{{ $hari }}">{{ $hari }}</label>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-text">Jika tidak ada yang dicentang, wisata dianggap buka setiap hari.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium small">Tanggal Tutup Khusus <span class="text-muted">(hari libur, event, dll.)</span></label>
                        <div id="tanggal-tutup-container">
                            @php $tanggalTutup = old('tanggal_tutup', $wisata->tanggal_tutup ?? []); @endphp
                            @forelse((array)$tanggalTutup as $tgl)
                            <div class="input-group mb-2 tanggal-tutup-row">
                                <input type="date" name="tanggal_tutup[]" class="form-control" value="{{ $tgl }}">
                                <button type="button" class="btn btn-outline-danger btn-hapus-tanggal"><i class="bi bi-trash3"></i></button>
                            </div>
                            @empty
                            <div class="input-group mb-2 tanggal-tutup-row">
                                <input type="date" name="tanggal_tutup[]" class="form-control">
                                <button type="button" class="btn btn-outline-danger btn-hapus-tanggal"><i class="bi bi-trash3"></i></button>
                            </div>
                            @endforelse
                        </div>
                        <button type="button" id="btn-tambah-tanggal" class="btn btn-sm btn-outline-secondary mt-1">
                            <i class="bi bi-plus me-1"></i> Tambah Tanggal
                        </button>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                        <a href="{{ route('pengelola.wisata.index') }}" class="btn btn-light px-4 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SEKSI GALERI FOTO                                               --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-4">
    <div class="col-12 col-lg-10">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Galeri Foto</h5>
                <p class="text-muted small mb-4">
                    Foto dari ulasan pengunjung otomatis masuk ke sini dan <strong>tidak dapat dihapus</strong>.
                    Foto yang diunggah Pengelola dapat dihapus kapan saja.
                </p>

                {{-- Form Upload Foto Baru --}}
                <form action="{{ route('pengelola.wisata.gallery.store', $wisata) }}"
                      method="POST" enctype="multipart/form-data"
                      class="bg-light rounded-3 p-3 mb-4 border">
                    @csrf
                    <p class="fw-medium small text-secondary mb-2">
                        <i class="bi bi-cloud-upload me-1"></i> Tambah Foto ke Galeri
                    </p>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-5">
                            <label class="form-label small fw-medium">Pilih Foto <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control form-control-sm" required accept="image/jpeg,image/png,image/jpg" onchange="openGlobalCrop(this, { previewContainerId: 'preview-gallery-baru' })">
                            <div class="mt-2 d-none">
                                <img id="preview-gallery-baru" src="" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <label class="form-label small fw-medium">Keterangan <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="text" name="caption" class="form-control form-control-sm" placeholder="Contoh: Pemandangan sore hari">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-upload me-1"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Grid Galeri --}}
                @php $galleries = $wisata->galleries ?? []; @endphp
                @if(count($galleries) === 0)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-images fs-2 opacity-50 d-block mb-2"></i>
                        Belum ada foto di galeri.
                    </div>
                @else
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @foreach($galleries as $index => $gallery)
                    <div class="col">
                        <div class="position-relative border rounded-3 overflow-hidden bg-light" style="height: 150px;">
                            <img src="{{ Storage::url($gallery['image']) }}"
                                 class="w-100 h-100"
                                 style="object-fit: cover;"
                                 alt="{{ $gallery['caption'] ?? 'Foto galeri' }}">
                            {{-- Badge sumber --}}
                            @if(($gallery['source'] ?? 'pengelola') === 'review')
                                <span class="badge text-bg-info position-absolute top-0 start-0 m-1 small fw-medium">
                                    <i class="bi bi-person-fill me-1"></i>Review
                                </span>
                            @else
                                <span class="badge text-bg-success position-absolute top-0 start-0 m-1 small fw-medium">
                                    <i class="bi bi-person-badge-fill me-1"></i>Pengelola
                                </span>
                                {{-- Tombol hapus hanya untuk foto pengelola --}}
                                <button type="button"
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 btn-hapus-foto"
                                        style="width:28px; height:28px; padding:0; font-size:0.75rem;"
                                        data-action="{{ route('pengelola.wisata.gallery.destroy', ['wisata' => $wisata->id_wisata, 'index' => $index]) }}"
                                        title="Hapus foto ini">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @endif
                        </div>
                        @if(!empty($gallery['caption']))
                            <div class="small text-muted mt-1 text-truncate px-1" title="{{ $gallery['caption'] }}">
                                {{ $gallery['caption'] }}
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Foto --}}
<div class="modal fade" id="modalHapusFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width:64px;height:64px;">
                        <i class="bi bi-image text-danger fs-3"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1">Hapus Foto?</h5>
                <p class="text-muted mb-4">Foto ini akan dihapus dari galeri secara permanen.</p>
                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                    <button type="button" class="btn btn-light px-4 fw-medium rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapusFoto" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger px-4 fw-medium rounded-pill">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<x-crop-modal />
@endsection

@push('scripts')
<script>
document.addEventListener('turbo:load', function () {
    var container = document.getElementById('tanggal-tutup-container');
    var btnTambah = document.getElementById('btn-tambah-tanggal');

    if (btnTambah) {
        btnTambah.addEventListener('click', function () {
            var row = document.createElement('div');
            row.className = 'input-group mb-2 tanggal-tutup-row';
            row.innerHTML = '<input type="date" name="tanggal_tutup[]" class="form-control"><button type="button" class="btn btn-outline-danger btn-hapus-tanggal"><i class="bi bi-trash3"></i></button>';
            container.appendChild(row);
            bindHapus(row.querySelector('.btn-hapus-tanggal'));
        });
    }

    function bindHapus(btn) {
        btn.addEventListener('click', function () {
            var rows = container.querySelectorAll('.tanggal-tutup-row');
            if (rows.length > 1) {
                this.closest('.tanggal-tutup-row').remove();
            } else {
                this.closest('.tanggal-tutup-row').querySelector('input').value = '';
            }
        });
    }

    container && container.querySelectorAll('.btn-hapus-tanggal').forEach(bindHapus);

    // Tombol hapus foto galeri
    document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
        btn.addEventListener('click', function () {
            document.getElementById('formHapusFoto').action = this.dataset.action;
            new bootstrap.Modal(document.getElementById('modalHapusFoto')).show();
        });
    });
});
</script>
@endpush
