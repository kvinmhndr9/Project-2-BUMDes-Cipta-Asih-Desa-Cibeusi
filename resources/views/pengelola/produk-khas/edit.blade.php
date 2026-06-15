@extends('layouts.app')

@section('title', 'Ubah Produk Khas')

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.produk-khas.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Produk Khas
    </a>
</div>

<h4 class="fs-4 fw-semibold mb-4">Ubah Produk Khas</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('pengelola.produk-khas.update', $produkKhas) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-medium">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $produkKhas->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="id_wisata" class="form-label fw-medium">Asal Tempat Wisata</label>
                        <select name="id_wisata" id="id_wisata" class="form-select @error('id_wisata') is-invalid @enderror">
                            <option value="">-- Tidak Terikat Wisata Tertentu --</option>
                            @foreach($wisataList as $w)
                                <option value="{{ $w->id }}" {{ old('id_wisata', $produkKhas->id_wisata) == $w->id ? 'selected' : '' }}>{{ $w->nama }}</option>
                            @endforeach
                        </select>
                        @error('id_wisata')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-medium">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $produkKhas->keterangan) }}</textarea>
                        @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="urutan" class="form-label fw-medium">Urutan tampil</label>
                        <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $produkKhas->urutan) }}" min="0" class="form-control @error('urutan') is-invalid @enderror">
                        @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="gambar" class="form-label fw-medium">Ganti Gambar Produk</label>
                        @if($produkKhas->gambar_url)
                        <div class="mb-2">
                            <img src="{{ $produkKhas->gambar_url }}" alt="{{ $produkKhas->nama }}" class="rounded shadow-sm" style="height: 120px; width: auto; object-fit: cover;" id="current-gambar">
                            <div class="form-text mt-1 text-muted small">Gambar saat ini.</div>
                        </div>
                        @endif
                        <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg" class="form-control @error('gambar') is-invalid @enderror" onchange="openGlobalCrop(this, { previewContainerId: 'preview-gambar-baru', aspectRatio: 1 })">
                        <div class="form-text">Maks. 10MB. Format: jpeg, png, jpg.</div>
                        
                        <div class="mt-2 d-none" id="preview-container-baru">
                            <p class="small text-primary fw-medium mb-1">Pratinjau Gambar Baru:</p>
                            <img id="preview-gambar-baru" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                        @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan Perubahan</button>
                        <a href="{{ route('pengelola.produk-khas.index') }}" class="btn btn-light px-4 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SEKSI GALERI FOTO PRODUK KHAS                                   --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="row mt-4">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-1">Galeri Foto</h5>
                <p class="text-muted small mb-4">Tambah atau hapus foto galeri produk khas ini.</p>

                {{-- Form Upload Foto Baru --}}
                <form action="{{ route('pengelola.produk-khas.gallery.store', $produkKhas) }}"
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
                            <input type="text" name="caption" class="form-control form-control-sm" placeholder="Contoh: Produk unggulan">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-upload me-1"></i> Upload
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Grid Galeri --}}
                @php $galleries = $produkKhas->galleries ?? []; @endphp
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
                            <button type="button"
                                    class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 btn-hapus-foto"
                                    style="width:28px; height:28px; padding:0; font-size:0.75rem;"
                                    data-action="{{ route('pengelola.produk-khas.gallery.destroy', ['produkKhas' => $produkKhas->id, 'index' => $index]) }}"
                                    title="Hapus foto ini">
                                <i class="bi bi-x-lg"></i>
                            </button>
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
    document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
        btn.addEventListener('click', function () {
            document.getElementById('formHapusFoto').action = this.dataset.action;
            new bootstrap.Modal(document.getElementById('modalHapusFoto')).show();
        });
    });
});
</script>
@endpush

