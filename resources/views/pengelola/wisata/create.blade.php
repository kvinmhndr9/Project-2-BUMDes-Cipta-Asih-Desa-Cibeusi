@extends('layouts.app')

@section('title', 'Tambah Tempat Wisata')

@section('content')
<div class="mb-4">
    <a href="{{ route('pengelola.wisata.index') }}" class="text-primary text-decoration-none small fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Wisata
    </a>
</div>

<h4 class="fs-4 fw-semibold mb-4">Tambah Tempat Wisata</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('pengelola.wisata.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-medium">Nama Wisata <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="harga_tiket" class="form-label fw-medium">Harga Tiket Kunjungan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="harga_tiket" id="harga_tiket" value="{{ old('harga_tiket') }}" min="0" class="form-control @error('harga_tiket') is-invalid @enderror" required>
                        @error('harga_tiket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="harga_camping" class="form-label fw-medium">Harga Tiket Camping (Rp) <span class="text-muted fw-normal small">— biarkan 0 jika tidak ada opsi camping</span></label>
                        <input type="number" name="harga_camping" id="harga_camping" value="{{ old('harga_camping', 0) }}" min="0" class="form-control @error('harga_camping') is-invalid @enderror" placeholder="Contoh: 25000">
                        @error('harga_camping')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="stok" class="form-label fw-medium">Stok <span class="text-danger">*</span></label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', 100) }}" min="0" class="form-control @error('stok') is-invalid @enderror" required>
                        @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-medium">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="gambar" class="form-label fw-medium">Gambar Utama (Sampul) <span class="text-danger">*</span></label>
                        <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg" class="form-control @error('gambar') is-invalid @enderror" onchange="openGlobalCrop(this, { previewContainerId: 'preview-gambar', aspectRatio: 16/9 })" required>
                        <div class="form-text">Maks. 10MB. Format: jpeg, png, jpg.</div>
                        <div class="mt-2 d-none">
                            <p class="small text-muted mb-1">Preview Foto:</p>
                            <img id="preview-gambar" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                        @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Simpan</button>
                        <a href="{{ route('pengelola.wisata.index') }}" class="btn btn-light px-4 fw-medium">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<x-crop-modal />
@endsection
