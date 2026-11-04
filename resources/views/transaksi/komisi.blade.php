@extends('layouts.app')

@section('title', 'Transaksi Komisi Bulanan')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-cash-stack text-primary"></i> Transaksi Komisi Bulanan (CL)
        </h1>
        <p class="page-subtitle">Input pencatatan transfer pembayaran komisi bulanan dari kantor pusat JNE.</p>
    </div>
    <div>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
            <i class="bi bi-shield-lock-fill me-1"></i> Khusus Role: Owner
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT MANUAL TRANSAKSI KOMISI BULANAN
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header bg-white">
        <span class="d-flex align-items-center gap-2 text-warning-emphasis fw-bold">
            <i class="bi bi-wallet2 text-warning"></i>
            Form Input Manual Transfer Komisi Bulanan
        </span>
    </div>
    <div class="card-body">
        <form id="komisiForm" novalidate>
            <div class="row g-3">
                <!-- *KodeTrxCL (Otomatis & Readonly) -->
                <div class="col-12 col-md-3">
                    <label for="inputKodeTrxCL" class="form-label">
                        Kode Trx CL <span class="badge bg-secondary ms-1">Otomatis</span>
                    </label>
                    <input type="text" class="form-control bg-light" id="inputKodeTrxCL" value="{{ $nextCode ?? 'CL-' . date('Ymd') . '-001' }}" readonly>
                    <small class="text-muted" style="font-size: 0.72rem;">Nomor pencatatan transfer komisi unik.</small>
                </div>

                <!-- Cabang (Wajib) -->
                <div class="col-12 col-md-3">
                    <label for="inputCabangCL" class="form-label">
                        Cabang <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-buildings"></i></span>
                        <select class="form-select" id="inputCabangCL" data-field-name="Cabang" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $cab)
                            <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih cabang transaksi.</small>
                </div>

                <!-- TanggalTf (Flatpickr) -->
                <div class="col-12 col-md-3">
                    <label for="inputTanggalTf" class="form-label">
                        Tanggal Transfer Komisi <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control datepicker" id="inputTanggalTf" data-field-name="Tanggal Transfer" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih tanggal transfer.</small>
                </div>

                <!-- Nominal (Rupiah) -->
                <div class="col-12 col-md-3">
                    <label for="inputNominalCL" class="form-label">
                        Nominal Komisi (Rp) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control rupiah-input" id="inputNominalCL" data-field-name="Nominal Transfer" placeholder="0,00" required>
                    </div>
                    <div class="currency-helper-text">Format: 1.500.000,00 (titik ribuan, koma desimal)</div>
                </div>

                <!-- KetTrxCL -->
                <div class="col-12">
                    <label for="inputKetTrxCL" class="form-label">
                        Keterangan Transfer Komisi <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="inputKetTrxCL" rows="2" data-field-name="Keterangan Komisi" placeholder="Contoh: Pencairan komisi kemitraan periode Februari 2025 via transfer BCA" required></textarea>
                </div>

                <div class="col-12 text-end pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3 me-2" id="btnResetCL">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Komisi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI KOMISI BULANAN
     ========================================================================= -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Riwayat Pencatatan Komisi Bulanan
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="komisiTrxTable" placeholder="Cari kode, cabang, tanggal, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="komisiTrxTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Trx CL</th>
                    <th>Cabang</th>
                    <th>Tanggal Transfer</th>
                    <th class="text-end">Nominal Komisi</th>
                    <th>Keterangan Transfer</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($komisis as $index => $cl)
                <tr data-id="{{ $cl->kode_trx_cl }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $cl->kode_trx_cl }}</span></td>
                    <td><span class="badge bg-light text-primary border">{{ $cl->nama_cabang }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }}</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($cl->nominal, 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 320px;">{{ $cl->ket_trx_cl }}</span></td>
                    <td class="text-center">
                        @can('update', $cl)
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-cl me-1"
                                    data-id="{{ $cl->kode_trx_cl }}"
                                    data-id-cab="{{ $cl->id_cab }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }}"
                                    data-nominal="{{ number_format($cl->nominal, 2, ',', '.') }}"
                                    data-ket="{{ $cl->ket_trx_cl }}"
                                    data-update-url="{{ route('transaksi.komisi.update', $cl->kode_trx_cl) }}"
                                    title="Edit Transaksi Komisi">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endcan

                        @can('delete', $cl)
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                    data-id="{{ $cl->kode_trx_cl }}"
                                    data-delete-url="{{ route('transaksi.komisi.destroy', $cl->kode_trx_cl) }}"
                                    data-item-name="{{ $cl->kode_trx_cl }} ({{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }})"
                                    title="Hapus Transaksi Komisi">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan

                        @cannot('update', $cl)
                            @cannot('delete', $cl)
                                <span class="badge bg-light text-muted border">-</span>
                            @endcannot
                        @endcannot
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted"><i class="bi bi-database-check text-success me-1"></i> Data langsung dari MariaDB (Tabel komisis)</small>
        <small class="text-muted">Total: <span id="komisiCountBadge" class="fw-bold">{{ count($komisis) }}</span> transaksi</small>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT KOMISI BULANAN
     ========================================================================= -->
<div class="modal fade" id="modalEditCL" tabindex="-1" aria-labelledby="modalEditCLLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalEditCLLabel">
                    <i class="bi bi-pencil-square me-2"></i> Edit Pencairan Komisi Bulanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditCL" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editCLUpdateUrl" value="">
                    
                    <!-- Kode Trx CL (Readonly & Immutable) -->
                    <div class="mb-3">
                        <label class="form-label">Kode Trx CL</label>
                        <input type="text" class="form-control bg-light fw-bold" id="editKodeTrxCL" readonly>
                    </div>

                    <!-- Cabang -->
                    <div class="mb-3">
                        <label class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select" id="editCLIdCab" required>
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->id_cab }}">{{ $cab->id_cab }} - {{ $cab->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Transfer -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transfer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="editCLTanggal" placeholder="dd-mm-yyyy" required>
                    </div>

                    <!-- Nominal Komisi -->
                    <div class="mb-3">
                        <label class="form-label">Nominal Komisi (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-success">Rp</span>
                            <input type="text" class="form-control rupiah-input" id="editCLNominal" required>
                        </div>
                    </div>

                    <!-- Keterangan Transfer -->
                    <div class="mb-3">
                        <label class="form-label">Keterangan / Berita Transfer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCLKet" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnUpdateCL">
                        <i class="bi bi-save me-1"></i> Perbarui Komisi
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
        const form = document.getElementById('komisiForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Modal Edit CL
        const modalEditCLEl = document.getElementById('modalEditCL');
        const modalEditCL = modalEditCLEl ? new bootstrap.Modal(modalEditCLEl) : null;
        const formEditCL = document.getElementById('formEditCL');
        const btnUpdateCL = document.getElementById('btnUpdateCL');

        // Event listener klik Edit CL
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-cl');
            if (btn && modalEditCL) {
                e.preventDefault();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                const idCab = btn.getAttribute('data-id-cab');
                const tanggal = btn.getAttribute('data-tanggal');
                const nominal = btn.getAttribute('data-nominal');
                const ket = btn.getAttribute('data-ket');
                const updateUrl = btn.getAttribute('data-update-url');

                document.getElementById('editCLUpdateUrl').value = updateUrl;
                document.getElementById('editKodeTrxCL').value = id;
                document.getElementById('editCLIdCab').value = idCab;
                document.getElementById('editCLTanggal').value = tanggal;
                document.getElementById('editCLNominal').value = nominal;
                document.getElementById('editCLKet').value = ket;

                document.getElementById('modalEditCLLabel').innerHTML = `<i class="bi bi-pencil-square me-2"></i> Edit Komisi (${id})`;
                modalEditCL.show();
            }
        });

        // Submit Form Edit CL
        if (formEditCL) {
            formEditCL.addEventListener('submit', function (e) {
                e.preventDefault();

                const updateUrl = document.getElementById('editCLUpdateUrl').value;
                const idCab = document.getElementById('editCLIdCab').value;
                const tanggal = document.getElementById('editCLTanggal').value;
                const nominalVal = document.getElementById('editCLNominal').value;
                const keterangan = document.getElementById('editCLKet').value.trim();

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                btnUpdateCL.disabled = true;
                btnUpdateCL.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_cab: idCab,
                        tanggal_tf: tanggal,
                        nominal: nominalVal,
                        ket_trx_cl: keterangan
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    btnUpdateCL.disabled = false;
                    btnUpdateCL.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Komisi';
                    if (status >= 200 && status < 300 && body.success) {
                        modalEditCL.hide();
                        showToastAlert(body.message, 'success');
                        setTimeout(() => window.location.reload(), 400);
                    } else {
                        const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal memperbarui komisi.');
                        showToastAlert(errMsg, 'danger');
                    }
                })
                .catch(err => {
                    btnUpdateCL.disabled = false;
                    btnUpdateCL.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Komisi';
                    showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
                });
            });
        }

        // Reset listener
        document.getElementById('btnResetCL').addEventListener('click', function () {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });

        // Submit Form ke MariaDB
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // 1. Validasi Wajib Isi
            const isValid = validateRequiredFields(form);
            if (!isValid) return;

            const kodeTrx = document.getElementById('inputKodeTrxCL').value;
            const idCab = document.getElementById('inputCabangCL').value;
            const tanggal = document.getElementById('inputTanggalTf').value;
            const nominalVal = document.getElementById('inputNominalCL').value;
            const keterangan = document.getElementById('inputKetTrxCL').value.trim();

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch("{{ route('transaksi.komisi.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    kode_trx_cl: kodeTrx,
                    id_cab: idCab,
                    tanggal_tf: tanggal,
                    nominal: nominalVal,
                    ket_trx_cl: keterangan
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Komisi';
                if (status >= 200 && status < 300 && body.success) {
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan transaksi komisi.');
                    showToastAlert(errMsg, 'danger');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Komisi';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
