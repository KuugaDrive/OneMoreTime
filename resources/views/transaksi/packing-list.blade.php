@extends('layouts.app')

@section('title', 'Transaksi Packing List')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Transaksi Packing List</h1>
        <p class="page-subtitle">Pencatatan kiriman harian, omset penjualan bruto, dan komisi agen.</p>
    </div>
    <div>
        <span class="text-secondary small fw-medium">
            Akses: {{ auth()->user()->isOwner() ? 'Owner (Semua Cabang)' : 'Admin (' . (auth()->user()->cabang ? auth()->user()->cabang->nama_cabang : auth()->user()->id_cab) . ')' }}
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT TRANSAKSI PACKING LIST
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Input Transaksi Baru</span>
    </div>
    <div class="card-body p-3 p-md-4">
        <form id="packingListForm" novalidate>
            <div class="row g-3">
                <!-- Kolom Kiri: Informasi Dokumen -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Informasi Dokumen
                        </div>

                        <div class="row g-2">
                            <!-- Kode Trx PL -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputKodeTrxPL" class="form-label small fw-semibold text-dark">Kode Trx PL</label>
                                <input type="text" class="form-control bg-light font-monospace" id="inputKodeTrxPL" value="{{ $nextCode ?? 'PL-' . date('Ymd') . '-001' }}" readonly>
                            </div>

                            <!-- No. PL JNE -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputNoPlJNE" class="form-label small fw-semibold text-dark">
                                    No. PL JNE <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control font-monospace" id="inputNoPlJNE" data-field-name="No. PL JNE" placeholder="PL/17/..." style="text-transform: uppercase;" required>
                            </div>

                            <!-- Cabang -->
                            <div class="col-12 col-sm-6 mb-2">
                                @if(auth()->user() && auth()->user()->isAdmin())
                                    <label for="inputCabangPL" class="form-label small fw-semibold text-dark">Cabang</label>
                                    <input type="hidden" id="inputCabangPL" value="{{ auth()->user()->id_cab }}">
                                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->cabang ? auth()->user()->cabang->nama_cabang : auth()->user()->id_cab }}" readonly>
                                @else
                                    <label for="inputCabangPL" class="form-label small fw-semibold text-dark">
                                        Cabang <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="inputCabangPL" data-field-name="Cabang" required>
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach($cabangs as $cab)
                                        <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <!-- Tanggal PL -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputTanggalPL" class="form-label small fw-semibold text-dark">
                                    Tanggal PL <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control datepicker" id="inputTanggalPL" data-field-name="Tanggal PL" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                            </div>

                            <!-- Jumlah Paket -->
                            <div class="col-12 mb-2">
                                <label for="inputJumlahPaket" class="form-label small fw-semibold text-dark">
                                    Jumlah Paket (Koli) <span class="text-danger">*</span>
                                </label>
                                <input type="number" min="0" inputmode="numeric" class="form-control numeric-int-only tabular-nums" id="inputJumlahPaket" data-field-name="Jumlah Paket" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Rincian Keuangan -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Rincian Keuangan
                        </div>

                        <div class="row g-2">
                            <!-- Total Bruto -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputTotalPL" class="form-label small fw-semibold text-dark">
                                    Total Penjualan Bruto <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary small">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control rupiah-input text-end tabular-nums" id="inputTotalPL" data-field-name="Total Penjualan" placeholder="0,00" required>
                                </div>
                            </div>

                            <!-- Discount Charge -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputDiscountChargePL" class="form-label small fw-semibold text-dark">
                                    Discount Charge (Komisi) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary small">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control rupiah-input text-end tabular-nums" id="inputDiscountChargePL" data-field-name="Discount Charge" placeholder="0,00" required>
                                </div>
                            </div>

                            <!-- e-Voucher -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputEVoucherPL" class="form-label small fw-semibold text-dark">
                                    e-Voucher
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary small">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control rupiah-input text-end tabular-nums" id="inputEVoucherPL" data-field-name="e-Voucher" placeholder="0,00" value="0,00">
                                </div>
                            </div>

                            <!-- Grand Total (Readonly & Prominent) -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputGrandTotalPL" class="form-label small fw-semibold text-dark">
                                    Grand Total (Setoran)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary small">Rp</span>
                                    <input type="text" class="form-control bg-light fw-bold text-end tabular-nums text-primary" id="inputGrandTotalPL" placeholder="0,00" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-text small text-secondary mt-1" style="font-size: 11px;">
                            Rumus Grand Total = Total Bruto - Discount Charge - e-Voucher.
                        </div>
                    </div>
                </div>

                <!-- Keterangan Transaksi -->
                <div class="col-12">
                    <label for="inputKetTrxPL" class="form-label small fw-semibold text-dark">
                        Keterangan Transaksi <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="inputKetTrxPL" data-field-name="Keterangan" placeholder="Catatan transaksi kiriman harian" required>
                </div>

                <!-- Tombol Aksi -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3" id="btnResetPL">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI PACKING LIST
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Daftar Transaksi Packing List</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="plTable" placeholder="Cari kode, no. PL, cabang, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="plTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Kode Trx</th>
                    <th>No. PL JNE</th>
                    <th>Cabang</th>
                    <th>Tanggal</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Disc Charge</th>
                    <th class="text-end">e-Voucher</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-center">Paket</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packingLists as $index => $pl)
                <tr data-id="{{ $pl->kode_trx_pl }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $pl->kode_trx_pl }}</span></td>
                    <td>
                        @if(!empty($pl->no_pl_jne))
                            <span class="font-monospace text-secondary small">{{ $pl->no_pl_jne }}</span>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td><span class="text-dark">{{ $pl->nama_cabang }}</span></td>
                    <td class="tabular-nums">{{ \Carbon\Carbon::parse($pl->tanggal_pl)->format('d-m-Y') }}</td>
                    <td class="text-end tabular-nums">Rp {{ number_format($pl->total, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($pl->discount_charge, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums text-secondary">Rp {{ number_format($pl->e_voucher ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end tabular-nums fw-semibold text-dark">Rp {{ number_format($pl->grand_total, 2, ',', '.') }}</td>
                    <td class="text-center tabular-nums">{{ $pl->jumlah_paket }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 160px;">{{ $pl->ket_trx_pl }}</span></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1 align-items-center">
                            @can('update', $pl)
                                <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-pl"
                                        data-id="{{ $pl->kode_trx_pl }}"
                                        data-no-pl-jne="{{ $pl->no_pl_jne }}"
                                        data-id-cab="{{ $pl->id_cab }}"
                                        data-nama-cabang="{{ $pl->nama_cabang }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($pl->tanggal_pl)->format('d-m-Y') }}"
                                        data-total="{{ number_format($pl->total, 2, ',', '.') }}"
                                        data-discount-charge="{{ number_format($pl->discount_charge, 2, ',', '.') }}"
                                        data-e-voucher="{{ number_format($pl->e_voucher ?? 0, 2, ',', '.') }}"
                                        data-grand-total="{{ number_format($pl->grand_total, 2, ',', '.') }}"
                                        data-jumlah-paket="{{ $pl->jumlah_paket }}"
                                        data-ket="{{ $pl->ket_trx_pl }}"
                                        data-update-url="{{ route('transaksi.packing-list.update', $pl->kode_trx_pl) }}"
                                        title="Ubah Transaksi PL"
                                        aria-label="Ubah Transaksi PL">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endcan

                            @can('delete', $pl)
                                <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                        data-id="{{ $pl->kode_trx_pl }}"
                                        data-delete-url="{{ route('transaksi.packing-list.destroy', $pl->kode_trx_pl) }}"
                                        data-item-name="{{ $pl->kode_trx_pl }} ({{ \Carbon\Carbon::parse($pl->tanggal_pl)->format('d-m-Y') }})"
                                        title="Hapus Transaksi PL"
                                        aria-label="Hapus Transaksi PL">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan

                            @cannot('update', $pl)
                                @cannot('delete', $pl)
                                    <span class="badge badge-locked" title="Transaksi terkunci (hanya dapat diubah pada hari pembuatan)">Terkunci</span>
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
        <span class="text-secondary small">Total transaksi: <strong id="plCountBadge" class="text-dark">{{ count($packingLists) }}</strong></span>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT PACKING LIST
     ========================================================================= -->
<div class="modal fade" id="modalEditPL" tabindex="-1" aria-labelledby="modalEditPLLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="modalEditPLLabel">
                    Ubah Transaksi Packing List
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEditPL" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editUpdateUrl" value="">
                    <div class="row g-3">
                        <!-- Kode Trx PL -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Kode Trx PL</label>
                            <input type="text" class="form-control bg-light font-monospace" id="editKodeTrxPL" readonly>
                        </div>

                        <!-- No. PL JNE -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">No. PL JNE</label>
                            <input type="text" class="form-control font-monospace" id="editNoPlJne" placeholder="PL/17/..." maxlength="30">
                        </div>

                        <!-- Cabang -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Cabang <span class="text-danger">*</span></label>
                            @if(auth()->user()->isOwner())
                                <select class="form-select" id="editIdCab" required>
                                    @foreach($cabangs as $cab)
                                        <option value="{{ $cab->id_cab }}">{{ $cab->id_cab }} - {{ $cab->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control bg-light" id="editNamaCabangAdmin" value="{{ auth()->user()->cabang ? auth()->user()->cabang->nama_cabang : auth()->user()->id_cab }}" readonly>
                                <input type="hidden" id="editIdCab" value="{{ auth()->user()->id_cab }}">
                            @endif
                        </div>

                        <!-- Tanggal PL -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Tanggal PL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker" id="editTanggalPL" placeholder="dd-mm-yyyy" required>
                        </div>

                        <!-- Jumlah Paket -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Jumlah Paket (Koli) <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control numeric-int-only tabular-nums" id="editJumlahPaket" required>
                        </div>

                        <!-- Total (Rupiah) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Total Penjualan Bruto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary small">Rp</span>
                                <input type="text" class="form-control rupiah-input text-end tabular-nums" id="editTotalPL" required>
                            </div>
                        </div>

                        <!-- Discount Charge -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Discount Charge <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary small">Rp</span>
                                <input type="text" class="form-control rupiah-input text-end tabular-nums" id="editDiscountChargePL" required>
                            </div>
                        </div>

                        <!-- e-Voucher -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">e-Voucher</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary small">Rp</span>
                                <input type="text" class="form-control rupiah-input text-end tabular-nums" id="editEVoucherPL">
                            </div>
                        </div>

                        <!-- Grand Total -->
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Grand Total</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary small">Rp</span>
                                <input type="text" class="form-control bg-light fw-bold text-end tabular-nums text-primary" id="editGrandTotalPL" readonly>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-dark">Keterangan Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editKetTrxPL" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnUpdatePL">
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
        const form = document.getElementById('packingListForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        // Modal Edit PL
        const modalEditEl = document.getElementById('modalEditPL');
        const modalEdit = modalEditEl ? new bootstrap.Modal(modalEditEl) : null;
        const formEdit = document.getElementById('formEditPL');
        const btnUpdatePL = document.getElementById('btnUpdatePL');

        // Helper parser & formatter
        const parseFn = typeof parseRupiah === 'function' ? parseRupiah : (val) => parseFloat(val) || 0;
        const formatFn = typeof formatRupiahNumberOnly === 'function' ? formatRupiahNumberOnly : (val) => val.toString();

        // Kalkulasi Grand Total Edit Form
        const recalcEditGrandTotal = () => {
            const total = parseFn(document.getElementById('editTotalPL').value) || 0;
            const disc = parseFn(document.getElementById('editDiscountChargePL').value) || 0;
            const voucher = parseFn(document.getElementById('editEVoucherPL').value) || 0;
            const grandTotal = Math.max(0, total - disc - voucher);
            document.getElementById('editGrandTotalPL').value = formatFn(grandTotal);
        };

        if (document.getElementById('editTotalPL')) {
            document.getElementById('editTotalPL').addEventListener('input', recalcEditGrandTotal);
            document.getElementById('editDiscountChargePL').addEventListener('input', recalcEditGrandTotal);
            document.getElementById('editEVoucherPL').addEventListener('input', recalcEditGrandTotal);
        }

        // Event listener klik tombol Edit PL
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-pl');
            if (btn && modalEdit) {
                e.preventDefault();
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                const id = btn.getAttribute('data-id');
                const noPlJne = btn.getAttribute('data-no-pl-jne') || '';
                const idCab = btn.getAttribute('data-id-cab') || '';
                const tanggal = btn.getAttribute('data-tanggal') || '';
                const total = btn.getAttribute('data-total') || '';
                const disc = btn.getAttribute('data-discount-charge') || '';
                const voucher = btn.getAttribute('data-e-voucher') || '0,00';
                const grandTotal = btn.getAttribute('data-grand-total') || '';
                const jmlPaket = btn.getAttribute('data-jumlah-paket') || '';
                const ket = btn.getAttribute('data-ket') || '';
                const updateUrl = btn.getAttribute('data-update-url') || '';

                document.getElementById('editUpdateUrl').value = updateUrl;
                document.getElementById('editKodeTrxPL').value = id;
                document.getElementById('editNoPlJne').value = noPlJne;
                if (document.getElementById('editIdCab')) {
                    document.getElementById('editIdCab').value = idCab;
                }
                document.getElementById('editTanggalPL').value = tanggal;
                document.getElementById('editJumlahPaket').value = jmlPaket;
                document.getElementById('editTotalPL').value = total;
                document.getElementById('editDiscountChargePL').value = disc;
                document.getElementById('editEVoucherPL').value = voucher;
                document.getElementById('editGrandTotalPL').value = grandTotal;
                document.getElementById('editKetTrxPL').value = ket;

                document.getElementById('modalEditPLLabel').textContent = 'Ubah Transaksi PL (' + id + ')';
                modalEdit.show();
            }
        });

        // Submit Form Edit PL
        if (formEdit) {
            formEdit.addEventListener('submit', function (e) {
                e.preventDefault();

                const updateUrl = document.getElementById('editUpdateUrl').value;
                const noPlJne = document.getElementById('editNoPlJne').value.trim();
                const idCab = document.getElementById('editIdCab').value;
                const tanggal = document.getElementById('editTanggalPL').value;
                const jmlPaket = document.getElementById('editJumlahPaket').value;
                const total = document.getElementById('editTotalPL').value;
                const discCharge = document.getElementById('editDiscountChargePL').value;
                const eVoucher = document.getElementById('editEVoucherPL').value;
                const keterangan = document.getElementById('editKetTrxPL').value.trim();

                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                btnUpdatePL.disabled = true;
                btnUpdatePL.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        no_pl_jne: noPlJne,
                        id_cab: idCab,
                        tanggal_pl: tanggal,
                        jumlah_paket: jmlPaket,
                        total: total,
                        discount_charge: discCharge,
                        e_voucher: eVoucher,
                        ket_trx_pl: keterangan
                    })
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    btnUpdatePL.disabled = false;
                    btnUpdatePL.textContent = 'Simpan Perubahan';
                    if (status >= 200 && status < 300 && body.success) {
                        modalEdit.hide();
                        showToastAlert(body.message, 'success');
                        setTimeout(() => window.location.reload(), 400);
                    } else {
                        const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal memperbarui transaksi.');
                        showToastAlert(errMsg, 'danger');
                    }
                })
                .catch(err => {
                    btnUpdatePL.disabled = false;
                    btnUpdatePL.textContent = 'Simpan Perubahan';
                    showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
                });
            });
        }

        // Reset listener
        document.getElementById('btnResetPL').addEventListener('click', function () {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.getElementById('inputNoPlJNE').value = '';
            document.getElementById('inputEVoucherPL').value = '0,00';
            document.getElementById('inputGrandTotalPL').value = '0,00';
        });

        // Submit Form ke Backend
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValid = validateRequiredFields(form);
            if (!isValid) return;

            const kodeTrx = document.getElementById('inputKodeTrxPL').value;
            const noPlJne = document.getElementById('inputNoPlJNE').value.trim();
            const idCab = document.getElementById('inputCabangPL').value;
            const tanggal = document.getElementById('inputTanggalPL').value;
            const jmlPaket = document.getElementById('inputJumlahPaket').value;
            const total = document.getElementById('inputTotalPL').value;
            const discCharge = document.getElementById('inputDiscountChargePL').value;
            const eVoucher = document.getElementById('inputEVoucherPL').value;
            const keterangan = document.getElementById('inputKetTrxPL').value.trim();

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch("{{ route('transaksi.packing-list.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    kode_trx_pl: kodeTrx,
                    no_pl_jne: noPlJne,
                    id_cab: idCab,
                    tanggal_pl: tanggal,
                    jumlah_paket: jmlPaket,
                    total: total,
                    discount_charge: discCharge,
                    e_voucher: eVoucher,
                    ket_trx_pl: keterangan
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Transaksi';
                if (status >= 200 && status < 300 && body.success) {
                    showToastAlert(body.message, 'success');
                    setTimeout(() => window.location.reload(), 400);
                } else {
                    const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal menyimpan transaksi.');
                    showToastAlert(errMsg, 'danger');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Transaksi';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
