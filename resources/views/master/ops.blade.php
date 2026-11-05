@extends('layouts.app')

@section('title', 'Master Beban Operasional')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Master Beban Operasional</h1>
        <p class="page-subtitle">Pengelolaan kategori beban biaya operasional agen.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3" id="btnOpenAddOpsModal">
            <i class="bi bi-plus me-1"></i> Tambah Kategori
        </button>
    </div>
</div>

<!-- Card Tabel Master Ops -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Kategori Beban</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="opsTable" placeholder="Cari nama beban, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="opsTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Beban</th>
                    <th>Nama Beban Operasional</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ops as $index => $o)
                <tr data-id="{{ $o['id_ops'] }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $o['id_ops'] }}</span></td>
                    <td class="fw-medium text-dark ops-name-cell">{{ $o['beban_ops'] }}</td>
                    <td><span class="text-secondary">{{ $o['ket_ops'] }}</span></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-ops"
                                    data-id="{{ $o['id_ops'] }}"
                                    data-name="{{ $o['beban_ops'] }}"
                                    data-desc="{{ $o['ket_ops'] }}"
                                    title="Ubah Beban Ops"
                                    aria-label="Ubah Beban Ops">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                    data-id="{{ $o['id_ops'] }}"
                                    data-delete-url="{{ route('master.ops.destroy', $o['id_ops']) }}"
                                    data-item-name="{{ $o['beban_ops'] }} ({{ $o['id_ops'] }})"
                                    title="Hapus Beban Ops"
                                    aria-label="Hapus Beban Ops">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-secondary small">Total kategori: <strong id="opsCountBadge" class="text-dark">{{ count($ops) }}</strong></span>
    </div>
</div>

<!-- Modal Tambah & Edit Ops -->
<div class="modal fade" id="opsFormModal" tabindex="-1" aria-labelledby="opsFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="opsFormModalLabel">
                    Tambah Kategori Beban Ops
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="opsForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editOpsIdHolder" value="">

                    <!-- ID Ops -->
                    <div class="mb-3">
                        <label for="inputIdOps" class="form-label small fw-semibold text-dark">ID Beban</label>
                        <input type="text" class="form-control bg-light" id="inputIdOps" value="{{ $nextId ?? 'OPS-006' }}" readonly>
                    </div>

                    <!-- Beban Ops -->
                    <div class="mb-3">
                        <label for="inputBebanOps" class="form-label small fw-semibold text-dark">
                            Nama Beban Operasional <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputBebanOps" data-field-name="Nama Beban Ops" placeholder="Nama kategori pengeluaran" required>
                    </div>

                    <!-- Ket Ops -->
                    <div class="mb-3">
                        <label for="inputKetOps" class="form-label small fw-semibold text-dark">
                            Keterangan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputKetOps" rows="3" data-field-name="Keterangan Ops" placeholder="Keterangan peruntukan pengeluaran" required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveOps">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('opsFormModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('opsForm');
        const defaultNextId = '{{ $nextId ?? "OPS-006" }}';
        const btnSave = document.getElementById('btnSaveOps');

        // Buka Modal Tambah Ops
        document.getElementById('btnOpenAddOpsModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editOpsIdHolder').value = '';
            document.getElementById('inputIdOps').value = defaultNextId;
            document.getElementById('opsFormModalLabel').textContent = 'Tambah Kategori Beban Ops';
            modal.show();
        });

        // Buka Modal Edit Ops
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-ops');
            if (btn) {
                form.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                document.getElementById('editOpsIdHolder').value = id;
                document.getElementById('inputIdOps').value = id;
                document.getElementById('inputBebanOps').value = btn.getAttribute('data-name');
                document.getElementById('inputKetOps').value = btn.getAttribute('data-desc');

                document.getElementById('opsFormModalLabel').textContent = 'Ubah Kategori Beban Ops (' + id + ')';
                modal.show();
            }
        });

        // Submit Form ke Backend (POST/PUT via Fetch)
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValidRequired = validateRequiredFields(form);
            if (!isValidRequired) return;

            const editId = document.getElementById('editOpsIdHolder').value;
            const idOps = document.getElementById('inputIdOps').value;
            const bebanOps = document.getElementById('inputBebanOps').value.trim();
            const ketOps = document.getElementById('inputKetOps').value.trim();

            const url = editId ? `/master/ops/${editId}` : "{{ route('master.ops.store') }}";
            const method = editId ? 'PUT' : 'POST';
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            btnSave.disabled = true;
            btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_ops: idOps,
                    beban_ops: bebanOps,
                    ket_ops: ketOps
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                btnSave.disabled = false;
                btnSave.textContent = 'Simpan';
                if (status >= 200 && status < 300 && body.success) {
                    modal.hide();
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan data beban ops.');
                    showToastAlert(errMsg, 'danger');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                btnSave.textContent = 'Simpan';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
