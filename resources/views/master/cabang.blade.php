@extends('layouts.app')

@section('title', 'Master Cabang')

@section('content')
<!-- Header Halaman -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-buildings-fill text-primary"></i> Master Cabang
        </h1>
        <p class="page-subtitle">Kelola data gerai / cabang agen JNE, alamat titik operasional, serta penanggung jawab (PIC).</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" id="btnOpenAddCabangModal">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Cabang Baru
        </button>
    </div>
</div>

<!-- Card Tabel Data Master Cabang -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Daftar Cabang & Gerai Agen JNE
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="cabangTable" placeholder="Cari cabang, PIC, alamat...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="cabangTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Cabang</th>
                    <th>Nama Cabang</th>
                    <th>User Name JNE</th>
                    <th>Alamat</th>
                    <th>Nama PIC</th>
                    <th>No. HP</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cabangs as $index => $c)
                <tr data-id="{{ $c['id_cab'] }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $c['id_cab'] }}</span></td>
                    <td class="fw-semibold cabang-name-cell">{{ $c['nama_cabang'] }}</td>
                    <td>
                        @if(!empty($c['user_name_jne']))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">{{ $c['user_name_jne'] }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 240px;">{{ $c['alamat'] }}</span></td>
                    <td><span class="badge bg-light text-dark border">{{ $c['nama_pic'] }}</span></td>
                    <td>{{ $c['no_hp'] }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-cabang me-1"
                                data-id="{{ $c['id_cab'] }}"
                                data-name="{{ $c['nama_cabang'] }}"
                                data-username-jne="{{ $c['user_name_jne'] ?? '' }}"
                                data-address="{{ $c['alamat'] }}"
                                data-pic="{{ $c['nama_pic'] }}"
                                data-phone="{{ $c['no_hp'] }}"
                                title="Edit Cabang">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                data-id="{{ $c['id_cab'] }}"
                                data-delete-url="{{ route('master.cabang.destroy', $c['id_cab']) }}"
                                data-item-name="{{ $c['nama_cabang'] }} ({{ $c['id_cab'] }})"
                                title="Hapus Cabang">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted">Data Master Cabang dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="cabangCountBadge" class="fw-bold">{{ count($cabangs) }}</span> cabang</small>
    </div>
</div>

<!-- =========================================================================
     MODAL TAMBAH & EDIT CABANG
     ========================================================================= -->
<div class="modal fade" id="cabangFormModal" tabindex="-1" aria-labelledby="cabangFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="cabangFormModalLabel">
                    <i class="bi bi-buildings-fill me-2"></i> Tambah Data Cabang
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="cabangForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editCabangIdHolder" value="">

                    <!-- *IdCab (Otomatis & Readonly) -->
                    <div class="mb-3">
                        <label for="inputIdCab" class="form-label">
                            ID Cabang <span class="badge bg-secondary ms-1">Otomatis / Readonly</span>
                        </label>
                        <input type="text" class="form-control bg-light" id="inputIdCab" value="{{ $nextId ?? 'CB-005' }}" readonly>
                        <small class="text-muted" style="font-size: 0.72rem;">ID dibuat otomatis oleh sistem dan tidak dapat diedit.</small>
                    </div>

                    <!-- Nama Cabang -> Unique -->
                    <div class="mb-3">
                        <label for="inputNamaCabang" class="form-label">
                            Nama Cabang <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaCabang" data-field-name="Nama Cabang" placeholder="Contoh: Cabang Roxy Square" required>
                    </div>

                    <!-- User Name JNE -> Opsional tapi unik -->
                    <div class="mb-3">
                        <label for="inputUserNameJne" class="form-label">
                            User Name JNE <span class="badge bg-light text-muted border ms-1">Opsional</span>
                        </label>
                        <input type="text" class="form-control font-monospace" id="inputUserNameJne" data-field-name="User Name JNE" placeholder="Contoh: BASECLG" maxlength="50">
                        <small class="text-muted" style="font-size: 0.72rem;">Isi dengan User Name pada dokumen Packing List JNE (contoh: BASECLG). Boleh dikosongkan.</small>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label for="inputAlamatCabang" class="form-label">
                            Alamat Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputAlamatCabang" rows="2" data-field-name="Alamat" placeholder="Alamat jalan, kelurahan, kecamatan titik gerai" required></textarea>
                    </div>

                    <!-- Nama PIC -->
                    <div class="mb-3">
                        <label for="inputNamaPic" class="form-label">
                            Nama PIC (Penanggung Jawab) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaPic" data-field-name="Nama PIC" placeholder="Contoh: Ahmad Fauzi" required>
                    </div>

                    <!-- No HP -->
                    <div class="mb-3">
                        <label for="inputNoHpCabang" class="form-label">
                            No. HP PIC <span class="text-danger">*</span>
                        </label>
                        <input type="tel" inputmode="numeric" class="form-control numeric-only" id="inputNoHpCabang" data-field-name="No HP" placeholder="Contoh: 081299887766" maxlength="15" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Hanya angka, contoh: 081299887766.</small>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSaveCabang">
                        <i class="bi bi-save me-1"></i> Simpan Cabang
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
        const modalEl = document.getElementById('cabangFormModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('cabangForm');
        const defaultNextId = '{{ $nextId ?? "CB-005" }}';
        const btnSave = document.getElementById('btnSaveCabang');

        // Buka Modal Tambah
        document.getElementById('btnOpenAddCabangModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editCabangIdHolder').value = '';
            document.getElementById('inputIdCab').value = defaultNextId;
            document.getElementById('inputUserNameJne').value = '';
            document.getElementById('cabangFormModalLabel').innerHTML = '<i class="bi bi-buildings-fill me-2"></i> Tambah Data Cabang';
            modal.show();
        });

        // Buka Modal Edit
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-cabang');
            if (btn) {
                form.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                document.getElementById('editCabangIdHolder').value = id;
                document.getElementById('inputIdCab').value = id;
                document.getElementById('inputNamaCabang').value = btn.getAttribute('data-name');
                document.getElementById('inputUserNameJne').value = btn.getAttribute('data-username-jne') || '';
                document.getElementById('inputAlamatCabang').value = btn.getAttribute('data-address');
                document.getElementById('inputNamaPic').value = btn.getAttribute('data-pic');
                document.getElementById('inputNoHpCabang').value = btn.getAttribute('data-phone');

                document.getElementById('cabangFormModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Data Cabang (' + id + ')';
                modal.show();
            }
        });

        // Submit Form ke Backend (POST/PUT via Fetch)
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValidRequired = validateRequiredFields(form);
            if (!isValidRequired) return;

            const editId = document.getElementById('editCabangIdHolder').value;
            const idCab = document.getElementById('inputIdCab').value;
            const namaCabang = document.getElementById('inputNamaCabang').value.trim();
            const userNameJne = document.getElementById('inputUserNameJne').value.trim();
            const alamat = document.getElementById('inputAlamatCabang').value.trim();
            const namaPic = document.getElementById('inputNamaPic').value.trim();
            const noHp = document.getElementById('inputNoHpCabang').value.trim();

            const url = editId ? `/master/cabang/${editId}` : "{{ route('master.cabang.store') }}";
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
                    id_cab: idCab,
                    nama_cabang: namaCabang,
                    user_name_jne: userNameJne || null,
                    alamat: alamat,
                    nama_pic: namaPic,
                    no_hp: noHp
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
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan cabang.');
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
