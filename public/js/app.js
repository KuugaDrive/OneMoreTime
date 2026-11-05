/**
 * App JavaScript - Sistem Komisi & Operasional Packing List JNE
 * Vanilla JavaScript Helpers: Rupiah, Role Simulation, Flatpickr, Validation, Table Simulation
 */

// =========================================================================
// 1. RUPIAH HELPER FUNCTIONS (FORMAT & PARSE)
// =========================================================================

/**
 * Format angka ke format Rupiah standar Indonesia: Rp 1.500.000,00
 * @param {number|string} number
 * @returns {string}
 */
function formatRupiah(number) {
    if (number === null || number === undefined || number === '' || isNaN(number)) {
        return 'Rp 0,00';
    }
    const num = parseFloat(number);
    const parts = num.toFixed(2).split('.');
    const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const decimalPart = parts[1];
    return `Rp ${integerPart},${decimalPart}`;
}

/**
 * Format angka tanpa prefix "Rp ": 1.500.000,00
 * Digunakan untuk nilai di input box saat diformat
 * @param {number|string} number
 * @returns {string}
 */
function formatRupiahNumberOnly(number) {
    if (number === null || number === undefined || number === '' || isNaN(number)) {
        return '0,00';
    }
    const num = parseFloat(number);
    const parts = num.toFixed(2).split('.');
    const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const decimalPart = parts[1];
    return `${integerPart},${decimalPart}`;
}

/**
 * Parser cerdas untuk angka dua format: English (1,500.50) & Indonesia (1.500,50).
 * Mengimplementasikan aturan yang sama persis dengan App\Support\NumberParser di backend:
 * - Titik dan koma: karakter paling kanan adalah pemisah desimal
 * - Satu separator: 3 digit -> ribuan; 1-2 digit -> desimal
 * - Abaikan 'Rp', 'RP', 'rp', spasi, dsb.
 * @param {string|number} str
 * @returns {number|null} Mengembalikan float atau null jika invalid
 */
function parseSmartNumber(str) {
    if (str === null || str === undefined) return null;
    if (typeof str === 'number') return isNaN(str) ? null : str;

    let s = str.toString().trim();
    if (!s) return null;

    // Bersihkan prefix Rp/rp dan spasi di sekitarnya
    s = s.replace(/^rp\s*/i, '').trim();

    // Hapus karakter selain digit, koma, titik, minus
    s = s.replace(/[^\d,\.-]/g, '');
    if (!s || !/\d/.test(s)) return null;

    // Cek minus
    const isNegative = s.startsWith('-');
    if (isNegative) s = s.slice(1);
    if (s.includes('-')) return null;

    const hasComma = s.includes(',');
    const hasDot = s.includes('.');

    let floatVal = null;

    if (hasComma && hasDot) {
        const lastComma = s.lastIndexOf(',');
        const lastDot = s.lastIndexOf('.');
        if (lastDot > lastComma) {
            // English: koma adalah ribuan, titik desimal
            const clean = s.replace(/,/g, '');
            if ((clean.match(/\./g) || []).length !== 1) return null;
            floatVal = parseFloat(clean);
        } else {
            // Indonesia: titik adalah ribuan, koma desimal
            const clean = s.replace(/\./g, '').replace(',', '.');
            if ((clean.match(/\./g) || []).length !== 1) return null;
            floatVal = parseFloat(clean);
        }
    } else if (hasComma) {
        const commaCount = (s.match(/,/g) || []).length;
        if (commaCount > 1) {
            // Multiple koma: pemisah ribuan English (contoh: 1,000,000)
            const parts = s.split(',');
            let valid = true;
            for (let i = 1; i < parts.length; i++) {
                if (parts[i].length !== 3) { valid = false; break; }
            }
            if (!valid) return null;
            floatVal = parseFloat(s.replace(/,/g, ''));
        } else {
            const parts = s.split(',');
            const after = parts[1];
            if (parts[0].length >= 1 && parts[0].length <= 3 && after.length === 3) {
                // Ribuan (contoh: 436,000)
                floatVal = parseFloat(parts[0] + parts[1]);
            } else {
                // Desimal (contoh: 436,5 atau 4,5 atau 436,50)
                floatVal = parseFloat(parts[0] + '.' + after);
            }
        }
    } else if (hasDot) {
        const dotCount = (s.match(/\./g) || []).length;
        if (dotCount > 1) {
            // Multiple titik: pemisah ribuan Indonesia (contoh: 1.000.000)
            const parts = s.split('.');
            let valid = true;
            for (let i = 1; i < parts.length; i++) {
                if (parts[i].length !== 3) { valid = false; break; }
            }
            if (!valid) return null;
            floatVal = parseFloat(s.replace(/\./g, ''));
        } else {
            const parts = s.split('.');
            const after = parts[1];
            if (parts[0].length >= 1 && parts[0].length <= 3 && after.length === 3) {
                // Ribuan (contoh: 436.000)
                floatVal = parseFloat(parts[0] + parts[1]);
            } else {
                // Desimal (contoh: 436.5 atau 1500.50)
                floatVal = parseFloat(parts[0] + '.' + after);
            }
        }
    } else {
        // Bilangan bulat murni
        floatVal = parseFloat(s);
    }

    if (isNaN(floatVal)) return null;
    return isNegative ? -floatVal : floatVal;
}

/**
 * Parse string format Rupiah (contoh: "1.500.000,00" atau "Rp 1.500.000,00") menjadi angka float murni
 * @param {string} str
 * @returns {number}
 */
function parseRupiah(str) {
    if (str === null || str === undefined || str === '') return 0;
    if (typeof str === 'number') return isNaN(str) ? 0 : str;

    const parsed = parseSmartNumber(str);
    return (parsed !== null && !isNaN(parsed)) ? parsed : 0;
}

/**
 * Helper untuk memformat string nilai input rupiah secara real-time
 * Contoh:
 *   "4" -> "4,00"
 *   "40" -> "40,00"
 *   "4000" -> "4.000,00"
 *   "4000000" -> "4.000.000,00"
 *   "4000,5" -> "4.000,50"
 */
function formatRupiahLive(inputVal, cursorPos) {
    if (!inputVal || !/\d/.test(inputVal)) {
        return { val: '', pos: 0 };
    }

    const commaIdx = inputVal.indexOf(',');
    const inDecimal = commaIdx !== -1 && cursorPos > commaIdx;

    const parts = inputVal.split(',');
    let intRaw = parts[0].replace(/\D/g, '');

    // Hapus leading zero kecuali nol tunggal
    intRaw = intRaw.replace(/^0+(?=\d)/, '');
    if (!intRaw && inDecimal) intRaw = '0';
    if (!intRaw) return { val: '', pos: 0 };

    const intFormatted = intRaw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Desimal: jika ada bagian setelah koma, gunakan, jika tidak default '00'
    let decRaw = parts.length > 1 ? parts.slice(1).join('').replace(/\D/g, '').slice(0, 2) : '00';
    while (decRaw.length < 2) decRaw += '0';

    const formatted = intFormatted + ',' + decRaw;

    let newCursor = formatted.length;
    if (!inDecimal) {
        // Hitung berapa digit di sebelah kiri kursor sebelum pemformatan
        const digitsBeforeCursor = (inputVal.slice(0, cursorPos).split(',')[0] || '').replace(/\D/g, '').length;
        if (digitsBeforeCursor === 0) {
            newCursor = 0;
        } else {
            let count = 0;
            newCursor = intFormatted.length; // fallback ke sebelum koma
            for (let i = 0; i < intFormatted.length; i++) {
                if (/\d/.test(intFormatted[i])) count++;
                if (count === digitsBeforeCursor) {
                    newCursor = i + 1;
                    break;
                }
            }
        }
    } else {
        const decDigitsBefore = inputVal.slice(commaIdx + 1, cursorPos).replace(/\D/g, '').length;
        newCursor = intFormatted.length + 1 + Math.min(2, decDigitsBefore);
    }

    return { val: formatted, pos: newCursor };
}

/**
 * Setup input otomatis untuk input bermask/rupiah
 * Mencegah huruf / alfabet masuk ke dalam kolom harga serta memformat ribuan & ,00 secara otomatis
 */
function setupRupiahInputs() {
    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    rupiahInputs.forEach(input => {
        // Pastikan inputmode bernilai numeric untuk mempermudah keyboard numerik di mobile
        input.setAttribute('inputmode', 'numeric');

        // Blokir pengetikan alfabet dan tombol koma/titik untuk lompat ke desimal
        input.addEventListener('keydown', function (e) {
            // Izinkan tombol kontrol dan navigasi
            if ([8, 9, 13, 27, 46, 37, 38, 39, 40, 35, 36].includes(e.keyCode || e.which)) {
                // Backspace khusus saat posisi tepat setelah koma
                if ((e.keyCode === 8 || e.key === 'Backspace') && this.selectionStart === this.selectionEnd) {
                    const commaIdx = this.value.indexOf(',');
                    if (commaIdx !== -1 && this.selectionStart === commaIdx + 1) {
                        e.preventDefault();
                        this.setSelectionRange(commaIdx, commaIdx);
                    }
                }
                return;
            }

            // Izinkan kombinasi shortcut (Ctrl/Cmd + A, C, V, X, Z)
            if (e.ctrlKey || e.metaKey) {
                return;
            }

            // Jika menekan koma (,) atau titik (.), langsung lompat ke belakang koma untuk mengedit desimal
            if (e.key === ',' || e.key === '.') {
                e.preventDefault();
                const commaIdx = this.value.indexOf(',');
                if (commaIdx !== -1) {
                    this.setSelectionRange(commaIdx + 1, commaIdx + 1);
                } else {
                    this.value = '0,00';
                    this.setSelectionRange(2, 2);
                }
                return;
            }

            // Blokir jika bukan angka 0-9
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        });

        // Dukungan beforeinput untuk browser modern
        input.addEventListener('beforeinput', function (e) {
            if (e.data && !/^[0-9,\.]*$/.test(e.data)) {
                e.preventDefault();
            }
        });

        // Event input untuk format real-time otomatis (live formatting)
        input.addEventListener('input', function () {
            // Bersihkan karakter non-angka dan non-separator
            let rawVal = this.value.replace(/[^0-9,\.]/g, '');
            if (!rawVal || !/\d/.test(rawVal)) {
                this.value = '';
                return;
            }

            const currentPos = this.selectionStart || rawVal.length;
            const res = formatRupiahLive(rawVal, currentPos);
            this.value = res.val;
            this.setSelectionRange(res.pos, res.pos);
        });

        // Tangani paste: parsing format English & Indonesia, format ulang ke format Indonesia
        input.addEventListener('paste', function (e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (clipboardData) {
                const text = clipboardData.getData('text');
                const parsed = parseSmartNumber(text);
                if (parsed !== null) {
                    e.preventDefault();
                    this.value = formatRupiahNumberOnly(parsed);
                    this.dispatchEvent(new Event('input', { bubbles: true }));
                    this.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });

        // Saat fokus: bersihkan jika hanya bernilai 0
        input.addEventListener('focus', function () {
            const raw = parseRupiah(this.value);
            if (raw === 0 && (this.value === '0,00' || this.value === '0' || this.value === '')) {
                this.value = '';
            }
        });

        // Format saat blur
        input.addEventListener('blur', function () {
            const val = this.value.trim();
            if (/\d/.test(val)) {
                const raw = parseRupiah(val);
                this.value = formatRupiahNumberOnly(raw);
            } else {
                this.value = '';
            }
        });
    });
}

/**
 * Setup input untuk field yang murni angka (No HP, Jumlah Paket, dsb.)
 * Mencegah alfabet dan simbol masuk ke dalam field
 */
function setupNumericInputs() {
    const numericInputs = document.querySelectorAll('.numeric-only, .numeric-int-only, #inputJumlahPaket, #inputNoHp, #inputNoHpCabang');
    numericInputs.forEach(input => {
        input.setAttribute('inputmode', 'numeric');

        // Blokir pengetikan alfabet dan karakter non-digit
        input.addEventListener('keydown', function (e) {
            // Izinkan tombol kontrol dan navigasi
            if ([8, 9, 13, 27, 46, 37, 38, 39, 40, 35, 36].includes(e.keyCode || e.which)) {
                return;
            }
            // Izinkan kombinasi shortcut (Ctrl/Cmd + A, C, V, X, Z)
            if (e.ctrlKey || e.metaKey) {
                return;
            }
            // Hanya izinkan angka 0-9
            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('beforeinput', function (e) {
            if (e.data && !/^[0-9]*$/.test(e.data)) {
                e.preventDefault();
            }
        });

        input.addEventListener('paste', function (e) {
            const clipboardData = e.clipboardData || window.clipboardData;
            if (clipboardData) {
                const text = clipboardData.getData('text');
                if (/[^0-9]/.test(text)) {
                    e.preventDefault();
                    const cleanText = text.replace(/[^0-9]/g, '');
                    if (cleanText) {
                        const start = this.selectionStart || 0;
                        const end = this.selectionEnd || 0;
                        const cur = this.value;
                        this.value = cur.substring(0, start) + cleanText + cur.substring(end);
                        this.selectionStart = this.selectionEnd = start + cleanText.length;
                        this.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            }
        });

        input.addEventListener('input', function () {
            const clean = this.value.replace(/[^0-9]/g, '');
            if (this.value !== clean) {
                this.value = clean;
            }
        });
    });
}

// =========================================================================
// 2. SIMULASI ROLE (DIHAPUS - DIGANTIKAN OLEH SERVER-SIDE RBAC)
// =========================================================================

// =========================================================================
// 3. FLATPICKR INITIALIZATION (LOCALE INDONESIA, FORMAT dd-mm-yyyy)
// =========================================================================
function initDatePickers() {
    if (typeof flatpickr === 'undefined') return;

    // Pastikan locale Indonesia terdaftar
    if (flatpickr.l10ns && flatpickr.l10ns.id) {
        flatpickr.localize(flatpickr.l10ns.id);
    }

    const dateInputs = document.querySelectorAll('.datepicker');
    dateInputs.forEach(input => {
        if (input._flatpickr) return; // hindari double init

        const defaultDate = input.getAttribute('data-default-date') || input.value || 'today';
        flatpickr(input, {
            dateFormat: 'd-m-Y',
            allowInput: true,
            defaultDate: input.value ? input.value : (input.hasAttribute('data-no-default') ? null : defaultDate),
            locale: flatpickr.l10ns ? flatpickr.l10ns.id : 'id'
        });
    });
}

// =========================================================================
// 4. CLIENT-SIDE VALIDATION & UNIQUE CHECK
// =========================================================================

/**
 * Validasi form wajib isi serta validasi format angka dan rupiah
 * @param {HTMLFormElement} form
 * @returns {boolean} isValid
 */
function validateRequiredFields(form) {
    let isValid = true;
    const requiredInputs = form.querySelectorAll('[required]');

    requiredInputs.forEach(input => {
        // Hapus feedback lama
        input.classList.remove('is-invalid');
        const existingFeedback = input.parentElement.querySelector('.invalid-feedback.dynamic-required');
        if (existingFeedback) existingFeedback.remove();

        const val = input.value.trim();
        const fieldName = input.getAttribute('data-field-name') || 'Field ini';

        if (!val || (input.tagName === 'SELECT' && val === '')) {
            isValid = false;
            markFieldInvalid(input, `${fieldName} wajib diisi.`);
            return;
        }

        // Validasi input Rupiah: harus memiliki digit angka
        if (input.classList.contains('rupiah-input')) {
            if (!/\d/.test(val)) {
                isValid = false;
                markFieldInvalid(input, `${fieldName} harus berupa angka / nominal yang valid.`);
                return;
            }
        }

        // Validasi input nomor telepon / no hp: hanya angka 8-15 digit
        if (input.classList.contains('numeric-only') || input.id === 'inputNoHp' || input.id === 'inputNoHpCabang') {
            if (!/^[0-9]+$/.test(val)) {
                isValid = false;
                markFieldInvalid(input, `${fieldName} hanya boleh diisi dengan angka.`);
                return;
            }
            if (val.length < 8 || val.length > 15) {
                isValid = false;
                markFieldInvalid(input, `${fieldName} harus berisi antara 8 sampai 15 digit angka.`);
                return;
            }
        }

        // Validasi Jumlah Paket: bilangan bulat minimal 1
        if (input.id === 'inputJumlahPaket' || input.classList.contains('numeric-int-only')) {
            const intVal = parseInt(val, 10);
            if (isNaN(intVal) || intVal < 1) {
                isValid = false;
                markFieldInvalid(input, `${fieldName} harus berupa bilangan bulat minimal 1.`);
                return;
            }
        }
    });

    return isValid;
}

/**
 * Helper menampilkan pesan invalid pada input
 */
function markFieldInvalid(input, message) {
    input.classList.add('is-invalid');
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback dynamic-required';
    feedback.textContent = message;
    input.parentElement.appendChild(feedback);
}

/**
 * Validasi unik terhadap data yang sudah ada di tabel
 * @param {HTMLTableElement} table
 * @param {number} columnIndex - indeks kolom (0-based)
 * @param {string} valueToCheck
 * @param {string|null} excludeId - ID baris yang sedang diedit (jika mode edit)
 * @param {HTMLInputElement} inputElement
 * @param {string} fieldLabel
 * @returns {boolean} isUnique
 */
function checkTableUnique(table, columnIndex, valueToCheck, excludeId, inputElement, fieldLabel) {
    if (!table) return true;

    // Bersihkan error unik sebelumnya
    const oldFeedback = inputElement.parentElement.querySelector('.invalid-feedback.dynamic-unique');
    if (oldFeedback) oldFeedback.remove();

    const normalizedNew = valueToCheck.trim().toLowerCase();
    const rows = table.querySelectorAll('tbody tr');
    let isDuplicate = false;

    rows.forEach(row => {
        const rowId = row.getAttribute('data-id');
        if (excludeId && rowId === excludeId) {
            return; // Lewati baris yang sedang diedit
        }

        const cell = row.children[columnIndex];
        if (cell) {
            const cellText = cell.textContent.trim().toLowerCase();
            if (cellText === normalizedNew) {
                isDuplicate = true;
            }
        }
    });

    if (isDuplicate) {
        inputElement.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback dynamic-unique';
        feedback.textContent = `${fieldLabel} "${valueToCheck}" sudah digunakan.`;
        inputElement.parentElement.appendChild(feedback);
        return false;
    }

    return true;
}

// =========================================================================
// 5. MODAL HAPUS DATA (KONFIRMASI SEBELUM HAPUS KE BACKEND)
// =========================================================================
let itemToDeleteRow = null;
let itemDeleteUrl = null;

function setupDeleteConfirmation() {
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    if (!deleteModalEl) return;

    const confirmBtn = document.getElementById('btnConfirmDelete');
    const deleteModal = new bootstrap.Modal(deleteModalEl);

    // Event delegation untuk tombol hapus di semua tabel
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-row');
        if (btn) {
            e.preventDefault();
            itemToDeleteRow = btn.closest('tr');
            itemDeleteUrl = btn.getAttribute('data-delete-url') || null;
            const itemName = btn.getAttribute('data-item-name') || 'data ini';
            const itemLabel = document.getElementById('deleteConfirmItemName');
            if (itemLabel) {
                itemLabel.textContent = itemName;
            }
            deleteModal.show();
        }
    });

    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (itemDeleteUrl) {
                confirmBtn.disabled = true;
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                fetch(itemDeleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(({ status, body }) => {
                    confirmBtn.disabled = false;
                    deleteModal.hide();
                    if (status >= 200 && status < 300 && body.success) {
                        if (itemToDeleteRow) {
                            itemToDeleteRow.style.transition = 'all 0.3s ease';
                            itemToDeleteRow.style.backgroundColor = '#fee2e2';
                            setTimeout(() => {
                                itemToDeleteRow.remove();
                                renumberTableRows();
                                showToastAlert(body.message || 'Data berhasil dihapus dari database.', 'success');
                            }, 200);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        showToastAlert(body.message || 'Gagal menghapus data dari database.', 'danger');
                    }
                })
                .catch(err => {
                    confirmBtn.disabled = false;
                    deleteModal.hide();
                    showToastAlert('Terjadi kesalahan jaringan atau server saat menghapus data.', 'danger');
                });
            } else if (itemToDeleteRow) {
                // Efek animasi fade-out baris (simulasi client)
                itemToDeleteRow.style.transition = 'all 0.3s ease';
                itemToDeleteRow.style.backgroundColor = '#fee2e2';
                setTimeout(() => {
                    itemToDeleteRow.remove();
                    renumberTableRows();
                    showToastAlert('Data berhasil dihapus dari tabel.', 'success');
                }, 200);
                deleteModal.hide();
            }
        });
    }
}

/**
 * Nomor urut ulang kolom No pada tabel
 */
function renumberTableRows() {
    document.querySelectorAll('.table-app').forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        let index = 1;
        rows.forEach(row => {
            const noCell = row.querySelector('.row-number');
            if (noCell) {
                noCell.textContent = index++;
            }
        });
    });
}

// =========================================================================
// 6. TOAST NOTIFIKASI
// =========================================================================
function showToastAlert(message, type = 'success') {
    let container = document.getElementById('toastNotificationContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastNotificationContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1090';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 shadow`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

// =========================================================================
// 7. PENCARIAN & PAGINATION TABEL SEDERHANA
// =========================================================================
function setupTableSearchAndPagination() {
    document.querySelectorAll('.table-search-box').forEach(searchInput => {
        const targetTableId = searchInput.getAttribute('data-target-table');
        const table = document.getElementById(targetTableId);
        if (!table) return;

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Handle baris kosong
            let emptyMsgRow = table.querySelector('.empty-search-row');
            if (visibleCount === 0) {
                if (!emptyMsgRow) {
                    emptyMsgRow = document.createElement('tr');
                    emptyMsgRow.className = 'empty-search-row text-center text-muted';
                    const colCount = table.querySelectorAll('thead th').length || 6;
                    emptyMsgRow.innerHTML = `<td colspan="${colCount}" class="py-4"><i class="bi bi-search me-2"></i>Data "${query}" tidak ditemukan.</td>`;
                    table.querySelector('tbody').appendChild(emptyMsgRow);
                }
            } else if (emptyMsgRow) {
                emptyMsgRow.remove();
            }
        });
    });
}

// =========================================================================
// 8. SIDEBAR TOGGLE UNTUK MOBILE
// =========================================================================
function setupSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.querySelector('.app-sidebar');
    let backdrop = document.querySelector('.sidebar-backdrop');

    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', function () {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }
}

// =========================================================================
// 9. KALKULASI OTOMATIS TRANSAKSI PACKING LIST
//    Grand Total = Total - Discount Charge - e-Voucher
// =========================================================================
function setupPackingListCalculator() {
    const inputTotal = document.getElementById('inputTotalPL');
    const inputDiscount = document.getElementById('inputDiscountChargePL');
    const inputEVoucher = document.getElementById('inputEVoucherPL');
    const inputGrandTotal = document.getElementById('inputGrandTotalPL');

    if (!inputTotal || !inputDiscount || !inputGrandTotal) return;

    function calculateGrandTotal() {
        const total = parseRupiah(inputTotal.value);
        const discount = parseRupiah(inputDiscount.value);
        const eVoucher = inputEVoucher ? parseRupiah(inputEVoucher.value) : 0;
        const grandTotal = Math.max(0, total - discount - eVoucher);
        inputGrandTotal.value = formatRupiahNumberOnly(grandTotal);
    }

    inputTotal.addEventListener('input', calculateGrandTotal);
    inputTotal.addEventListener('blur', calculateGrandTotal);
    inputDiscount.addEventListener('input', calculateGrandTotal);
    inputDiscount.addEventListener('blur', calculateGrandTotal);
    if (inputEVoucher) {
        inputEVoucher.addEventListener('input', calculateGrandTotal);
        inputEVoucher.addEventListener('blur', calculateGrandTotal);
    }
}

// =========================================================================
// DOM READY INITIALIZATION
// =========================================================================
document.addEventListener('DOMContentLoaded', function () {
    // 1. Setup Rupiah & Numeric formatting
    setupRupiahInputs();
    setupNumericInputs();

    // 2. Flatpickr
    initDatePickers();

    // 3. Modal Hapus Data
    setupDeleteConfirmation();

    // 4. Search & Pagination Tabel
    setupTableSearchAndPagination();

    // 5. Sidebar Mobile Toggle
    setupSidebarToggle();

    // 6. Packing List Calculator
    setupPackingListCalculator();
});
