@extends('layouts.app')

@section('title', 'Master Cabang')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Master Data Cabang</h1>
        <p class="page-subtitle">Pengelolaan data cabang, alamat operasional, dan penanggung jawab.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3" id="btnOpenAddCabangModal">
            <i class="bi bi-plus me-1"></i> Tambah Cabang
        </button>
    </div>
</div>

<!-- Card Tabel Master Cabang -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Cabang</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="cabangTable" placeholder="Cari cabang, PIC, alamat...">
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
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cabangs as $index => $c)
                <tr data-id="{{ $c['id_cab'] }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $c['id_cab'] }}</span></td>
                    <td class="fw-medium text-dark cabang-name-cell">{{ $c['nama_cabang'] }}</td>
                    <td>
                        @if(!empty($c['user_name_jne']))
                            <span class="font-monospace text-secondary small">{{ $c['user_name_jne'] }}</span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 220px;">{{ $c['alamat'] }}</span></td>
                    <td><span class="text-dark">{{ $c['nama_pic'] }}</span></td>
                    <td class="tabular-nums">{{ $c['no_hp'] }}</td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-cabang"
                                    data-id="{{ $c['id_cab'] }}"
                                    data-name="{{ $c['nama_cabang'] }}"
                                    data-username-jne="{{ $c['user_name_jne'] ?? '' }}"
                                    data-address="{{ $c['alamat'] }}"
                                    data-pic="{{ $c['nama_pic'] }}"
                                    data-phone="{{ $c['no_hp'] }}"
                                    title="Ubah Cabang"
                                    aria-label="Ubah Cabang">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                    data-id="{{ $c['id_cab'] }}"
                                    data-delete-url="{{ route('master.cabang.destroy', $c['id_cab']) }}"
                                    data-item-name="{{ $c['nama_cabang'] }} ({{ $c['id_cab'] }})"
                                    title="Hapus Cabang"
                                    aria-label="Hapus Cabang">
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
        <span class="text-secondary small">Total cabang: <strong id="cabangCountBadge" class="text-dark">{{ count($cabangs) }}</strong></span>
    </div>
</div>

<!-- Modal Tambah & Edit Cabang -->
<div class="modal fade" id="cabangFormModal" tabindex="-1" aria-labelledby="cabangFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="cabangFormModalLabel">
                    Tambah Cabang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="cabangForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editCabangIdHolder" value="">

                    <!-- ID Cabang -->
                    <div class="mb-3">
                        <label for="inputIdCab" class="form-label small fw-semibold text-dark">ID Cabang</label>
                        <input type="text" class="form-control bg-light" id="inputIdCab" value="{{ $nextId ?? 'CB-005' }}" readonly>
                    </div>

                    <!-- Nama Cabang -->
                    <div class="mb-3">
                        <label for="inputNamaCabang" class="form-label small fw-semibold text-dark">
                            Nama Cabang <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaCabang" data-field-name="Nama Cabang" placeholder="Nama cabang" required>
                    </div>

                    <!-- User Name JNE -->
                    <div class="mb-3">
                        <label for="inputUserNameJne" class="form-label small fw-semibold text-dark">
                            User Name JNE <span class="text-secondary small fw-normal">(Opsional)</span>
                        </label>
                        <input type="text" class="form-control font-monospace" id="inputUserNameJne" data-field-name="User Name JNE" placeholder="Nama counter/user resmi JNE" maxlength="50">
                        <div class="form-text small text-secondary">Identitas counter sesuai dokumen resmi JNE.</div>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label for="inputAlamatCabang" class="form-label small fw-semibold text-dark">
                            Alamat Cabang <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputAlamatCabang" rows="2" data-field-name="Alamat Cabang" placeholder="Alamat lengkap cabang" required></textarea>
                    </div>

                    <!-- Nama PIC -->
                    <div class="mb-3">
                        <label for="inputNamaPic" class="form-label small fw-semibold text-dark">
                            Nama PIC (Penanggung Jawab) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaPic" data-field-name="Nama PIC" placeholder="Nama penanggung jawab" required>
                    </div>

                    <!-- No HP -->
                    <div class="mb-3">
                        <label for="inputNoHpCabang" class="form-label small fw-semibold text-dark">
                            Nomor Telepon / HP <span class="text-danger">*</span>
                        </label>
                        <input type="text" inputmode="numeric" class="form-control numeric-phone-only" id="inputNoHpCabang" data-field-name="Nomor Telepon" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveCabang">Simpan</button>
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

        // Buka Modal Tambah Cabang
        document.getElementById('btnOpenAddCabangModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editCabangIdHolder').value = '';
            document.getElementById('inputIdCab').value = defaultNextId;
            document.getElementById('inputUserNameJne').value = '';
            document.getElementById('cabangFormModalLabel').textContent = 'Tambah Cabang';
            modal.show();
        });

        // Buka Modal Edit Cabang
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

                document.getElementById('cabangFormModalLabel').textContent = 'Ubah Cabang (' + id + ')';
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
                btnSave.textContent = 'Simpan';
                if (status >= 200 && status < 300 && body.success) {
                    modal.hide();
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan data cabang.');
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
