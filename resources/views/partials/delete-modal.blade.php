<!-- Modal Konfirmasi Hapus Data -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="deleteConfirmModalLabel">
                    Konfirmasi Hapus Data
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary mb-2" style="font-size: 14px;">
                    Apakah Anda yakin ingin menghapus data <strong id="deleteConfirmItemName" class="text-dark">item yang dipilih</strong>?
                </p>
                <div class="text-danger small">
                    Tindakan ini permanen dan data yang dihapus tidak dapat dikembalikan.
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-3">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="btnConfirmDelete">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
