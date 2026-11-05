@extends('layouts.app')

@section('title', 'Transaksi Biaya Operasional')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Transaksi Biaya Operasional</h1>
        <p class="page-subtitle">Pencatatan pengeluaran operasional cabang agen (utilitas, bahan packing, armada).</p>
    </div>
    <div>
        <span class="text-secondary small fw-medium">
            Akses: Khusus Owner
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT TRANSAKSI OPERASIONAL
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Input Biaya Operasional Baru</span>
    </div>
    <div class="card-body p-3 p-md-4">
        <form id="operasionalForm" novalidate>
            <div class="row g-3">
                <!-- Kolom Kiri: Informasi Operasional -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Informasi Transaksi
                        </div>

                        <div class="row g-2">
                            <!-- Kode Trx Ops -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputKodeTrxOps" class="form-label small fw-semibold text-dark">Kode Trx Ops</label>
                                <input type="text" class="form-control bg-light font-monospace" id="inputKodeTrxOps" value="{{ $nextCode ?? 'OPS-' . date('Ymd') . '-001' }}" readonly>
                            </div>

                            <!-- Cabang -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputCabangOps" class="form-label small fw-semibold text-dark">
                                    Cabang <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="inputCabangOps" data-field-name="Cabang" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($cabangs as $cab)
                                    <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal Trx Ops -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputTanggalTrxOps" class="form-label small fw-semibold text-dark">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control datepicker" id="inputTanggalTrxOps" data-field-name="Tanggal Transaksi" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                            </div>

                            <!-- Beban Operasional -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputBebanOps" class="form-label small fw-semibold text-dark">
                                    Beban Operasional <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="inputBebanOps" data-field-name="Beban Operasional" required>
                                    <option value="">-- Pilih Kategori Beban --</option>
                                    @forelse($opsList as $ops)
                                    <option value="{{ $ops['beban_ops'] }}">
                                        [{{ $ops['id_ops'] }}] {{ $ops['beban_ops'] }}
                                    </option>
                                    @empty
                                    <option value="" disabled>Belum ada data beban operasional</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Rincian Nominal & Keterangan -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Rincian Pengeluaran
                        </div>

                        <div class="mb-3">
                            <label for="inputNominalOps" class="form-label small fw-semibold text-dark">
                                Nominal Biaya (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary small">Rp</span>
                                <input type="text" inputmode="numeric" class="form-control rupiah-input text-end tabular-nums" id="inputNominalOps" data-field-name="Nominal" placeholder="0,00" required>
                            </div>
                            <div class="form-text small text-secondary" style="font-size: 11px;">Contoh pengisian: 150.000,00</div>
                        </div>

                        <div class="mb-2">
                            <label for="inputKetTrxOps" class="form-label small fw-semibold text-dark">
                                Keterangan Pengeluaran <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="inputKetTrxOps" rows="2" data-field-name="Keterangan Ops" placeholder="Rincian peruntukan pengeluaran operasional" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3" id="btnResetOps">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Simpan Operasional
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI OPERASIONAL
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Transaksi Biaya Operasional</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="opsTrxTable" placeholder="Cari kode, cabang, beban, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="opsTrxTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Kode Trx</th>
                    <th>Cabang</th>
                    <th>Tanggal</th>
                    <th>Beban Operasional</th>
                    <th class="text-end">Nominal Pengeluaran</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($operasionals as $index => $op)
                <tr data-id="{{ $op->kode_trx_ops }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $op->kode_trx_ops }}</span></td>
                    <td><span class="text-dark">{{ $op->nama_cabang }}</span></td>
                    <td class="tabular-nums">{{ \Carbon\Carbon::parse($op->tanggal_trx_ops)->format('d-m-Y') }}</td>
                    <td><span class="text-dark">{{ $op->bebanOps ? $op->bebanOps->beban_ops : '-' }}</span></td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($op->nominal, 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 240px;">{{ $op->ket_trx_ops }}</span></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1 align-items-center">
                            @can('update', $op)
                                <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-ops"
                                        data-id="{{ $op->kode_trx_ops }}"
                                        data-id-cab="{{ $op->id_cab }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($op->tanggal_trx_ops)->format('d-m-Y') }}"
                                        data-beban-ops-id="{{ $op->beban_ops_id }}"
                                        data-nominal="{{ number_format($op->nominal, 2, ',', '.') }}"
                                        data-ket="{{ $op->ket_trx_ops }}"
                                        data-update-url="{{ route('transaksi.operasional.update', $op->kode_trx_ops) }}"
                                        title="Ubah Transaksi Ops"
                                        aria-label="Ubah Transaksi Ops">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endcan

                            @can('delete', $op)
                                <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                        data-id="{{ $op->kode_trx_ops }}"
                                        data-delete-url="{{ route('transaksi.operasional.destroy', $op->kode_trx_ops) }}"
                                        data-item-name="{{ $op->kode_trx_ops }} ({{ $op->bebanOps ? $op->bebanOps->beban_ops : $op->beban_ops_id }})"
                                        title="Hapus Transaksi Ops"
                                        aria-label="Hapus Transaksi Ops">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan

                            @cannot('update', $op)
                                @cannot('delete', $op)
                                    <span class="text-secondary small">-</span>
                                @endcannot
                            @endcannot
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-secondary small">Total transaksi: <strong id="opsTrxCountBadge" class="text-dark">{{ count($operasionals) }}</strong></span>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT OPERASIONAL
     ========================================================================= -->
<div class="modal fade" id="modalEditOps" tabindex="-1" aria-labelledby="modalEditOpsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="modalEditOpsLabel">
                    Ubah Biaya Operasional
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEditOps" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editOpsUpdateUrl" value="">
                    
                    <!-- Kode Trx -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Kode Trx Ops</label>
                        <input type="text" class="form-control bg-light font-monospace" id="editKodeTrxOps" readonly>
                    </div>

                    <!-- Cabang -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select" id="editOpsIdCab" required>
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->id_cab }}">{{ $cab->id_cab }} - {{ $cab->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Trx -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="editOpsTanggal" placeholder="dd-mm-yyyy" required>
                    </div>

                    <!-- Beban Operasional -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Beban Operasional <span class="text-danger">*</span></label>
                        <select class="form-select" id="editOpsBeban" required>
                            @foreach($opsList as $ops)
                                <option value="{{ $ops['id_ops'] }}">{{ $ops['beban_ops'] }} ({{ $ops['id_ops'] }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nominal -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary small">Rp</span>
                            <input type="text" class="form-control rupiah-input text-end tabular-nums" id="editOpsNominal" required>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Keterangan Biaya <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editOpsKet" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnUpdateOps">
                        Simpan Perubahan
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

                document.getElementById('modalEditOpsLabel').textContent = 'Ubah Operasional (' + id + ')';
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
                    btnUpdateOps.textContent = 'Simpan Perubahan';
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
                    btnUpdateOps.textContent = 'Simpan Perubahan';
                    showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
                });
            });
        }

        // Reset listener
        document.getElementById('btnResetOps').addEventListener('click', function () {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.dynamic-required').forEach(el => el.remove());
        });

        // Submit Form ke Backend
        form.addEventListener('submit', function (e) {
            e.preventDefault();

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
                submitBtn.textContent = 'Simpan Operasional';
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
                submitBtn.textContent = 'Simpan Operasional';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
