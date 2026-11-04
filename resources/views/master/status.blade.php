@extends('layouts.app')

@section('title', 'Master Status')

@section('content')
<!-- Header Halaman -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-check-circle-fill text-primary"></i> Master Status
        </h1>
        <p class="page-subtitle">Kelola master status akun pengguna & alur status sistem (Level 1 sampai 4).</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" id="btnOpenAddStatusModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Status Baru
        </button>
    </div>
</div>

<!-- Card Tabel Data Master Status -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Daftar Master Status Sistem
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="statusTable" placeholder="Cari nama status, level...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="statusTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Status</th>
                    <th>Nama Status</th>
                    <th>Level</th>
                    <th>Keterangan Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statuses as $index => $s)
                <tr data-id="{{ $s['id_status'] }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $s['id_status'] }}</span></td>
                    <td class="fw-semibold status-name-cell">
                        @if($s['nama_status'] === 'Aktif')
                            <span class="badge badge-status-aktif px-2 py-1"><i class="bi bi-check-circle me-1"></i>{{ $s['nama_status'] }}</span>
                        @elseif($s['nama_status'] === 'Non-Aktif')
                            <span class="badge badge-status-nonaktif px-2 py-1">{{ $s['nama_status'] }}</span>
                        @else
                            <span class="badge bg-warning text-dark px-2 py-1">{{ $s['nama_status'] }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-secondary">Level {{ $s['level'] }}</span></td>
                    <td>{{ $s['ket_status'] }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-status me-1"
                                data-id="{{ $s['id_status'] }}"
                                data-name="{{ $s['nama_status'] }}"
                                data-level="{{ $s['level'] }}"
                                data-desc="{{ $s['ket_status'] }}"
                                title="Edit Status">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                data-id="{{ $s['id_status'] }}"
                                data-delete-url="{{ route('master.status.destroy', $s['id_status']) }}"
                                data-item-name="{{ $s['nama_status'] }} ({{ $s['id_status'] }})"
                                title="Hapus Status">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted">Data Master Status dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="statusCountBadge" class="fw-bold">{{ count($statuses) }}</span> status</small>
    </div>
</div>

<!-- =========================================================================
     MODAL TAMBAH & EDIT STATUS
     ========================================================================= -->
<div class="modal fade" id="statusFormModal" tabindex="-1" aria-labelledby="statusFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="statusFormModalLabel">
                    <i class="bi bi-check-circle-fill me-2"></i> Tambah Data Status
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editStatusIdHolder" value="">

                    <!-- *IdStatus (Otomatis & Readonly) -->
                    <div class="mb-3">
                        <label for="inputIdStatus" class="form-label">
                            ID Status <span class="badge bg-secondary ms-1">Otomatis / Readonly</span>
                        </label>
                        <input type="text" class="form-control bg-light" id="inputIdStatus" value="{{ $nextId ?? 'ST-005' }}" readonly>
                        <small class="text-muted" style="font-size: 0.72rem;">ID dibuat otomatis oleh sistem dan tidak dapat diedit.</small>
                    </div>

                    <!-- NamaStatus -> Unique -->
                    <div class="mb-3">
                        <label for="inputNamaStatus" class="form-label">
                            Nama Status <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaStatus" data-field-name="Nama Status" placeholder="Contoh: Pending Review" required>
                    </div>

                    <!-- Level (Dropdown 1, 2, 3, 4) -->
                    <div class="mb-3">
                        <label for="inputLevelStatus" class="form-label">
                            Level Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputLevelStatus" data-field-name="Level Status" required>
                            <option value="">-- Pilih Level Status --</option>
                            <option value="1">1 (Tingkat 1 - Operasional / Utama)</option>
                            <option value="2">2 (Tingkat 2 - Sekunder / Nonaktif)</option>
                            <option value="3">3 (Tingkat 3 - Khusus / Cuti)</option>
                            <option value="4">4 (Tingkat 4 - Dibekukan / Suspended)</option>
                        </select>
                    </div>

                    <!-- KetStatus -->
                    <div class="mb-3">
                        <label for="inputKetStatus" class="form-label">
                            Keterangan Status <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputKetStatus" rows="3" data-field-name="Keterangan Status" placeholder="Deskripsi peruntukan status ini" required></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSaveStatus">
                        <i class="bi bi-save me-1"></i> Simpan Status
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
        const modalEl = document.getElementById('statusFormModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('statusForm');
        const defaultNextId = '{{ $nextId ?? "ST-005" }}';
        const btnSave = document.getElementById('btnSaveStatus');

        // Buka Modal Tambah
        document.getElementById('btnOpenAddStatusModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editStatusIdHolder').value = '';
            document.getElementById('inputIdStatus').value = defaultNextId;
            document.getElementById('statusFormModalLabel').innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Tambah Data Status';
            modal.show();
        });

        // Buka Modal Edit
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-status');
            if (btn) {
                form.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                document.getElementById('editStatusIdHolder').value = id;
                document.getElementById('inputIdStatus').value = id;
                document.getElementById('inputNamaStatus').value = btn.getAttribute('data-name');
                document.getElementById('inputLevelStatus').value = btn.getAttribute('data-level');
                document.getElementById('inputKetStatus').value = btn.getAttribute('data-desc');

                document.getElementById('statusFormModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Status (' + id + ')';
                modal.show();
            }
        });

        // Submit Form ke Backend (POST/PUT via Fetch)
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValidRequired = validateRequiredFields(form);
            if (!isValidRequired) return;

            const editId = document.getElementById('editStatusIdHolder').value;
            const idStatus = document.getElementById('inputIdStatus').value;
            const namaStatus = document.getElementById('inputNamaStatus').value.trim();
            const level = document.getElementById('inputLevelStatus').value;
            const ketStatus = document.getElementById('inputKetStatus').value.trim();

            const url = editId ? `/master/status/${editId}` : "{{ route('master.status.store') }}";
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
                    id_status: idStatus,
                    nama_status: namaStatus,
                    level: level,
                    ket_status: ketStatus
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
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan status.');
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
