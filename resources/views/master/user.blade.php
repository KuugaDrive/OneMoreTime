@extends('layouts.app')

@section('title', 'Master User')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-people-fill text-primary"></i> Master Data User
        </h1>
        <p class="page-subtitle">Kelola akun pengguna, status, role akses, serta informasi kontak person agen.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" id="btnOpenAddModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah User Baru
        </button>
    </div>
</div>

<!-- =========================================================================
     TABEL MASTER USER
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Daftar Pengguna Sistem (Users)
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 300px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="userTable" placeholder="Cari nama, ID, no hp...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="userTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID User</th>
                    <th>Nama User (Username)</th>
                    <th>Role</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Password</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $u)
                <tr data-id="{{ $u['id_user'] }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $u['id_user'] }}</span></td>
                    <td class="fw-semibold user-name-cell">{{ $u['nama_user'] }}</td>
                    <td>
                        <span class="badge {{ strtolower($u['role']) === 'owner' ? 'bg-primary' : 'bg-secondary' }}">
                            {{ ucfirst($u['role']) }}
                        </span>
                    </td>
                    <td>
                        @if(strtolower($u['role']) === 'owner')
                            <span class="text-muted">-</span>
                        @elseif(!empty($u['nama_cabang']))
                            <span class="badge bg-light text-primary border"><i class="bi bi-buildings me-1"></i>{{ $u['nama_cabang'] }}</span>
                        @elseif(!empty($u['id_cab']))
                            <span class="badge bg-light text-primary border">{{ $u['id_cab'] }}</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger">Belum terikat cabang</span>
                        @endif
                    </td>
                    <td>
                        @if($u['status'] === 'Aktif')
                            <span class="badge badge-status-aktif"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge badge-status-nonaktif">{{ $u['status'] }}</span>
                        @endif
                    </td>
                    <td>{{ $u['no_hp'] }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 180px;">{{ $u['alamat'] }}</span></td>
                    <td><code>{{ $u['password'] }}</code></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary btn-edit-user me-1"
                                data-id="{{ $u['id_user'] }}"
                                data-name="{{ $u['nama_user'] }}"
                                data-phone="{{ $u['no_hp'] }}"
                                data-address="{{ $u['alamat'] }}"
                                data-status="{{ $u['status'] }}"
                                data-role="{{ ucfirst($u['role']) }}"
                                data-cabang="{{ $u['id_cab'] ?? '' }}"
                                title="Edit User">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                data-id="{{ $u['id_user'] }}"
                                data-delete-url="{{ route('master.user.destroy', $u['id_user']) }}"
                                data-item-name="{{ $u['nama_user'] }} ({{ $u['id_user'] }})"
                                title="Hapus User">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted">Data Master User dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="userCountBadge" class="fw-bold">{{ count($users) }}</span> data</small>
    </div>
</div>

<!-- =========================================================================
     MODAL TAMBAH & EDIT USER
     ========================================================================= -->
<div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="userFormModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i> Tambah Data User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editUserIdHolder" value="">

                    <!-- *IdUser (Otomatis & Readonly) -->
                    <div class="mb-3">
                        <label for="inputIdUser" class="form-label">
                            ID User <span class="badge bg-secondary ms-1">Otomatis / Readonly</span>
                        </label>
                        <input type="text" class="form-control bg-light" id="inputIdUser" value="{{ $nextId ?? 'USR-003' }}" readonly>
                        <small class="text-muted" style="font-size: 0.72rem;">ID dibuat otomatis oleh sistem dan tidak dapat diubah.</small>
                    </div>

                    <!-- Nama User (Username) -> Unique -->
                    <div class="mb-3">
                        <label for="inputNamaUser" class="form-label">
                            Nama User (Username) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaUser" data-field-name="Nama User" placeholder="Contoh: joko_operasional" required>
                    </div>

                    <!-- No HP -->
                    <div class="mb-3">
                        <label for="inputNoHp" class="form-label">
                            No. HP / WhatsApp <span class="text-danger">*</span>
                        </label>
                        <input type="text" inputmode="numeric" class="form-control numeric-phone-only" id="inputNoHp" data-field-name="Nomor HP" placeholder="08xxxxxxxxxx (Hanya angka)" required>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label for="inputAlamat" class="form-label">
                            Alamat Pengguna <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputAlamat" rows="2" data-field-name="Alamat" placeholder="Alamat lengkap pengguna" required></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="inputStatus" class="form-label">
                            Status Akun <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputStatus" data-field-name="Status" required>
                            <option value="">-- Pilih Status --</option>
                            @foreach($statuses as $st)
                            <option value="{{ $st['nama_status'] }}">{{ $st['nama_status'] }} (Level {{ $st['level'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label for="inputRole" class="form-label">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputRole" data-field-name="Role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="Owner">Owner</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <!-- Cabang (Wajib HANYA jika Role = Admin) -->
                    <div class="mb-3" id="wrapperCabangUser" style="display: none;">
                        <label for="inputCabangUser" class="form-label">
                            Cabang Penugasan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputCabangUser" data-field-name="Cabang Penugasan">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $cab)
                            <option value="{{ $cab['id_cab'] }}">{{ $cab['nama_cabang'] }} ({{ $cab['id_cab'] }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.72rem;">Admin operasional wajib terikat ke satu cabang.</small>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="inputPassword" class="form-label">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="inputPassword" data-field-name="Password" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnSaveUser">
                        <i class="bi bi-save me-1"></i> Simpan Data
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
        const modalEl = document.getElementById('userFormModal');
        const modal = new bootstrap.Modal(modalEl);
        const form = document.getElementById('userForm');
        const defaultNextId = '{{ $nextId ?? "USR-003" }}';
        const btnSave = document.getElementById('btnSaveUser');
        const roleSelect = document.getElementById('inputRole');
        const cabangWrapper = document.getElementById('wrapperCabangUser');
        const cabangSelect = document.getElementById('inputCabangUser');

        function toggleCabangVisibility() {
            const role = (roleSelect.value || '').toLowerCase();
            if (role === 'admin') {
                cabangWrapper.style.display = 'block';
                cabangSelect.setAttribute('required', 'required');
            } else {
                cabangWrapper.style.display = 'none';
                cabangSelect.removeAttribute('required');
                cabangSelect.value = '';
            }
        }
        roleSelect.addEventListener('change', toggleCabangVisibility);

        // Buka Modal Tambah
        document.getElementById('btnOpenAddModal').addEventListener('click', function () {
            form.reset();
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('editUserIdHolder').value = '';
            document.getElementById('inputIdUser').value = defaultNextId;
            cabangSelect.value = '';
            toggleCabangVisibility();
            document.getElementById('userFormModalLabel').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i> Tambah Data User';
            modal.show();
        });

        // Buka Modal Edit
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-user');
            if (btn) {
                form.reset();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                document.getElementById('editUserIdHolder').value = id;
                document.getElementById('inputIdUser').value = id;
                document.getElementById('inputNamaUser').value = btn.getAttribute('data-name');
                document.getElementById('inputNoHp').value = btn.getAttribute('data-phone');
                document.getElementById('inputAlamat').value = btn.getAttribute('data-address');
                document.getElementById('inputStatus').value = btn.getAttribute('data-status');
                document.getElementById('inputRole').value = btn.getAttribute('data-role');
                cabangSelect.value = btn.getAttribute('data-cabang') || '';
                toggleCabangVisibility();
                document.getElementById('inputPassword').value = '********';

                document.getElementById('userFormModalLabel').innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Data User (' + id + ')';
                modal.show();
            }
        });

        // Submit Form ke Backend (POST/PUT via Fetch)
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValidRequired = validateRequiredFields(form);
            if (!isValidRequired) return;

            const editId = document.getElementById('editUserIdHolder').value;
            const idUser = document.getElementById('inputIdUser').value;
            const namaUser = document.getElementById('inputNamaUser').value.trim();
            const noHp = document.getElementById('inputNoHp').value.trim();
            const alamat = document.getElementById('inputAlamat').value.trim();
            const status = document.getElementById('inputStatus').value;
            const role = document.getElementById('inputRole').value;
            const idCab = (role.toLowerCase() === 'admin') ? cabangSelect.value : null;
            const password = document.getElementById('inputPassword').value;

            const url = editId ? `/master/user/${editId}` : "{{ route('master.user.store') }}";
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
                    id_user: idUser,
                    nama_user: namaUser,
                    no_hp: noHp,
                    alamat: alamat,
                    status: status,
                    role: role,
                    level: role,
                    id_cab: idCab,
                    password: password
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
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan data.');
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
