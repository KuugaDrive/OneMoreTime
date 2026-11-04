@extends('layouts.app')

@section('title', 'Master Ops')

@section('content')
<!-- Header Halaman -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-tools text-primary"></i> Master Beban Operasional (Ops)
        </h1>
        <p class="page-subtitle">Kelola kategori beban biaya operasional agen seperti utilitas kantor, bensin armada, lakban & packing.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" id="btnOpenAddOpsModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Kategori Ops
        </button>
    </div>
</div>

<!-- Card Tabel Data Master Ops -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Daftar Kategori Beban Operasional
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="opsTable" placeholder="Cari nama beban, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="opsTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Ops</th>
                    <th>Nama Beban Ops</th>
                    <th>Keterangan Beban Operasional</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ops as $index => $o)
                <tr data-id="{{ $o['id_ops'] }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $o['id_ops'] }}</span></td>
                    <td class="fw-semibold ops-name-cell">{{ $o['beban_ops'] }}</td>
                    <td>{{ $o['ket_ops'] }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-ops me-1"
                                data-id="{{ $o['id_ops'] }}"
                                data-name="{{ $o['beban_ops'] }}"
                                data-desc="{{ $o['ket_ops'] }}"
                                title="Edit Ops">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                data-id="{{ $o['id_ops'] }}"
                                data-delete-url="{{ route('master.ops.destroy', $o['id_ops']) }}"
                                data-item-name="{{ $o['beban_ops'] }} ({{ $o['id_ops'] }})"
                                title="Hapus Ops">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted">Data Master Beban Ops dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="opsCountBadge" class="fw-bold">{{ count($ops) }}</span> kategori</small>
    </div>
</div>

<!-- =========================================================================
     MODAL TAMBAH & EDIT BEBAN OPS
     ========================================================================= -->
<div class="modal fade" id="opsFormModal" tabindex="-1" aria-labelledby="opsFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="opsFormModalLabel">
                    <i class="bi bi-tools me-2"></i> Tambah Kategori Beban Ops
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="opsForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editOpsIdHolder" value="">

                    <!-- *IdOps (Otomatis & Readonly) -->
                    <div class="mb-3">
                        <label for="inputIdOps" class="form-label">
                            ID Ops <span class="badge bg-secondary ms-1">Otomatis / Readonly</span>
                        </label>
                        <input type="text" class="form-control bg-light" id="inputIdOps" value="{{ $nextId ?? 'OP-006' }}" readonly>
                        <small class="text-muted" style="font-size: 0.72rem;">ID dibuat otomatis oleh sistem dan tidak dapat diedit.</small>
                    </div>

                    <!-- BebanOps -> Unique -->
                    <div class="mb-3">
                        <label for="inputBebanOps" class="form-label">
                            Nama Beban Ops <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputBebanOps" data-field-name="Beban Ops" placeholder="Contoh: Sewa Ruko & Bangunan" required>
                    </div>

                    <!-- KetOps -->
                    <div class="mb-3">
                        <label for="inputKetOps" class="form-label">
                            Keterangan Beban Ops <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputKetOps" rows="3" data-field-name="Keterangan Beban Ops" placeholder="Rincian pos pengeluaran operasional ini" required></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSaveOps">
                        <i class="bi bi-save me-1"></i> Simpan Beban Ops
                    </button>
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
        const defaultNextId = '{{ $nextId ?? "OP-006" }}';
        const btnSave = document.getElementById('btnSaveOps');

        // Buka Modal Tambah
        document.getElementById('btnOpenAddOpsModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editOpsIdHolder').value = '';
            document.getElementById('inputIdOps').value = defaultNextId;
            document.getElementById('opsFormModalLabel').innerHTML = '<i class="bi bi-tools me-2"></i> Tambah Kategori Beban Ops';
            modal.show();
        });

        // Buka Modal Edit
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

                document.getElementById('opsFormModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Beban Ops (' + id + ')';
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
                btnSave.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Data';
                if (status >= 200 && status < 300 && body.success) {
                    modal.hide();
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan operasional.');
                    showToastAlert(errMsg, 'danger');
                }
            })
            .catch(err => {
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Data';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
