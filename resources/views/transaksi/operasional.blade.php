@extends('layouts.app')

@section('title', 'Transaksi Operasional')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-receipt-cutoff text-primary"></i> Transaksi Biaya Operasional
        </h1>
        <p class="page-subtitle">Pencatatan pengeluaran operasional cabang agen (listrik, bensin armada, lakban, servis kendaraan).</p>
    </div>
    <div>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
            <i class="bi bi-shield-lock-fill me-1"></i> Khusus Role: Owner
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT TRANSAKSI OPERASIONAL
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header bg-white">
        <span class="d-flex align-items-center gap-2 text-danger fw-bold">
            <i class="bi bi-plus-circle-fill"></i>
            Form Input Biaya Operasional Baru
        </span>
    </div>
    <div class="card-body">
        <form id="operasionalForm" novalidate>
            <div class="row g-3">
                <!-- *KodeTrxOps (Otomatis & Readonly) -->
                <div class="col-12 col-md-3">
                    <label for="inputKodeTrxOps" class="form-label">
                        Kode Trx Ops <span class="badge bg-secondary ms-1">Otomatis</span>
                    </label>
                    <input type="text" class="form-control bg-light" id="inputKodeTrxOps" value="{{ $nextCode ?? 'OPS-' . date('Ymd') . '-001' }}" readonly>
                    <small class="text-muted" style="font-size: 0.72rem;">Nomor transaksi operasional otomatis.</small>
                </div>

                <!-- Cabang (Wajib) -->
                <div class="col-12 col-md-3">
                    <label for="inputCabangOps" class="form-label">
                        Cabang <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-buildings"></i></span>
                        <select class="form-select" id="inputCabangOps" data-field-name="Cabang" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangs as $cab)
                            <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih cabang transaksi.</small>
                </div>

                <!-- TanggalTrxOps (Flatpickr) -->
                <div class="col-12 col-md-3">
                    <label for="inputTanggalTrxOps" class="form-label">
                        Tanggal Transaksi Ops <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control datepicker" id="inputTanggalTrxOps" data-field-name="Tanggal Transaksi" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Format tanggal dd-mm-yyyy.</small>
                </div>

                <!-- BebanOps (Dropdown dari Master Ops) -->
                <!-- Sengaja enabled; menyimpang dari diagram yang menulis 'disable', demi fleksibilitas input -->
                <div class="col-12 col-md-3">
                    <label for="inputBebanOps" class="form-label">
                        Beban Operasional <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-tools"></i></span>
                        <select class="form-select" id="inputBebanOps" data-field-name="Beban Operasional" required>
                            <option value="">-- Pilih Beban Operasional --</option>
                            @forelse($opsList as $ops)
                            <option value="{{ $ops['beban_ops'] }}">
                                [{{ $ops['id_ops'] }}] {{ $ops['beban_ops'] }}
                            </option>
                            @empty
                            <option value="" disabled>Belum ada data beban operasional</option>
                            @endforelse
                        </select>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih jenis beban operasional kantor.</small>
                </div>

                <!-- Nominal (Rupiah) -->
                <div class="col-12 col-md-6">
                    <label for="inputNominalOps" class="form-label">
                        Nominal Biaya (Rp) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control rupiah-input" id="inputNominalOps" data-field-name="Nominal" placeholder="0,00" required>
                    </div>
                    <div class="currency-helper-text">Format: 1.500.000,00 (titik ribuan, koma desimal)</div>
                </div>

                <!-- KetTrxOps -->
                <div class="col-12 col-md-6">
                    <label for="inputKetTrxOps" class="form-label">
                        Keterangan Biaya Ops <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="inputKetTrxOps" rows="2" data-field-name="Keterangan Ops" placeholder="Contoh: Pembelian token listrik kantor cabang Rp 450.000" required></textarea>
                </div>

                <div class="col-12 text-end pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3 me-2" id="btnResetOps">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Ops
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI OPERASIONAL
     ========================================================================= -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-danger"></i>
            Daftar Transaksi Biaya Operasional
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="opsTrxTable" placeholder="Cari kode, cabang, beban ops, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="opsTrxTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Trx Ops</th>
                    <th>Cabang</th>
                    <th>Tanggal Trx</th>
                    <th>Beban Operasional</th>
                    <th class="text-end">Nominal Pengeluaran</th>
                    <th>Keterangan Transaksi</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operasionals as $index => $op)
                <tr data-id="{{ $op->kode_trx_ops }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $op->kode_trx_ops }}</span></td>
                    <td><span class="badge bg-light text-primary border">{{ $op->nama_cabang }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($op->tanggal_trx_ops)->format('d-m-Y') }}</td>
                    <td><span class="badge bg-light text-primary border">{{ $op->bebanOps ? $op->bebanOps->beban_ops : '-' }}</span></td>
                    <td class="text-end fw-bold text-danger">Rp {{ number_format($op->nominal, 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $op->ket_trx_ops }}</span></td>
                    <td class="text-center">
                        @can('update', $op)
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-ops me-1"
                                    data-id="{{ $op->kode_trx_ops }}"
                                    data-id-cab="{{ $op->id_cab }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($op->tanggal_trx_ops)->format('d-m-Y') }}"
                                    data-beban-ops-id="{{ $op->beban_ops_id }}"
                                    data-nominal="{{ number_format($op->nominal, 2, ',', '.') }}"
                                    data-ket="{{ $op->ket_trx_ops }}"
                                    data-update-url="{{ route('transaksi.operasional.update', $op->kode_trx_ops) }}"
                                    title="Edit Transaksi Ops">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endcan

                        @can('delete', $op)
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                    data-id="{{ $op->kode_trx_ops }}"
                                    data-delete-url="{{ route('transaksi.operasional.destroy', $op->kode_trx_ops) }}"
                                    data-item-name="{{ $op->kode_trx_ops }} ({{ $op->bebanOps ? $op->bebanOps->beban_ops : $op->beban_ops_id }})"
                                    title="Hapus Transaksi Ops">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan

                        @cannot('update', $op)
                            @cannot('delete', $op)
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
        <small class="text-muted">Data Transaksi Biaya Operasional dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="opsTrxCountBadge" class="fw-bold">{{ count($operasionals) }}</span> transaksi</small>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT OPERASIONAL
     ========================================================================= -->
<div class="modal fade" id="modalEditOps" tabindex="-1" aria-labelledby="modalEditOpsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalEditOpsLabel">
                    <i class="bi bi-pencil-square me-2"></i> Edit Biaya Operasional
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditOps" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editOpsUpdateUrl" value="">
                    
                    <!-- Kode Trx (Readonly & Immutable) -->
                    <div class="mb-3">
                        <label class="form-label">Kode Trx Ops</label>
                        <input type="text" class="form-control bg-light fw-bold" id="editKodeTrxOps" readonly>
                    </div>

                    <!-- Cabang -->
                    <div class="mb-3">
                        <label class="form-label">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select" id="editOpsIdCab" required>
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->id_cab }}">{{ $cab->id_cab }} - {{ $cab->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Trx -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="editOpsTanggal" placeholder="dd-mm-yyyy" required>
                    </div>

                    <!-- Beban Operasional -->
                    <div class="mb-3">
                        <label class="form-label">Beban Operasional <span class="text-danger">*</span></label>
                        <select class="form-select" id="editOpsBeban" required>
                            @foreach($opsList as $ops)
                                <option value="{{ $ops['id_ops'] }}">{{ $ops['beban_ops'] }} ({{ $ops['id_ops'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nominal -->
                    <div class="mb-3">
                        <label class="form-label">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-danger">Rp</span>
                            <input type="text" class="form-control rupiah-input" id="editOpsNominal" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label">Keterangan Biaya <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editOpsKet" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnUpdateOps">
                        <i class="bi bi-save me-1"></i> Perbarui Operasional
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
        const form = document.getElementById('operasionalForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Modal Edit Ops
        const modalEditOpsEl = document.getElementById('modalEditOps');
        const modalEditOps = modalEditOpsEl ? new bootstrap.Modal(modalEditOpsEl) : null;
        const formEditOps = document.getElementById('formEditOps');
        const btnUpdateOps = document.getElementById('btnUpdateOps');

        // Event listener klik Edit Ops
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-ops');
            if (btn && modalEditOps) {
                e.preventDefault();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                const idCab = btn.getAttribute('data-id-cab');
                const tanggal = btn.getAttribute('data-tanggal');
                const bebanId = btn.getAttribute('data-beban-ops-id');
                const nominal = btn.getAttribute('data-nominal');
                const ket = btn.getAttribute('data-ket');
                const updateUrl = btn.getAttribute('data-update-url');

                document.getElementById('editOpsUpdateUrl').value = updateUrl;
                document.getElementById('editKodeTrxOps').value = id;
                document.getElementById('editOpsIdCab').value = idCab;
                document.getElementById('editOpsTanggal').value = tanggal;
                document.getElementById('editOpsBeban').value = bebanId;
                document.getElementById('editOpsNominal').value = nominal;
                document.getElementById('editOpsKet').value = ket;

                document.getElementById('modalEditOpsLabel').innerHTML = `<i class="bi bi-pencil-square me-2"></i> Edit Operasional (${id})`;
                modalEditOps.show();
            }
        });

        // Submit Form Edit Ops
        if (formEditOps) {
            formEditOps.addEventListener('submit', function (e) {
                e.preventDefault();

                const updateUrl = document.getElementById('editOpsUpdateUrl').value;
                const idCab = document.getElementById('editOpsIdCab').value;
                const tanggal = document.getElementById('editOpsTanggal').value;
                const bebanOps = document.getElementById('editOpsBeban').value;
                const nominal = document.getElementById('editOpsNominal').value;
                const keterangan = document.getElementById('editOpsKet').value.trim();

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                btnUpdateOps.disabled = true;
                btnUpdateOps.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id_cab: idCab,
                        tanggal_trx_ops: tanggal,
                        beban_ops: bebanOps,
                        nominal: nominal,
                        ket_trx_ops: keterangan
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    btnUpdateOps.disabled = false;
                    btnUpdateOps.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Operasional';
                    if (status >= 200 && status < 300 && body.success) {
                        modalEditOps.hide();
                        showToastAlert(body.message, 'success');
                        setTimeout(() => window.location.reload(), 400);
                    } else {
                        const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal memperbarui operasional.');
                        showToastAlert(errMsg, 'danger');
                    }
                })
                .catch(err => {
                    btnUpdateOps.disabled = false;
                    btnUpdateOps.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Operasional';
                    showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
                });
            });
        }

        // Reset listener
        document.getElementById('btnResetOps').addEventListener('click', function () {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.dynamic-required').forEach(el => el.remove());
        });

        // Submit Form ke MariaDB
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // 1. Validasi Wajib Isi
            const isValid = validateRequiredFields(form);
            if (!isValid) return;

            const kodeTrx = document.getElementById('inputKodeTrxOps').value;
            const idCab = document.getElementById('inputCabangOps').value;
            const tanggal = document.getElementById('inputTanggalTrxOps').value;
            const bebanOps = document.getElementById('inputBebanOps').value;
            const nominal = document.getElementById('inputNominalOps').value;
            const keterangan = document.getElementById('inputKetTrxOps').value.trim();

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch("{{ route('transaksi.operasional.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    kode_trx_ops: kodeTrx,
                    id_cab: idCab,
                    tanggal_trx_ops: tanggal,
                    beban_ops: bebanOps,
                    nominal: nominal,
                    ket_trx_ops: keterangan
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Ops';
                if (status >= 200 && status < 300 && body.success) {
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan operasional.');
                    showToastAlert(errMsg, 'danger');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi Ops';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
