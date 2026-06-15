{{-- ── MODAL CROP FOTO GLOBAL ──────────────────────────── --}}
<div class="modal fade" id="globalCropModal" tabindex="-1" aria-labelledby="globalCropModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-lg border-0" style="border-radius: 1rem; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(90deg, #00b4d8, #2d6a4f); color: white; border: none;">
                <h5 class="modal-title fw-bold" id="globalCropModalLabel">
                    <i class="bi bi-crop me-2"></i>Sesuaikan Foto
                </h5>
                <button type="button" class="btn-close" onclick="cancelGlobalCrop()" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body p-0" style="background:#111827;">
                <div style="max-height: 50vh; overflow: hidden; padding: 1.25rem;">
                    <img id="globalCropModalImage" src="" alt="Crop Preview" style="display:block; max-width:100%;">
                </div>
                {{-- Kontrol zoom & rotate --}}
                <div class="crop-controls d-flex justify-content-center gap-2 py-3 px-3" style="background:#1f2937;">
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="globalCropper.zoom(0.1)" title="Zoom In"><i class="bi bi-zoom-in"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="globalCropper.zoom(-0.1)" title="Zoom Out"><i class="bi bi-zoom-out"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="globalCropper.rotate(-90)" title="Putar Kiri"><i class="bi bi-arrow-counterclockwise"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="globalCropper.rotate(90)" title="Putar Kanan"><i class="bi bi-arrow-clockwise"></i></button>
                    <button type="button" class="btn btn-sm btn-outline-light" onclick="globalCropper.reset()" title="Reset"><i class="bi bi-arrow-repeat"></i></button>
                </div>
            </div>
            <div class="modal-footer border-0" style="background:#1f2937;">
                <button type="button" class="btn rounded-pill px-4 fw-semibold" onclick="cancelGlobalCrop()" style="background:#dc2626; color:#fff; border:none;">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold" id="btnApplyGlobalCrop" onclick="applyGlobalCrop()" style="background:linear-gradient(90deg,#00b4d8,#2d6a4f); color:#fff; border:none;">
                    <i class="bi bi-check-circle me-1"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@pushonce('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpushonce

@pushonce('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
let globalCropper = null;
let currentCropInput = null;
let currentCropOptions = {};

// Register a single global hidden event listener on modal to clean up cropper memory/inputs
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('globalCropModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function() {
            if (globalCropper) {
                globalCropper.destroy();
                globalCropper = null;
            }
            if (currentCropInput && !currentCropInput.dataset.hasCropped) {
                currentCropInput.value = '';
            }
        });
    }
});

/**
 * Buka Crop Modal.
 * options:
 * - aspectRatio: NaN (bebas), 1 (kotak), 16/9, dll
 * - previewContainerId: (opsional) elemen img target untuk menampilkan preview
 * - onApply: callback custom setelah crop. Jika tidak ada, pakai default DataTransfer.
 */
function openGlobalCrop(input, options = {}) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    
    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file maksimal 10MB.');
        input.value = '';
        return;
    }

    currentCropInput = input;
    currentCropOptions = Object.assign({
        aspectRatio: NaN,
        previewContainerId: null,
        onApply: null
    }, options);

    const reader = new FileReader();
    reader.onload = function(e) {
        const img = document.getElementById('globalCropModalImage');
        img.src = e.target.result;
        
        if (globalCropper) {
            globalCropper.destroy();
            globalCropper = null;
        }
        
        const modalEl = document.getElementById('globalCropModal');
        
        // Remove previous dynamic shown handler if it exists
        const oldHandler = modalEl._cropShowHandler;
        if (oldHandler) {
            modalEl.removeEventListener('shown.bs.modal', oldHandler);
        }
        
        const handler = function() {
            globalCropper = new Cropper(img, {
                aspectRatio: currentCropOptions.aspectRatio,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        };
        
        modalEl.addEventListener('shown.bs.modal', handler, { once: true });
        modalEl._cropShowHandler = handler;
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    };
    reader.readAsDataURL(file);
}

function cancelGlobalCrop() {
    const modalEl = document.getElementById('globalCropModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

function applyGlobalCrop() {
    if (!globalCropper) return;
    
    const btnApply = document.getElementById('btnApplyGlobalCrop');
    const originalContent = btnApply.innerHTML;
    btnApply.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
    btnApply.disabled = true;

    globalCropper.getCroppedCanvas({
        maxWidth: 1920,
        maxHeight: 1080,
        imageSmoothingQuality: 'high'
    }).toBlob((blob) => {
        if (!blob) {
            alert('Gagal crop foto');
            btnApply.innerHTML = originalContent;
            btnApply.disabled = false;
            return;
        }

        if (typeof currentCropOptions.onApply === 'function') {
            const base64 = globalCropper.getCroppedCanvas({ width: 400, height: 400 }).toDataURL('image/jpeg', 0.9);
            currentCropOptions.onApply(blob, base64);
        } else {
            const ext = blob.type.split('/')[1] || 'jpeg';
            const fileName = 'cropped_image_' + new Date().getTime() + '.' + ext;
            const newFile = new File([blob], fileName, { type: blob.type, lastModified: new Date().getTime() });
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(newFile);
            currentCropInput.files = dataTransfer.files;
            currentCropInput.dataset.hasCropped = 'true';

            if (currentCropOptions.previewContainerId) {
                const previewImg = document.getElementById(currentCropOptions.previewContainerId);
                if (previewImg) {
                    previewImg.src = URL.createObjectURL(blob);
                    if (previewImg.parentElement && previewImg.parentElement.classList.contains('d-none')) {
                        previewImg.parentElement.classList.remove('d-none');
                    }
                }
            }
        }

        const modalEl = document.getElementById('globalCropModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        
        setTimeout(() => {
            btnApply.innerHTML = originalContent;
            btnApply.disabled = false;
        }, 300);
    }, 'image/jpeg', 0.85);
}
</script>
@endpushonce
