@extends('layouts.app')

@section('title', 'Master Pengguna')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Master Data Pengguna</h1>
        <p class="page-subtitle">Pengelolaan akun pengguna, hak akses peran, dan penugasan cabang.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm px-3" id="btnOpenAddModal">
            <i class="bi bi-plus me-1"></i> Tambah Pengguna
        </button>
    </div>
</div>

<!-- Tabel Master Pengguna -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Pengguna Sistem</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 280px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="userTable" placeholder="Cari nama, ID, no. HP...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="userTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>ID Pengguna</th>
                    <th>Nama Pengguna</th>
                    <th>Peran</th>
                    <th>Cabang</th>
                    <th>Status</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Kata Sandi</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $u)
                <tr data-id="{{ $u['id_user'] }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $u['id_user'] }}</span></td>
                    <td class="fw-medium text-dark user-name-cell">{{ $u['nama_user'] }}</td>
                    <td>
                        @if(strtolower($u['role']) === 'owner')
                            <span class="badge badge-role-owner">Owner</span>
                        @else
                            <span class="badge badge-role-admin">Admin</span>
                        @endif
                    </td>
                    <td>
                        @if(strtolower($u['role']) === 'owner')
                            <span class="text-secondary">-</span>
                        @elseif(!empty($u['nama_cabang']))
                            <span class="text-dark">{{ $u['nama_cabang'] }}</span>
                        @elseif(!empty($u['id_cab']))
                            <span class="text-dark">{{ $u['id_cab'] }}</span>
                        @else
                            <span class="badge bg-danger text-white">Belum terikat cabang</span>
                        @endif
                    </td>
                    <td>
                        @if($u['status'] === 'Aktif')
                            <span class="badge badge-status-aktif">Aktif</span>
                        @else
                            <span class="badge badge-status-nonaktif">{{ $u['status'] }}</span>
                        @endif
                    </td>
                    <td class="tabular-nums">{{ $u['no_hp'] }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 180px;">{{ $u['alamat'] }}</span></td>
                    <td><code class="text-secondary bg-light px-1 py-0 rounded">{{ $u['password'] }}</code></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1">
                            <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-user"
                                    data-id="{{ $u['id_user'] }}"
                                    data-name="{{ $u['nama_user'] }}"
                                    data-phone="{{ $u['no_hp'] }}"
                                    data-address="{{ $u['alamat'] }}"
                                    data-status="{{ $u['status'] }}"
                                    data-role="{{ ucfirst($u['role']) }}"
                                    data-cabang="{{ $u['id_cab'] ?? '' }}"
                                    title="Ubah Pengguna"
                                    aria-label="Ubah Pengguna">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                    data-id="{{ $u['id_user'] }}"
                                    data-delete-url="{{ route('master.user.destroy', $u['id_user']) }}"
                                    data-item-name="{{ $u['nama_user'] }} ({{ $u['id_user'] }})"
                                    title="Hapus Pengguna"
                                    aria-label="Hapus Pengguna">
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
        <span class="text-secondary small">Total data: <strong id="userCountBadge" class="text-dark">{{ count($users) }}</strong></span>
    </div>
</div>

<!-- Modal Tambah & Edit Pengguna -->
<div class="modal fade" id="userFormModal" tabindex="-1" aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="userFormModalLabel">
                    Tambah Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="userForm" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editUserIdHolder" value="">

                    <!-- ID User -->
                    <div class="mb-3">
                        <label for="inputIdUser" class="form-label small fw-semibold text-dark">ID Pengguna</label>
                        <input type="text" class="form-control bg-light" id="inputIdUser" value="{{ $nextId ?? 'USR-003' }}" readonly>
                    </div>

                    <!-- Nama User -->
                    <div class="mb-3">
                        <label for="inputNamaUser" class="form-label small fw-semibold text-dark">
                            Nama Pengguna (Username) <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputNamaUser" data-field-name="Nama Pengguna" placeholder="Masukkan username" required>
                    </div>

                    <!-- No HP -->
                    <div class="mb-3">
                        <label for="inputNoHp" class="form-label small fw-semibold text-dark">
                            Nomor HP / Telepon <span class="text-danger">*</span>
                        </label>
                        <input type="text" inputmode="numeric" class="form-control numeric-phone-only" id="inputNoHp" data-field-name="Nomor HP" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <!-- Alamat -->
                    <div class="mb-3">
                        <label for="inputAlamat" class="form-label small fw-semibold text-dark">
                            Alamat <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="inputAlamat" rows="2" data-field-name="Alamat" placeholder="Alamat lengkap" required></textarea>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="inputStatus" class="form-label small fw-semibold text-dark">
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
                        <label for="inputRole" class="form-label small fw-semibold text-dark">
                            Peran (Role) <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputRole" data-field-name="Peran" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="Owner">Owner</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <!-- Cabang (Wajib HANYA jika Role = Admin) -->
                    <div class="mb-3" id="wrapperCabangUser" style="display: none;">
                        <label for="inputCabangUser" class="form-label small fw-semibold text-dark">
                            Cabang Penugasan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="inputCabangUser" data-field-name="Cabang Penugasan">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $cab)
                            <option value="{{ $cab['id_cab'] }}">{{ $cab['nama_cabang'] }} ({{ $cab['id_cab'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="inputPassword" class="form-label small fw-semibold text-dark">
                            Kata Sandi <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="inputPassword" data-field-name="Kata Sandi" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnSaveUser">Simpan</button>
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
            document.getElementById('userFormModalLabel').textContent = 'Tambah Pengguna';
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

                document.getElementById('userFormModalLabel').textContent = 'Ubah Pengguna (' + id + ')';
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
                btnSave.textContent = 'Simpan';
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
                btnSave.textContent = 'Simpan';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
