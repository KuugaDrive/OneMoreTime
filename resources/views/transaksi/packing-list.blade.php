@extends('layouts.app')

@section('title', 'Transaksi Packing List')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="bi bi-clipboard-check-fill text-primary"></i> Transaksi Packing List (PL Komisi Harian)
        </h1>
        <p class="page-subtitle">Input pencatatan kiriman harian, omset total, diskon/charge, serta perhitungan grand total komisi agen.</p>
    </div>
    <div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
            <i class="bi bi-shield-check me-1"></i> Hak Akses: Owner &amp; Admin
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT TRANSAKSI PACKING LIST
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header bg-white">
        <span class="d-flex align-items-center gap-2 text-primary fw-bold">
            <i class="bi bi-pencil-square"></i>
            Form Input Transaksi Packing List Baru
        </span>
    </div>
    <div class="card-body">
        <form id="packingListForm" novalidate>
            <div class="row g-3">
                <!-- *KodeTrxPL (Otomatis & Readonly) -->
                <div class="col-12 col-md-3">
                    <label for="inputKodeTrxPL" class="form-label">
                        Kode Trx PL <span class="badge bg-secondary ms-1">Otomatis</span>
                    </label>
                    <input type="text" class="form-control bg-light" id="inputKodeTrxPL" value="{{ $nextCode ?? 'PL-' . date('Ymd') . '-001' }}" readonly>
                    <small class="text-muted" style="font-size: 0.72rem;">Nomor transaksi unik dari sistem.</small>
                </div>

                <!-- No. PL JNE (Wajib diisi pada transaksi baru) -->
                <div class="col-12 col-md-3">
                    <label for="inputNoPlJNE" class="form-label">
                        No. PL JNE <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-file-earmark-text"></i></span>
                        <input type="text" class="form-control font-monospace" id="inputNoPlJNE" data-field-name="No. PL JNE" placeholder="Contoh: PL/17/2510004621" style="text-transform: uppercase;" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Nomor resmi dari dokumen cetak JNE.</small>
                </div>

                <!-- Cabang (Wajib untuk Owner / Readonly untuk Admin) -->
                <div class="col-12 col-md-3">
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <label for="inputCabangPL" class="form-label">
                            Cabang <span class="badge bg-secondary ms-1">Akun Terikat</span>
                        </label>
                        <input type="hidden" id="inputCabangPL" value="{{ auth()->user()->id_cab }}">
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-buildings-fill text-primary"></i></span>
                            <input type="text" class="form-control bg-light fw-semibold text-primary" value="{{ auth()->user()->cabang ? auth()->user()->cabang->nama_cabang . ' (' . auth()->user()->id_cab . ')' : auth()->user()->id_cab }}" readonly>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Cabang: {{ auth()->user()->cabang ? auth()->user()->cabang->nama_cabang : auth()->user()->id_cab }}</small>
                    @else
                        <label for="inputCabangPL" class="form-label">
                            Cabang <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-buildings"></i></span>
                            <select class="form-select" id="inputCabangPL" data-field-name="Cabang" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $cab)
                                <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Pilih cabang transaksi.</small>
                    @endif
                </div>

                <!-- TanggalPL (Flatpickr) -->
                <div class="col-12 col-md-3">
                    <label for="inputTanggalPL" class="form-label">
                        Tanggal PL <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-calendar3"></i></span>
                        <input type="text" class="form-control datepicker" id="inputTanggalPL" data-field-name="Tanggal PL" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Pilih tanggal pengiriman (dd-mm-yyyy).</small>
                </div>

                <!-- Jumlah Paket -->
                <div class="col-12 col-md-3">
                    <label for="inputJumlahPaket" class="form-label">
                        Jumlah Paket (Koli) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-box-seam"></i></span>
                        <input type="number" min="0" inputmode="numeric" class="form-control numeric-int-only" id="inputJumlahPaket" data-field-name="Jumlah Paket" placeholder="Contoh: 150" required>
                    </div>
                    <small class="text-muted" style="font-size: 0.72rem;">Total koli kiriman per hari (bilangan bulat).</small>
                </div>

                <!-- Total (Rupiah) -->
                <div class="col-12 col-md-3">
                    <label for="inputTotalPL" class="form-label">
                        Total Penjualan / Bruto (Rp) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control rupiah-input" id="inputTotalPL" data-field-name="Total Penjualan" placeholder="0,00" required>
                    </div>
                    <div class="currency-helper-text">Isi dengan Total pada ringkasan Packing List JNE (tidak termasuk COD dan Connote Cancel).</div>
                </div>

                <!-- Discount Charge (Rupiah) -->
                <div class="col-12 col-md-3">
                    <label for="inputDiscountChargePL" class="form-label">
                        Discount Charge (Rp) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control rupiah-input" id="inputDiscountChargePL" data-field-name="Discount Charge" placeholder="0,00" required>
                    </div>
                    <div class="currency-helper-text">Format: 1.500.000,00 (titik ribuan, koma desimal)</div>
                </div>

                <!-- e-Voucher (Rupiah) -->
                <div class="col-12 col-md-3">
                    <label for="inputEVoucherPL" class="form-label">
                        e-Voucher (Rp) <span class="badge bg-light text-muted border ms-1">Default 0,00</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                        <input type="text" inputmode="numeric" class="form-control rupiah-input" id="inputEVoucherPL" data-field-name="e-Voucher" placeholder="0,00" value="0,00">
                    </div>
                    <div class="currency-helper-text">Format: 1.500.000,00 (default 0,00)</div>
                </div>

                <!-- Grand Total (Rupiah, Readonly, Otomatis Total - Discount - eVoucher) -->
                <div class="col-12 col-md-4">
                    <label for="inputGrandTotalPL" class="form-label">
                        Grand Total (Rp) <span class="badge bg-primary ms-1">Auto = Total - Diskon - eVoucher</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold text-primary">Rp</span>
                        <input type="text" class="form-control bg-light fw-bold text-primary" id="inputGrandTotalPL" placeholder="0,00" readonly>
                    </div>
                    <div class="currency-helper-text">Setoran ke JNE (Total - Disc Charge - e-Voucher)</div>
                </div>

                <!-- KetTrxPL -->
                <div class="col-12 col-md-8">
                    <label for="inputKetTrxPL" class="form-label">
                        Keterangan Transaksi PL <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="inputKetTrxPL" data-field-name="Keterangan" placeholder="Contoh: Kiriman reguler counter pagi, pick-up Tokopedia & Shopee" required>
                    <small class="text-muted" style="font-size: 0.72rem;">Catatan operasional transaksi.</small>
                </div>

                <div class="col-12 text-end pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3 me-2" id="btnResetPL">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi PL
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI PACKING LIST
     ========================================================================= -->
<div class="card-app">
    <div class="card-header flex-wrap gap-2">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-primary"></i>
            Daftar Transaksi Packing List
        </span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control table-search-box" data-target-table="plTable" placeholder="Cari kode trx, cabang, tanggal, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="plTable">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Trx PL</th>
                    <th>No. PL JNE</th>
                    <th>Cabang</th>
                    <th>Tanggal PL</th>
                    <th class="text-end">Total Bruto</th>
                    <th class="text-end">Discount Charge</th>
                    <th class="text-end">e-Voucher</th>
                    <th class="text-end">Grand Total</th>
                    <th class="text-center">Jml Paket</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packingLists as $index => $pl)
                <tr data-id="{{ $pl->kode_trx_pl }}">
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td><span class="badge bg-light text-dark border fw-bold">{{ $pl->kode_trx_pl }}</span></td>
                    <td>
                        @if(!empty($pl->no_pl_jne))
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">{{ $pl->no_pl_jne }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light text-primary border">{{ $pl->nama_cabang }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($pl->tanggal_pl)->format('d-m-Y') }}</td>
                    <td class="text-end">Rp {{ number_format($pl->total, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">- Rp {{ number_format($pl->discount_charge, 2, ',', '.') }}</td>
                    <td class="text-end text-secondary">Rp {{ number_format($pl->e_voucher ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($pl->grand_total, 2, ',', '.') }}</td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $pl->jumlah_paket }}</span></td>
                    <td><span class="text-truncate d-inline-block" style="max-width: 180px;">{{ $pl->ket_trx_pl }}</span></td>
                    <td class="text-center">
                        @can('update', $pl)
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-pl me-1"
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
                                    title="Edit Transaksi PL">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endcan

                        @can('delete', $pl)
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row"
                                    data-id="{{ $pl->kode_trx_pl }}"
                                    data-delete-url="{{ route('transaksi.packing-list.destroy', $pl->kode_trx_pl) }}"
                                    data-item-name="{{ $pl->kode_trx_pl }} ({{ \Carbon\Carbon::parse($pl->tanggal_pl)->format('d-m-Y') }})"
                                    title="Hapus Transaksi PL">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endcan

                        @cannot('update', $pl)
                            @cannot('delete', $pl)
                                <span class="badge bg-light text-muted border" title="Transaksi terkunci (hanya dapat diedit pada hari yang sama)">
                                    <i class="bi bi-lock-fill me-1"></i>Terkunci
                                </span>
                            @endcannot
                        @endcannot
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2 px-3">
        <small class="text-muted">Data Transaksi Packing List dari Database MariaDB</small>
        <small class="text-muted">Total: <span id="plCountBadge" class="fw-bold">{{ count($packingLists) }}</span> transaksi</small>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT PACKING LIST
     ========================================================================= -->
<div class="modal fade" id="modalEditPL" tabindex="-1" aria-labelledby="modalEditPLLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalEditPLLabel">
                    <i class="bi bi-pencil-square me-2"></i> Edit Transaksi Packing List
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPL" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editUpdateUrl" value="">
                    <div class="row g-3">
                        <!-- Kode Trx PL (Readonly & Immutable) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Kode Trx PL</label>
                            <input type="text" class="form-control bg-light fw-bold" id="editKodeTrxPL" readonly>
                        </div>

                        <!-- No. PL JNE -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">No. PL JNE <span class="badge bg-light text-muted border ms-1">Dokumen Fisik</span></label>
                            <input type="text" class="form-control font-monospace" id="editNoPlJne" placeholder="PL/17/..." maxlength="30">
                        </div>

                        <!-- Cabang (Owner: Dropdown, Admin: Readonly Text) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Cabang <span class="text-danger">*</span></label>
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
                            <label class="form-label">Tanggal PL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control datepicker" id="editTanggalPL" placeholder="dd-mm-yyyy" required>
                        </div>

                        <!-- Jumlah Paket -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Jumlah Paket (Koli) <span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control numeric-int-only" id="editJumlahPaket" required>
                        </div>

                        <!-- Total (Rupiah) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Total Penjualan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="text" class="form-control rupiah-input" id="editTotalPL" required>
                            </div>
                        </div>

                        <!-- Discount Charge (Rupiah) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Discount Charge (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="text" class="form-control rupiah-input" id="editDiscountChargePL" required>
                            </div>
                        </div>

                        <!-- e-Voucher (Rupiah) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">e-Voucher (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                                <input type="text" class="form-control rupiah-input" id="editEVoucherPL">
                            </div>
                        </div>

                        <!-- Grand Total (Readonly) -->
                        <div class="col-12 col-md-4">
                            <label class="form-label">Grand Total (Rp) <span class="badge bg-primary ms-1">Auto</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-primary">Rp</span>
                                <input type="text" class="form-control bg-light fw-bold text-primary" id="editGrandTotalPL" readonly>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="col-12">
                            <label class="form-label">Keterangan Transaksi PL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editKetTrxPL" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" id="btnUpdatePL">
                        <i class="bi bi-save me-1"></i> Perbarui Transaksi PL
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

        // Kalkulasi Grand Total Edit Form
        const recalcEditGrandTotal = () => {
            const total = parseRupiahInput(document.getElementById('editTotalPL').value) || 0;
            const disc = parseRupiahInput(document.getElementById('editDiscountChargePL').value) || 0;
            const voucher = parseRupiahInput(document.getElementById('editEVoucherPL').value) || 0;
            const grandTotal = total - disc - voucher;
            document.getElementById('editGrandTotalPL').value = formatRupiahDisplay(grandTotal);
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

                document.getElementById('modalEditPLLabel').innerHTML = `<i class="bi bi-pencil-square me-2"></i> Edit Transaksi PL (${id})`;
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
                    btnUpdatePL.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Transaksi PL';
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
                    btnUpdatePL.innerHTML = '<i class="bi bi-save me-1"></i> Perbarui Transaksi PL';
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

        // Submit Form ke MariaDB
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // 1. Validasi Wajib Isi
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
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi PL';
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
                submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Simpan Transaksi PL';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
