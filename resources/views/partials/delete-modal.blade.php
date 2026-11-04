<!-- Modal Konfirmasi Hapus Data -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-trash3 text-danger" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Apakah Anda yakin ingin menghapus data ini?</h6>
                <p class="text-muted small mb-0">
                    Data <strong id="deleteConfirmItemName" class="text-danger">item yang dipilih</strong> akan dihapus dari tabel tampilan (simulasi tanpa backend). Tindakan ini tidak dapat dibatalkan.
                </p>
                <!-- TODO BACKEND: hubungkan konfirmasi ini ke form DELETE request atau AJAX destroy ke API -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="btnConfirmDelete">
                    <i class="bi bi-trash-fill me-1"></i>Ya, Hapus Data
                </button>
            </div>
        </div>
    </div>
</div>
