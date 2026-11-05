@extends('layouts.app')

@section('title', 'Transaksi Komisi Bulanan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Transaksi Komisi Bulanan (CL)</h1>
        <p class="page-subtitle">Pencatatan penerimaan transfer pembayaran komisi bulanan dari kantor pusat JNE.</p>
    </div>
    <div>
        <span class="text-secondary small fw-medium">
            Akses: Khusus Owner
        </span>
    </div>
</div>

<!-- =========================================================================
     FORM INPUT MANUAL TRANSAKSI KOMISI BULANAN
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Input Penerimaan Komisi Baru</span>
    </div>
    <div class="card-body p-3 p-md-4">
        <form id="komisiForm" novalidate>
            <div class="row g-3">
                <!-- Kolom Kiri: Informasi Transfer -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Informasi Transfer
                        </div>

                        <div class="row g-2">
                            <!-- Kode Trx CL -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputKodeTrxCL" class="form-label small fw-semibold text-dark">Kode Trx CL</label>
                                <input type="text" class="form-control bg-light font-monospace" id="inputKodeTrxCL" value="{{ $nextCode ?? 'CL-' . date('Ymd') . '-001' }}" readonly>
                            </div>

                            <!-- Cabang -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputCabangCL" class="form-label small fw-semibold text-dark">
                                    Cabang <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="inputCabangCL" data-field-name="Cabang" required>
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach($cabangs as $cab)
                                    <option value="{{ $cab->id_cab }}">{{ $cab->nama_cabang }} ({{ $cab->id_cab }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal Transfer -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputTanggalTf" class="form-label small fw-semibold text-dark">
                                    Tanggal Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control datepicker" id="inputTanggalTf" data-field-name="Tanggal Transfer" placeholder="dd-mm-yyyy" value="{{ date('d-m-Y') }}" required>
                            </div>

                            <!-- Nominal Komisi -->
                            <div class="col-12 col-sm-6 mb-2">
                                <label for="inputNominalCL" class="form-label small fw-semibold text-dark">
                                    Nominal Komisi (Rp) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary small">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control rupiah-input text-end tabular-nums" id="inputNominalCL" data-field-name="Nominal Transfer" placeholder="0,00" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Keterangan -->
                <div class="col-12 col-lg-6">
                    <div class="p-3 border rounded bg-white h-100">
                        <div class="fw-semibold text-secondary small text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 11px;">
                            Keterangan Transfer
                        </div>

                        <div class="mb-3">
                            <label for="inputKetTrxCL" class="form-label small fw-semibold text-dark">
                                Berita / Catatan Transfer <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="inputKetTrxCL" rows="3" data-field-name="Keterangan Komisi" placeholder="Pencairan komisi periode bulan ..." required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm px-3" id="btnResetCL">
                        Reset
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Simpan Komisi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     TABEL DATA TRANSAKSI KOMISI BULANAN
     ========================================================================= -->
<div class="card-app mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
        <span class="fw-semibold text-dark" style="font-size: 13px;">Riwayat Pencatatan Komisi Bulanan</span>
        <div class="d-flex align-items-center gap-2" style="max-width: 320px; width: 100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-box" data-target-table="komisiTrxTable" placeholder="Cari kode, cabang, tanggal, keterangan...">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-app align-middle mb-0" id="komisiTrxTable">
            <thead>
                <tr>
                    <th style="width: 45px;">No</th>
                    <th>Kode Trx CL</th>
                    <th>Cabang</th>
                    <th>Tanggal Transfer</th>
                    <th class="text-end">Nominal Komisi</th>
                    <th>Keterangan Transfer</th>
                    <th class="text-center" style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($komisis as $index => $cl)
                <tr data-id="{{ $cl->kode_trx_cl }}">
                    <td class="row-number text-secondary">{{ $index + 1 }}</td>
                    <td><span class="font-monospace text-dark fw-medium">{{ $cl->kode_trx_cl }}</span></td>
                    <td><span class="text-dark">{{ $cl->nama_cabang }}</span></td>
                    <td class="tabular-nums">{{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }}</td>
                    <td class="text-end tabular-nums fw-medium text-dark">Rp {{ number_format($cl->nominal, 2, ',', '.') }}</td>
                    <td><span class="text-truncate d-inline-block text-secondary" style="max-width: 300px;">{{ $cl->ket_trx_cl }}</span></td>
                    <td class="text-center">
                        <div class="d-inline-flex gap-1 align-items-center">
                            @can('update', $cl)
                                <button type="button" class="btn btn-action-icon btn-outline-secondary btn-edit-cl"
                                        data-id="{{ $cl->kode_trx_cl }}"
                                        data-id-cab="{{ $cl->id_cab }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }}"
                                        data-nominal="{{ number_format($cl->nominal, 2, ',', '.') }}"
                                        data-ket="{{ $cl->ket_trx_cl }}"
                                        data-update-url="{{ route('transaksi.komisi.update', $cl->kode_trx_cl) }}"
                                        title="Ubah Transaksi Komisi"
                                        aria-label="Ubah Transaksi Komisi">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            @endcan

                            @can('delete', $cl)
                                <button type="button" class="btn btn-action-icon btn-outline-danger btn-delete-row"
                                        data-id="{{ $cl->kode_trx_cl }}"
                                        data-delete-url="{{ route('transaksi.komisi.destroy', $cl->kode_trx_cl) }}"
                                        data-item-name="{{ $cl->kode_trx_cl }} ({{ \Carbon\Carbon::parse($cl->tanggal_tf)->format('d-m-Y') }})"
                                        title="Hapus Transaksi Komisi"
                                        aria-label="Hapus Transaksi Komisi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endcan

                            @cannot('update', $cl)
                                @cannot('delete', $cl)
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
        <span class="text-secondary small">Total transaksi: <strong id="komisiCountBadge" class="text-dark">{{ count($komisis) }}</strong></span>
    </div>
</div>

<!-- =========================================================================
     MODAL EDIT KOMISI BULANAN
     ========================================================================= -->
<div class="modal fade" id="modalEditCL" tabindex="-1" aria-labelledby="modalEditCLLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fs-6 fw-semibold text-dark" id="modalEditCLLabel">
                    Ubah Pencairan Komisi Bulanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEditCL" novalidate>
                <div class="modal-body p-4">
                    <input type="hidden" id="editCLUpdateUrl" value="">
                    
                    <!-- Kode Trx CL -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Kode Trx CL</label>
                        <input type="text" class="form-control bg-light font-monospace" id="editKodeTrxCL" readonly>
                    </div>

                    <!-- Cabang -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Cabang <span class="text-danger">*</span></label>
                        <select class="form-select" id="editCLIdCab" required>
                            @foreach($cabangs as $cab)
                                <option value="{{ $cab->id_cab }}">{{ $cab->id_cab }} - {{ $cab->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tanggal Transfer -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Tanggal Transfer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control datepicker" id="editCLTanggal" placeholder="dd-mm-yyyy" required>
                    </div>

                    <!-- Nominal Komisi -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Nominal Komisi (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary small">Rp</span>
                            <input type="text" class="form-control rupiah-input text-end tabular-nums" id="editCLNominal" required>
                        </div>
                    </div>

                    <!-- Keterangan Transfer -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-dark">Keterangan / Berita Transfer <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCLKet" required>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" id="btnUpdateCL">
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

                document.getElementById('modalEditCLLabel').textContent = 'Ubah Komisi (' + id + ')';
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
                    btnUpdateCL.textContent = 'Simpan Perubahan';
                    if (status >= 200 && status < 300 && body.success) {
                        modalEditCL.hide();
                        showToastAlert(body.message, 'success');
                        setTimeout(() => window.location.reload(), 400);
                    } else {
                        const errMsg = body.message || (body.errors ? Object.values(body.errors).flat().join('<br>') : 'Gagal memperbarui transaksi komisi.');
                        showToastAlert(errMsg, 'danger');
                    }
                })
                .catch(err => {
                    btnUpdateCL.disabled = false;
                    btnUpdateCL.textContent = 'Simpan Perubahan';
                    showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
                });
            });
        }

        // Reset listener
        document.getElementById('btnResetCL').addEventListener('click', function () {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        });

        // Submit Form ke Backend
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const isValid = validateRequiredFields(form);
            if (!isValid) return;

            const kodeTrx = document.getElementById('inputKodeTrxCL').value;
            const idCab = document.getElementById('inputCabangCL').value;
            const tanggal = document.getElementById('inputTanggalTf').value;
            const nominal = document.getElementById('inputNominalCL').value;
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
                    nominal: nominal,
                    ket_trx_cl: keterangan
                })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan Komisi';
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
                submitBtn.textContent = 'Simpan Komisi';
                showToastAlert('Terjadi kesalahan koneksi atau server.', 'danger');
            });
        });
    });
</script>
@endpush
