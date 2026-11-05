@extends('layouts.app')

@section('title', 'Master Status')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Master Data Status</h1>
        <p class="page-subtitle">Pengelolaan status akun pengguna dan hierarki level akses sistem.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3" id="btnOpenAddStatusModal">
            <i class="bi bi-plus me-1"></i> Tambah Status
        </button>
    </div>
</div>

<!-- Card Tabel Master Status -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Status Pengguna</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="statusTable" placeholder="Cari nama status, level...">
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
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statuses as $index => $s)
                <tr data-id="{{ $s['id_status'] }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $s['id_status'] }}</span></td>
                    <td class="fw-medium text-dark status-name-cell">
                        @if($s['nama_status'] === 'Aktif')
                            <span class="badge badge-status-aktif">Aktif</span>
                        @elseif($s['nama_status'] === 'Non-Aktif')
                            <span class="badge badge-status-nonaktif">{{ $s['nama_status'] }}</span>
                        @else
                            <span class="badge bg-light text-dark border">{{ $s['nama_status'] }}</span>
                        @endif
                    </td>
                    <td><span class="text-secondary small">Level {{ $s['level'] }}</span></td>
                    <td><span class="text-secondary">{{ $s['ket_status'] }}</span></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-status"
                                    data-id="{{ $s['id_status'] }}"
                                    data-name="{{ $s['nama_status'] }}"
                                    data-level="{{ $s['level'] }}"
                                    data-desc="{{ $s['ket_status'] }}"
                                    title="Ubah Status"
                                    aria-label="Ubah Status">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                    data-id="{{ $s['id_status'] }}"
                                    data-delete-url="{{ route('master.status.destroy', $s['id_status']) }}"
                                    data-item-name="{{ $s['nama_status'] }} ({{ $s['id_status'] }})"
                                    title="Hapus Status"
                                    aria-label="Hapus Status">
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
        <span class="text-secondary small">Total status: <strong id="statusCountBadge" class="text-dark">{{ count($statuses) }}</strong></span>
    </div>
</div>

<!-- Modal Tambah & Edit Status -->
<div class="modal fade" id="statusFormModal" tabindex="-1" aria-labelledby="statusFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="statusFormModalLabel">
                    Tambah Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="statusForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editStatusIdHolder" value="">

                    <!-- ID Status -->
                    <div class="mb-3">
                        <label for="inputIdStatus" class="form-label small fw-semibold text-dark">ID Status</label>
                        <input type="text" class="form-control bg-light" id="inputIdStatus" value="{{ $nextId ?? 'ST-005' }}" readonly>
                    </div>

                    <!-- Nama Status -->
                    <div class="mb-3">
                        <label for="inputNamaStatus" class="form-label small fw-semibold text-dark">
                            Nama Status <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaStatus" data-field-name="Nama Status" placeholder="Nama status" required>
                    </div>

                    <!-- Level Status -->
                    <div class="mb-3">
                        <label for="inputLevel" class="form-label small fw-semibold text-dark">
                            Level Status (1 - 4) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputLevel" data-field-name="Level" required>
                            <option value="">-- Pilih Level --</option>
                            <option value="1">Level 1 - Pengguna Aktif Penuh</option>
                            <option value="2">Level 2 - Pengguna Terbatas</option>
                            <option value="3">Level 3 - Pengguna Nonaktif Sementara</option>
                            <option value="4">Level 4 - Akun Dinonaktifkan Permanen</option>
                        </select>
                    </div>

                    <!-- Ket Status -->
                    <div class="mb-3">
                        <label for="inputKetStatus" class="form-label small fw-semibold text-dark">
                            Keterangan Status <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputKetStatus" rows="2" data-field-name="Keterangan Status" placeholder="Deskripsi status" required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveStatus">Simpan</button>
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

        // Buka Modal Tambah Status
        document.getElementById('btnOpenAddStatusModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editStatusIdHolder').value = '';
            document.getElementById('inputIdStatus').value = defaultNextId;
            document.getElementById('inputLevel').disabled = false;
            document.getElementById('statusFormModalLabel').textContent = 'Tambah Status';
            modal.show();
        });

        // Buka Modal Edit Status
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-status');
            if (btn) {
                form.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                const level = btn.getAttribute('data-level');
                document.getElementById('editStatusIdHolder').value = id;
                document.getElementById('inputIdStatus').value = id;
                document.getElementById('inputNamaStatus').value = btn.getAttribute('data-name');
                document.getElementById('inputLevel').value = level;
                document.getElementById('inputKetStatus').value = btn.getAttribute('data-desc');

                // Jika level 1 (Aktif), nonaktifkan select level
                if (level == '1') {
                    document.getElementById('inputLevel').disabled = true;
                } else {
                    document.getElementById('inputLevel').disabled = false;
                }

                document.getElementById('statusFormModalLabel').textContent = 'Ubah Status (' + id + ')';
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
            const level = document.getElementById('inputLevel').value;
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
                btnSave.textContent = 'Simpan';
                if (status >= 200 && status < 300 && body.success) {
                    modal.hide();
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan data status.');
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
