<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Status;
use App\Models\Cabang;
use App\Models\BebanOps;
use App\Models\PackingList;
use App\Models\Operasional;
use App\Models\Komisi;
use Carbon\Carbon;

class JneAppTest extends TestCase
{
    protected User $owner;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->owner = User::where('role', 'owner')->first();
        $this->admin = User::where('role', 'admin')->first();
    }

    public function test_root_redirects_to_login_for_guest(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('AGEN RESMI JNE');
    }

    // =========================================================================
    // BAGIAN B: HAK AKSES ADMIN & OWNER
    // =========================================================================
    public function test_owner_can_login_with_valid_credentials_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/login', [
            'username' => 'owner_utama',
            'password' => 'password',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->owner);
    }

    public function test_admin_can_login_and_redirects_to_packing_list(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin_operasional',
            'password' => 'password',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/transaksi/packing-list');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_root_redirects_according_to_role(): void
    {
        $ownerResp = $this->actingAs($this->owner)->get('/');
        $ownerResp->assertRedirect('/dashboard');

        $adminResp = $this->actingAs($this->admin)->get('/');
        $adminResp->assertRedirect('/transaksi/packing-list');
    }

    public function test_admin_gets_403_on_restricted_routes(): void
    {
        $restrictedUrls = [
            '/dashboard',
            '/master/user',
            '/master/cabang',
            '/master/ops',
            '/master/status',
            '/transaksi/operasional',
            '/transaksi/komisi',
            '/laporan/packing-list',
            '/laporan/operasional',
            '/laporan/laba-bersih',
            '/laporan/komisi',
        ];

        foreach ($restrictedUrls as $url) {
            $response = $this->actingAs($this->admin)->get($url);
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_access_packing_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/transaksi/packing-list');
        $response->assertStatus(200);
    }

    public function test_owner_can_access_all_pages(): void
    {
        $pages = [
            '/dashboard',
            '/master/user',
            '/master/cabang',
            '/master/ops',
            '/master/status',
            '/transaksi/packing-list',
            '/transaksi/operasional',
            '/transaksi/komisi',
            '/laporan/packing-list',
            '/laporan/operasional',
            '/laporan/laba-bersih',
            '/laporan/komisi',
        ];

        foreach ($pages as $url) {
            $response = $this->actingAs($this->owner)->get($url);
            $response->assertStatus(200);
        }
    }

    // =========================================================================
    // BAGIAN C: ATURAN STATUS AKUN & OWNER
    // =========================================================================
    public function test_login_rejected_if_user_status_is_not_level_1(): void
    {
        $cutiUser = User::where('username', 'user_cuti_test')->first();
        if (!$cutiUser) {
            $cutiUser = User::create([
                'id_user' => 'USR-998',
                'username' => 'user_cuti_test',
                'no_hp' => '081234567899',
                'alamat' => 'Jl. Cuti No. 1',
                'status_id' => 'ST-002', // Level 2: Cuti
                'role' => 'admin',
                'id_cab' => 'CB-001',
                'password' => bcrypt('password'),
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'username' => 'user_cuti_test',
            'password' => 'password',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_cannot_delete_status_level_1(): void
    {
        $response = $this->actingAs($this->owner)->deleteJson('/master/status/ST-001');
        $response->assertStatus(422);
        $this->assertNotNull(Status::find('ST-001'));
    }

    public function test_cannot_change_level_of_status_level_1(): void
    {
        $response = $this->actingAs($this->owner)->putJson('/master/status/ST-001', [
            'nama_status' => 'Aktif',
            'level' => 2,
            'ket_status' => 'Ubah level',
        ]);
        $response->assertStatus(422);
        $this->assertEquals(1, Status::find('ST-001')->level);
    }

    public function test_cannot_delete_status_in_use_by_user(): void
    {
        // ST-002 dipakai oleh user_cuti_test
        $response = $this->actingAs($this->owner)->deleteJson('/master/status/ST-002');
        $response->assertStatus(422);
        $this->assertNotNull(Status::find('ST-002'));
    }

    public function test_owner_cannot_demote_or_deactivate_self(): void
    {
        // 1. Owner tidak boleh mengubah status diri ke non-Level 1
        $response = $this->actingAs($this->owner)->putJson("/master/user/{$this->owner->id_user}", [
            'nama_user' => $this->owner->username,
            'no_hp' => $this->owner->no_hp,
            'alamat' => $this->owner->alamat,
            'status' => 'ST-002', // Cuti
            'role' => 'owner',
        ]);
        $response->assertStatus(422);

        // 2. Owner tidak boleh mengubah role diri ke non-owner
        $response = $this->actingAs($this->owner)->putJson("/master/user/{$this->owner->id_user}", [
            'nama_user' => $this->owner->username,
            'no_hp' => $this->owner->no_hp,
            'alamat' => $this->owner->alamat,
            'status' => 'ST-001',
            'role' => 'admin',
        ]);
        $response->assertStatus(422);

        // 3. Owner tidak boleh menghapus diri sendiri
        $response = $this->actingAs($this->owner)->deleteJson("/master/user/{$this->owner->id_user}");
        $response->assertStatus(422);
        $this->assertNotNull(User::find($this->owner->id_user));
    }

    // =========================================================================
    // BAGIAN A & MASTER CABANG RESTRICTIONS
    // =========================================================================
    public function test_cannot_delete_cabang_used_in_transactions(): void
    {
        $response = $this->actingAs($this->owner)->deleteJson('/master/cabang/CB-001');
        $response->assertStatus(422);
        $this->assertNotNull(Cabang::find('CB-001'));
    }

    public function test_crud_master_cabang(): void
    {
        // 1. Store
        $response = $this->actingAs($this->owner)->postJson('/master/cabang', [
            'nama_cabang' => 'Cabang Test Baru Unique',
            'alamat' => 'Jl. Automated No. 1',
            'nama_pic' => 'PIC Automated',
            'no_hp' => '081234567890',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $newCab = Cabang::where('nama_cabang', 'Cabang Test Baru Unique')->first();
        $this->assertNotNull($newCab);

        // 2. Update
        $response = $this->actingAs($this->owner)->putJson("/master/cabang/{$newCab->id_cab}", [
            'nama_cabang' => 'Cabang Test Baru Updated',
            'alamat' => 'Jl. Automated No. 1 Updated',
            'nama_pic' => 'PIC Automated 2',
            'no_hp' => '081234567891',
        ]);
        $response->assertStatus(200);

        // 3. Delete
        $response = $this->actingAs($this->owner)->deleteJson("/master/cabang/{$newCab->id_cab}");
        $response->assertStatus(200);
        $this->assertNull(Cabang::find($newCab->id_cab));
    }

    // =========================================================================
    // BAGIAN D: VALIDASI UNIQUE & PERHITUNGAN GRAND TOTAL
    // =========================================================================
    public function test_unique_validations(): void
    {
        // 1. Unique Username
        $response = $this->actingAs($this->owner)->postJson('/master/user', [
            'nama_user' => 'owner_utama',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Raya',
            'status' => 'ST-001',
            'role' => 'admin',
            'password' => 'secret123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama_user']);

        // 2. Unique Nama Cabang
        $existingCabang = Cabang::first();
        $response = $this->actingAs($this->owner)->postJson('/master/cabang', [
            'nama_cabang' => $existingCabang->nama_cabang,
            'alamat' => 'Jl. Raya',
            'nama_pic' => 'PIC',
            'no_hp' => '081234567890',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama_cabang']);

        // 3. Unique Beban Ops
        $existingOps = BebanOps::first();
        $response = $this->actingAs($this->owner)->postJson('/master/ops', [
            'beban_ops' => $existingOps->beban_ops,
            'ket_ops' => 'Biaya bulanan',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['beban_ops']);

        // 4. Unique Nama Status
        $existingStatus = Status::first();
        $response = $this->actingAs($this->owner)->postJson('/master/status', [
            'nama_status' => $existingStatus->nama_status,
            'level' => 1,
            'ket_status' => 'Aktif',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nama_status']);
    }

    public function test_crud_transaksi_packing_list_recalculates_grand_total_backend(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'no_pl_jne' => 'PL/TEST/RECALC',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '20-05-2025',
            'total' => '2.500.000,00',
            'discount_charge' => '250.000,00',
            'jumlah_paket' => 35,
            'ket_trx_pl' => 'Testing Trx PL Automated Recalc',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $pl = PackingList::where('ket_trx_pl', 'Testing Trx PL Automated Recalc')->first();
        $this->assertNotNull($pl);
        $this->assertEquals('CB-001', $pl->id_cab);
        $this->assertEquals('PL/TEST/RECALC', $pl->no_pl_jne);
        // Backend recalculates 2,500,000 - 250,000 = 2,250,000
        $this->assertEquals(2250000.00, (float) $pl->grand_total);

        // Delete
        $delResponse = $this->actingAs($this->owner)->deleteJson("/transaksi/packing-list/{$pl->kode_trx_pl}");
        $delResponse->assertStatus(200);
        $this->assertNull(PackingList::find($pl->kode_trx_pl));
    }

    public function test_discount_cannot_exceed_total(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'id_cab' => 'CB-001',
            'tanggal_pl' => '20-05-2025',
            'total' => '100.000,00',
            'discount_charge' => '150.000,00', // Greater than total
            'jumlah_paket' => 5,
            'ket_trx_pl' => 'Testing Discount > Total',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['discount_charge']);
    }

    public function test_crud_transaksi_operasional_with_selected_beban_ops(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/transaksi/operasional', [
            'id_cab' => 'CB-002',
            'tanggal_trx_ops' => '20-05-2025',
            'beban_ops' => 'Bahan Bakar & Tol Armada',
            'nominal' => '150.000,00',
            'ket_trx_ops' => 'Bensin motor kurir pick up automated test',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $ops = Operasional::where('ket_trx_ops', 'Bensin motor kurir pick up automated test')->first();
        $this->assertNotNull($ops);
        $this->assertEquals('CB-002', $ops->id_cab);
        $this->assertEquals('OP-002', $ops->beban_ops_id);
        $this->assertEquals(150000.00, (float) $ops->nominal);

        // Delete
        $delResponse = $this->actingAs($this->owner)->deleteJson("/transaksi/operasional/{$ops->kode_trx_ops}");
        $delResponse->assertStatus(200);
        $this->assertNull(Operasional::find($ops->kode_trx_ops));
    }

    // =========================================================================
    // BAGIAN G: LAPORAN & VALIDASI FILTER
    // =========================================================================
    public function test_laba_bersih_monthly_recap_includes_month_with_only_one_data(): void
    {
        PackingList::where('kode_trx_pl', 'PL-20261201-999')->orWhere('no_pl_jne', 'PL/TEST/DEC2026')->delete();

        // Buat satu transaksi PL di bulan unik yang tidak ada operasionalnya, misal Desember 2026
        PackingList::create([
            'kode_trx_pl' => 'PL-20261201-999',
            'no_pl_jne' => 'PL/TEST/DEC2026',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '2026-12-01',
            'total' => 1000000,
            'discount_charge' => 100000,
            'grand_total' => 900000,
            'jumlah_paket' => 10,
            'ket_trx_pl' => 'Testing Solo Month PL',
            'created_by' => $this->owner->id_user,
        ]);

        $response = $this->actingAs($this->owner)->get('/laporan/laba-bersih?tanggal_awal=01-12-2026&tanggal_akhir=31-12-2026');
        $response->assertStatus(200);
        $response->assertSee('Desember 2026');
        // Sesuai Bagian A: default laba_basis adalah discount_charge (100.000,00)
        $response->assertSee('100.000,00');

        PackingList::where('kode_trx_pl', 'PL-20261201-999')->delete();
    }

    public function test_backend_validates_required_fields(): void
    {
        // 1. Packing List missing required fields
        $responsePL = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', []);
        $responsePL->assertStatus(422);
        $responsePL->assertJsonValidationErrors(['id_cab', 'tanggal_pl', 'jumlah_paket', 'total', 'discount_charge', 'ket_trx_pl']);

        // 2. Operasional missing required fields
        $responseOps = $this->actingAs($this->owner)->postJson('/transaksi/operasional', []);
        $responseOps->assertStatus(422);
        $responseOps->assertJsonValidationErrors(['id_cab', 'tanggal_trx_ops', 'beban_ops', 'nominal', 'ket_trx_ops']);

        // 3. Komisi missing required fields
        $responseKomisi = $this->actingAs($this->owner)->postJson('/transaksi/komisi', []);
        $responseKomisi->assertStatus(422);
        $responseKomisi->assertJsonValidationErrors(['id_cab', 'tanggal_tf', 'nominal', 'ket_trx_cl']);

        // 4. Cabang missing required fields
        $responseCab = $this->actingAs($this->owner)->postJson('/master/cabang', []);
        $responseCab->assertStatus(422);
        $responseCab->assertJsonValidationErrors(['nama_cabang', 'alamat', 'nama_pic', 'no_hp']);

        // 5. Status missing required fields
        $responseStatus = $this->actingAs($this->owner)->postJson('/master/status', []);
        $responseStatus->assertStatus(422);
        $responseStatus->assertJsonValidationErrors(['nama_status', 'level', 'ket_status']);
    }

    public function test_middleware_active_logs_out_user_if_status_becomes_non_level_1(): void
    {
        User::where('id_user', 'USR-995')->delete();
        // Buat user aktif baru
        $tempUser = User::create([
            'id_user' => 'USR-995',
            'username' => 'user_active_test',
            'no_hp' => '081234567899',
            'alamat' => 'Jl. Test',
            'status_id' => 'ST-001', // Level 1
            'role' => 'admin',
            'id_cab' => 'CB-001',
            'password' => bcrypt('password'),
        ]);

        // Login sukses
        $response = $this->actingAs($tempUser)->get('/transaksi/packing-list');
        $response->assertStatus(200);

        // Ubah status ke ST-002 (Level 2: Cuti)
        $tempUser->update(['status_id' => 'ST-002']);

        // Request berikutnya dikeluarkan oleh middleware active
        $followUp = $this->actingAs($tempUser)->get('/transaksi/packing-list');
        $followUp->assertRedirect('/login');
        $followUp->assertSessionHasErrors(['username']);

        $tempUser->delete();
    }

    public function test_laporan_filter_with_branch(): void
    {
        // CB-001
        $cabang1 = Cabang::find('CB-001');
        $respCab1 = $this->actingAs($this->owner)->get('/laporan/packing-list?tanggal_awal=01-01-2025&tanggal_akhir=31-12-2025&id_cab=CB-001');
        $respCab1->assertStatus(200);
        $respCab1->assertSee($cabang1->nama_cabang);

        // Laba bersih filter cabang
        $respLaba = $this->actingAs($this->owner)->get('/laporan/laba-bersih?tanggal_awal=01-01-2025&tanggal_akhir=31-12-2025&id_cab=CB-001');
        $respLaba->assertStatus(200);
        $respLaba->assertSee($cabang1->nama_cabang);
    }

    public function test_owner_can_download_all_pdf_reports(): void
    {
        $pdfRoutes = [
            '/laporan/packing-list/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=all',
            '/laporan/packing-list/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=CB-001',
            '/laporan/operasional/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=all',
            '/laporan/operasional/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=CB-001',
            '/laporan/laba-bersih/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=all',
            '/laporan/laba-bersih/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=CB-001',
            '/laporan/komisi/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=all',
            '/laporan/komisi/pdf?tanggal_awal=01-01-2025&tanggal_akhir=31-03-2025&id_cab=CB-001',
        ];

        foreach ($pdfRoutes as $route) {
            $response = $this->actingAs($this->owner)->get($route);
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/pdf');
            $this->assertStringStartsWith('%PDF', $response->getContent());
        }
    }

    public function test_laba_bersih_service_three_configurations(): void
    {
        $service = new \App\Services\LabaBersihService();

        // 1. Konfigurasi default: laba_basis = discount_charge, include_komisi = false
        $resDefault = $service->calculateMonthly('2025-01-01', '2025-12-31', 'all', 'discount_charge', false);
        $this->assertEquals('discount_charge', $resDefault['config']['basis']);
        $this->assertFalse($resDefault['config']['include_komisi']);
        $expectedLabaDefault = $resDefault['totals']['total_disc_charge'] - $resDefault['totals']['total_ops'];
        $this->assertEquals($expectedLabaDefault, $resDefault['totals']['laba_bersih']);

        // 2. Konfigurasi lama: laba_basis = grand_total, include_komisi = false
        $resGrandTotal = $service->calculateMonthly('2025-01-01', '2025-12-31', 'all', 'grand_total', false);
        $this->assertEquals('grand_total', $resGrandTotal['config']['basis']);
        $this->assertFalse($resGrandTotal['config']['include_komisi']);
        $expectedLabaGt = $resGrandTotal['totals']['total_grand_total'] - $resGrandTotal['totals']['total_ops'];
        $this->assertEquals($expectedLabaGt, $resGrandTotal['totals']['laba_bersih']);

        // 3. Konfigurasi: include_komisi = true
        $resIncludeKomisi = $service->calculateMonthly('2025-01-01', '2025-12-31', 'all', 'discount_charge', true);
        $this->assertTrue($resIncludeKomisi['config']['include_komisi']);
        $expectedLabaKomisi = $resIncludeKomisi['totals']['total_disc_charge'] - $resIncludeKomisi['totals']['total_ops'] + $resIncludeKomisi['totals']['total_komisi'];
        $this->assertEquals($expectedLabaKomisi, $resIncludeKomisi['totals']['laba_bersih']);
    }

    public function test_input_pl_with_sample_jne_and_english_format(): void
    {
        PackingList::where('no_pl_jne', 'PL/17/2510004621')->delete();

        // Input PL sampel dari dokumen JNE asli dengan format English (koma ribuan, titik desimal)
        $response = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'no_pl_jne' => 'pl/17/2510004621 ', // lowercase & un-trimmed to test sanitization
            'id_cab' => 'CB-001',
            'tanggal_pl' => '17-01-2025',
            'total' => '436,000.00',
            'discount_charge' => '94,876.35',
            'e_voucher' => '0.00',
            'jumlah_paket' => 25,
            'ket_trx_pl' => 'Sample JNE Document Test',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $pl = PackingList::where('no_pl_jne', 'PL/17/2510004621')->first();
        $this->assertNotNull($pl);
        $this->assertEquals('PL/17/2510004621', $pl->no_pl_jne); // Uppercase and trimmed
        $this->assertEquals(436000.00, (float) $pl->total);
        $this->assertEquals(94876.35, (float) $pl->discount_charge);
        $this->assertEquals(0.00, (float) $pl->e_voucher);
        // Grand Total: 436,000 - 94,876.35 = 341,123.65
        $this->assertEquals(341123.65, (float) $pl->grand_total);

        // Uji dengan nilai e_voucher positif
        PackingList::where('no_pl_jne', 'PL/17/2510009999')->delete();
        $respWithVoucher = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'no_pl_jne' => 'PL/17/2510009999',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '18-01-2025',
            'total' => '500.000,00',
            'discount_charge' => '100.000,00',
            'e_voucher' => '50.000,00',
            'jumlah_paket' => 15,
            'ket_trx_pl' => 'Testing e-Voucher Deduction',
        ]);
        $respWithVoucher->assertStatus(200);

        $plVoucher = PackingList::where('no_pl_jne', 'PL/17/2510009999')->first();
        $this->assertNotNull($plVoucher);
        // Grand Total: 500,000 - 100,000 - 50,000 = 350,000
        $this->assertEquals(350000.00, (float) $plVoucher->grand_total);

        // Bersihkan
        $plVoucher->delete();
    }

    public function test_duplicate_no_pl_jne_rejected(): void
    {
        // Pastikan record PL/17/2510004621 sudah ada
        $pl = PackingList::where('no_pl_jne', 'PL/17/2510004621')->first();
        if (!$pl) {
            PackingList::create([
                'kode_trx_pl' => 'PL-TEST-DUP-01',
                'no_pl_jne' => 'PL/17/2510004621',
                'id_cab' => 'CB-001',
                'tanggal_pl' => '2025-01-17',
                'total' => 436000,
                'discount_charge' => 94876.35,
                'e_voucher' => 0,
                'grand_total' => 341123.65,
                'jumlah_paket' => 25,
                'ket_trx_pl' => 'Existing PL',
                'created_by' => $this->owner->id_user,
            ]);
        }

        // Coba input no_pl_jne yang sama persis
        $response = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'no_pl_jne' => 'PL/17/2510004621',
            'id_cab' => 'CB-002',
            'tanggal_pl' => '17-01-2025',
            'total' => '200.000,00',
            'discount_charge' => '40.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 10,
            'ket_trx_pl' => 'Duplicate Attempt',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['no_pl_jne']);
        $this->assertStringContainsString('Nomor PL JNE sudah pernah diinput', $response->json('errors.no_pl_jne.0'));
    }

    public function test_e_voucher_and_discount_cannot_exceed_total(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/transaksi/packing-list', [
            'no_pl_jne' => 'PL/TEST/EXCEED',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '20-05-2025',
            'total' => '100.000,00',
            'discount_charge' => '60.000,00',
            'e_voucher' => '50.000,00', // 60,000 + 50,000 = 110,000 > 100,000
            'jumlah_paket' => 5,
            'ket_trx_pl' => 'Testing Disc + eVoucher > Total',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['discount_charge']);
        $this->assertStringContainsString('tidak boleh lebih besar dari total', $response->json('errors.discount_charge.0'));
    }

    public function test_user_name_jne_in_cabang_uniqueness_and_nullable(): void
    {
        Cabang::where('id_cab', 'CB-TEST-1')->orWhere('id_cab', 'CB-TEST-2')->delete();

        // 1. Cabang dengan user_name_jne bernilai null diizinkan lebih dari satu
        $cb1 = Cabang::create([
            'id_cab' => 'CB-TEST-1',
            'nama_cabang' => 'Cabang Test Null 1',
            'alamat' => 'Alamat Test',
            'nama_pic' => 'PIC 1',
            'no_hp' => '081234567890',
            'user_name_jne' => null,
        ]);
        $cb2 = Cabang::create([
            'id_cab' => 'CB-TEST-2',
            'nama_cabang' => 'Cabang Test Null 2',
            'alamat' => 'Alamat Test',
            'nama_pic' => 'PIC 2',
            'no_hp' => '081234567891',
            'user_name_jne' => null,
        ]);
        $this->assertNotNull($cb1);
        $this->assertNotNull($cb2);

        // 2. Update cb1 dengan user_name_jne 'BASECLG'
        // Jika sudah ada yang pakai 'BASECLG' (misal dari Seeder), update harus ditolak jika duplikat
        $existing = Cabang::where('user_name_jne', 'BASECLG')->first();
        if ($existing) {
            $response = $this->actingAs($this->owner)->putJson("/master/cabang/{$cb1->id_cab}", [
                'nama_cabang' => 'Cabang Test Updated',
                'alamat' => 'Alamat Test',
                'nama_pic' => 'PIC 1',
                'no_hp' => '081234567890',
                'user_name_jne' => 'BASECLG',
            ]);
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['user_name_jne']);
        }

        $cb1->delete();
        $cb2->delete();
    }

    public function test_old_packing_list_with_null_no_pl_jne_can_be_updated_without_requiring_it(): void
    {
        PackingList::where('kode_trx_pl', 'PL-OLD-001')->delete();

        $oldPl = PackingList::create([
            'kode_trx_pl' => 'PL-OLD-001',
            'no_pl_jne' => null,
            'id_cab' => 'CB-001',
            'tanggal_pl' => '2025-01-01',
            'total' => 200000,
            'discount_charge' => 40000,
            'e_voucher' => 0,
            'grand_total' => 160000,
            'jumlah_paket' => 5,
            'ket_trx_pl' => 'Old PL Without No JNE',
            'created_by' => $this->owner->id_user,
        ]);

        // Update tanpa mengirim no_pl_jne tetap berhasil (karena ini update data lama)
        $response = $this->actingAs($this->owner)->putJson("/transaksi/packing-list/{$oldPl->kode_trx_pl}", [
            'id_cab' => 'CB-001',
            'tanggal_pl' => '01-01-2025',
            'total' => '200.000,00',
            'discount_charge' => '40.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 6,
            'ket_trx_pl' => 'Updated Old PL Without No JNE',
        ]);

        $response->assertStatus(200);
        $oldPl->refresh();
        $this->assertEquals(6, $oldPl->jumlah_paket);
        $this->assertNull($oldPl->no_pl_jne);

        $oldPl->delete();
    }

    public function test_admin_requires_id_cab_in_master_user(): void
    {
        User::whereIn('id_user', ['USR-TAD1'])->delete();

        // 1. Admin tanpa id_cab -> 422
        $response = $this->actingAs($this->owner)->postJson('/master/user', [
            'id_user' => 'USR-TAD1',
            'nama_user' => 'admin_no_cabang',
            'no_hp' => '081233334444',
            'alamat' => 'Jl. Test',
            'role' => 'admin',
            'status' => 'ST-001',
            'password' => 'secret123',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['id_cab']);

        // 2. Admin dengan id_cab valid -> 200/201
        $responseSuccess = $this->actingAs($this->owner)->postJson('/master/user', [
            'id_user' => 'USR-TAD1',
            'nama_user' => 'admin_with_cabang',
            'no_hp' => '081233334444',
            'alamat' => 'Jl. Test',
            'role' => 'admin',
            'id_cab' => 'CB-001',
            'status' => 'ST-001',
            'password' => 'secret123',
        ]);
        $responseSuccess->assertStatus(200);

        $created = User::find('USR-TAD1');
        $this->assertNotNull($created);
        $this->assertEquals('CB-001', $created->id_cab);

        $created->delete();
    }

    public function test_owner_id_cab_is_forced_to_null(): void
    {
        User::whereIn('id_user', ['USR-TOW1'])->delete();

        // Kirim id_cab saat membuat Owner
        $response = $this->actingAs($this->owner)->postJson('/master/user', [
            'id_user' => 'USR-TOW1',
            'nama_user' => 'owner_forced_null',
            'no_hp' => '081255556666',
            'alamat' => 'Jl. Owner Test',
            'role' => 'owner',
            'id_cab' => 'CB-001', // kirim id_cab, tapi backend harus memaksa null
            'status' => 'ST-001',
            'password' => 'secret123',
        ]);
        $response->assertStatus(200);

        $created = User::find('USR-TOW1');
        $this->assertNotNull($created);
        $this->assertNull($created->id_cab);

        $created->delete();
    }

    public function test_admin_cannot_save_transaction_to_other_branch(): void
    {
        // Admin terikat ke CB-001
        $this->assertEquals('CB-001', $this->admin->id_cab);

        PackingList::where('kode_trx_pl', 'PL-ADM-FORCED')->delete();

        // Admin mencoba mengirim id_cab CB-002
        $response = $this->actingAs($this->admin)->post('/transaksi/packing-list', [
            'kode_trx_pl' => 'PL-ADM-FORCED',
            'no_pl_jne' => 'PL/ADM/FORCE01',
            'id_cab' => 'CB-002', // disengaja kirim cabang lain
            'tanggal_pl' => '05-11-2026',
            'total' => '100.000,00',
            'discount_charge' => '20.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 3,
            'ket_trx_pl' => 'Test admin forced branch',
        ]);
        $response->assertSessionHasNoErrors();

        $saved = PackingList::where('kode_trx_pl', 'PL-ADM-FORCED')->first();
        $this->assertNotNull($saved);
        // Harus tersimpan ke cabang admin (CB-001), bukan CB-002
        $this->assertEquals('CB-001', $saved->id_cab);

        $saved->delete();
    }

    public function test_admin_only_sees_own_branch_transactions_in_index(): void
    {
        PackingList::whereIn('kode_trx_pl', ['PL-FILTER-CB1', 'PL-FILTER-CB2'])->delete();

        PackingList::create([
            'kode_trx_pl' => 'PL-FILTER-CB1',
            'no_pl_jne' => 'PL/FILT/001',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '2026-11-05',
            'total' => 100000,
            'discount_charge' => 20000,
            'e_voucher' => 0,
            'grand_total' => 80000,
            'jumlah_paket' => 2,
            'ket_trx_pl' => 'PL Branch 1 Unique Mark',
            'created_by' => $this->admin->id_user,
        ]);

        PackingList::create([
            'kode_trx_pl' => 'PL-FILTER-CB2',
            'no_pl_jne' => 'PL/FILT/002',
            'id_cab' => 'CB-002',
            'tanggal_pl' => '2026-11-05',
            'total' => 200000,
            'discount_charge' => 40000,
            'e_voucher' => 0,
            'grand_total' => 160000,
            'jumlah_paket' => 4,
            'ket_trx_pl' => 'PL Branch 2 Secret Mark',
            'created_by' => $this->owner->id_user,
        ]);

        $response = $this->actingAs($this->admin)->get('/transaksi/packing-list');
        $response->assertStatus(200);
        $response->assertSee('PL Branch 1 Unique Mark');
        $response->assertDontSee('PL Branch 2 Secret Mark');

        PackingList::whereIn('kode_trx_pl', ['PL-FILTER-CB1', 'PL-FILTER-CB2'])->delete();
    }

    public function test_admin_without_branch_gets_403(): void
    {
        User::where('id_user', 'USR-NO-CAB')->delete();
        $orphanAdmin = User::create([
            'id_user' => 'USR-NO-CAB',
            'username' => 'admin_orphan',
            'no_hp' => '081299990000',
            'alamat' => 'Jl. No Branch',
            'status_id' => 'ST-001',
            'role' => 'admin',
            'id_cab' => null, // anomali: null
            'password' => bcrypt('password'),
        ]);

        $responseIndex = $this->actingAs($orphanAdmin)->get('/transaksi/packing-list');
        $responseIndex->assertStatus(403);
        $this->assertStringContainsString('Akun Anda belum terikat ke cabang. Hubungi Owner.', $responseIndex->getContent());

        $responsePost = $this->actingAs($orphanAdmin)->post('/transaksi/packing-list', [
            'kode_trx_pl' => 'PL-ADM-ORPHAN',
            'no_pl_jne' => 'PL/ADM/ORPHAN',
            'tanggal_pl' => '05-11-2026',
            'total' => '100.000,00',
            'discount_charge' => '20.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 3,
        ]);
        $responsePost->assertStatus(403);

        $orphanAdmin->delete();
    }

    public function test_cannot_delete_cabang_used_by_user(): void
    {
        User::where('id_user', 'USR-INCB')->delete();
        Cabang::where('id_cab', 'CB-TUSER')->delete();
        $cb = Cabang::create([
            'id_cab' => 'CB-TUSER',
            'nama_cabang' => 'Cabang Testing User Protection',
            'alamat' => 'Jl. Proteksi',
            'nama_pic' => 'PIC Proteksi',
            'no_hp' => '081299887766',
        ]);

        User::where('id_user', 'USR-INCB')->delete();
        $userInCb = User::create([
            'id_user' => 'USR-INCB',
            'username' => 'user_in_cb',
            'no_hp' => '081277665544',
            'alamat' => 'Jl. Proteksi 2',
            'status_id' => 'ST-001',
            'role' => 'admin',
            'id_cab' => 'CB-TUSER',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->owner)->deleteJson("/master/cabang/{$cb->id_cab}");
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('pengguna', strtolower($response->json('message')));

        $this->assertNotNull(Cabang::find('CB-TUSER'));

        $userInCb->delete();
        $cb->delete();
    }

    public function test_laporan_komisi_reconciliation_lag_0_and_1(): void
    {
        // Bersihkan data tes jika ada
        PackingList::whereIn('kode_trx_pl', ['PL-RECON-01', 'PL-RECON-02'])->delete();
        Komisi::whereIn('kode_trx_cl', ['CL-RECON-01', 'CL-RECON-02'])->delete();

        // PL Mei 2026: discount_charge 100.000
        PackingList::create([
            'kode_trx_pl' => 'PL-RECON-01',
            'no_pl_jne' => 'PL/RECON/01',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '2026-05-10',
            'total' => 500000,
            'discount_charge' => 100000,
            'e_voucher' => 0,
            'grand_total' => 400000,
            'jumlah_paket' => 5,
            'ket_trx_pl' => 'PL Recon Mei',
            'created_by' => $this->owner->id_user,
        ]);

        // Transfer Mei 2026: 120.000
        Komisi::create([
            'kode_trx_cl' => 'CL-RECON-01',
            'id_cab' => 'CB-001',
            'tanggal_tf' => '2026-05-15',
            'nominal' => 120000,
            'ket_trx_cl' => 'Transfer Mei',
            'created_by' => $this->owner->id_user,
        ]);

        // Transfer Juni 2026: 90.000
        Komisi::create([
            'kode_trx_cl' => 'CL-RECON-02',
            'id_cab' => 'CB-001',
            'tanggal_tf' => '2026-06-15',
            'nominal' => 90000,
            'ket_trx_cl' => 'Transfer Juni',
            'created_by' => $this->owner->id_user,
        ]);

        // --- SKENARIO 1: lag = 0 ---
        config(['laporan.komisi_transfer_lag_bulan' => 0]);

        $resLag0 = $this->actingAs($this->owner)->get('/laporan/komisi?tanggal_awal=01-05-2026&tanggal_akhir=31-05-2026&id_cab=CB-001');
        $resLag0->assertStatus(200);
        // Mei: komisi sistem = 100.000,00, transfer masuk = 120.000,00, selisih = + Rp 20.000,00
        $resLag0->assertSee('100.000,00');
        $resLag0->assertSee('120.000,00');
        $resLag0->assertSee('+ Rp 20.000,00');

        // --- SKENARIO 2: lag = 1 ---
        // Transfer Juni 2026 (90.000) merekonsiliasi PL Mei 2026 (100.000)
        config(['laporan.komisi_transfer_lag_bulan' => 1]);

        $resLag1 = $this->actingAs($this->owner)->get('/laporan/komisi?tanggal_awal=01-06-2026&tanggal_akhir=30-06-2026&id_cab=CB-001');
        $resLag1->assertStatus(200);
        // Juni: komisi sistem = 100.000,00 (dari PL Mei), transfer masuk = 90.000,00, selisih = - Rp 10.000,00
        $resLag1->assertSee('100.000,00');
        $resLag1->assertSee('90.000,00');
        $resLag1->assertSee('- Rp 10.000,00');
        $resLag1->assertSee('1 bulan sebelumnya');

        // Bersihkan
        PackingList::whereIn('kode_trx_pl', ['PL-RECON-01', 'PL-RECON-02'])->delete();
        Komisi::whereIn('kode_trx_cl', ['CL-RECON-01', 'CL-RECON-02'])->delete();
    }

    public function test_laporan_komisi_asymmetric_and_branch_filter(): void
    {
        PackingList::whereIn('kode_trx_pl', ['PL-ASYM-01'])->delete();
        Komisi::whereIn('kode_trx_cl', ['CL-ASYM-01'])->delete();

        // PL di CB-001 (Juli 2026) tanpa ada transfer sama sekali
        PackingList::create([
            'kode_trx_pl' => 'PL-ASYM-01',
            'no_pl_jne' => 'PL/ASYM/01',
            'id_cab' => 'CB-001',
            'tanggal_pl' => '2026-07-10',
            'total' => 250000,
            'discount_charge' => 50000,
            'e_voucher' => 0,
            'grand_total' => 200000,
            'jumlah_paket' => 2,
            'ket_trx_pl' => 'PL Asym',
            'created_by' => $this->owner->id_user,
        ]);

        config(['laporan.komisi_transfer_lag_bulan' => 0]);

        // Cek cabang CB-001: transfer 0, komisi sistem 50.000, selisih - Rp 50.000,00
        $resCb1 = $this->actingAs($this->owner)->get('/laporan/komisi?tanggal_awal=01-07-2026&tanggal_akhir=31-07-2026&id_cab=CB-001');
        $resCb1->assertStatus(200);
        $resCb1->assertSee('50.000,00');
        $resCb1->assertSee('- Rp 50.000,00');

        // Cek cabang CB-002: tidak boleh melihat data cabang CB-001
        $resCb2 = $this->actingAs($this->owner)->get('/laporan/komisi?tanggal_awal=01-07-2026&tanggal_akhir=31-07-2026&id_cab=CB-002');
        $resCb2->assertStatus(200);
        $resCb2->assertDontSee('50.000,00');

        PackingList::whereIn('kode_trx_pl', ['PL-ASYM-01'])->delete();
    }

    public function test_owner_can_edit_and_delete_pl_anytime(): void
    {
        PackingList::where('kode_trx_pl', 'PL-OWN-EDIT')->delete();

        // Buat PL 10 hari lalu di cabang CB-002
        $pastDate = Carbon::now('Asia/Jakarta')->subDays(10);
        $pl = PackingList::create([
            'kode_trx_pl' => 'PL-OWN-EDIT',
            'no_pl_jne' => 'PL/OWN/01',
            'id_cab' => 'CB-002',
            'tanggal_pl' => $pastDate->format('Y-m-d'),
            'total' => 300000,
            'discount_charge' => 60000,
            'e_voucher' => 0,
            'grand_total' => 240000,
            'jumlah_paket' => 4,
            'ket_trx_pl' => 'Old PL for Owner',
            'created_by' => $this->owner->id_user,
        ]);
        $pl->created_at = $pastDate;
        $pl->save();

        // 1. Owner Edit PL (kapan saja & cabang mana saja)
        $resEdit = $this->actingAs($this->owner)->putJson("/transaksi/packing-list/{$pl->kode_trx_pl}", [
            'no_pl_jne' => 'PL/OWN/01',
            'id_cab' => 'CB-002',
            'tanggal_pl' => $pastDate->format('d-m-Y'),
            'total' => '350.000,00',
            'discount_charge' => '70.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 5,
            'ket_trx_pl' => 'Updated by Owner',
        ]);
        $resEdit->assertStatus(200);

        $pl->refresh();
        $this->assertEquals(5, $pl->jumlah_paket);

        // 2. Owner Hapus PL
        $resDelete = $this->actingAs($this->owner)->deleteJson("/transaksi/packing-list/{$pl->kode_trx_pl}");
        $resDelete->assertStatus(200);
        $this->assertNull(PackingList::find('PL-OWN-EDIT'));
    }

    public function test_admin_can_edit_pl_created_today_in_own_branch(): void
    {
        PackingList::where('kode_trx_pl', 'PL-ADM-TODAY')->delete();

        // Buat PL hari ini di CB-001 (cabang admin)
        $today = Carbon::now('Asia/Jakarta');
        $pl = PackingList::create([
            'kode_trx_pl' => 'PL-ADM-TODAY',
            'no_pl_jne' => 'PL/ADM/TODAY',
            'id_cab' => 'CB-001',
            'tanggal_pl' => $today->format('Y-m-d'),
            'total' => 200000,
            'discount_charge' => 40000,
            'e_voucher' => 0,
            'grand_total' => 160000,
            'jumlah_paket' => 2,
            'ket_trx_pl' => 'Today PL for Admin',
            'created_by' => $this->admin->id_user,
        ]);

        $resEdit = $this->actingAs($this->admin)->putJson("/transaksi/packing-list/{$pl->kode_trx_pl}", [
            'no_pl_jne' => 'PL/ADM/TODAY',
            'id_cab' => 'CB-001',
            'tanggal_pl' => $today->format('d-m-Y'),
            'total' => '220.000,00',
            'discount_charge' => '44.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 3,
            'ket_trx_pl' => 'Today PL Updated by Admin',
        ]);
        $resEdit->assertStatus(200);

        $pl->refresh();
        $this->assertEquals(3, $pl->jumlah_paket);

        $pl->delete();
    }

    public function test_admin_rejected_403_when_editing_pl_created_yesterday(): void
    {
        PackingList::where('kode_trx_pl', 'PL-ADM-YEST')->delete();

        // Buat PL kemarin di CB-001
        $yesterday = Carbon::now('Asia/Jakarta')->subDay();
        $pl = PackingList::create([
            'kode_trx_pl' => 'PL-ADM-YEST',
            'no_pl_jne' => 'PL/ADM/YEST',
            'id_cab' => 'CB-001',
            'tanggal_pl' => $yesterday->format('Y-m-d'),
            'total' => 200000,
            'discount_charge' => 40000,
            'e_voucher' => 0,
            'grand_total' => 160000,
            'jumlah_paket' => 2,
            'ket_trx_pl' => 'Yesterday PL',
            'created_by' => $this->admin->id_user,
        ]);
        $pl->created_at = $yesterday;
        $pl->save();

        $resEdit = $this->actingAs($this->admin)->putJson("/transaksi/packing-list/{$pl->kode_trx_pl}", [
            'no_pl_jne' => 'PL/ADM/YEST',
            'id_cab' => 'CB-001',
            'tanggal_pl' => $yesterday->format('d-m-Y'),
            'total' => '250.000,00',
            'discount_charge' => '50.000,00',
            'e_voucher' => '0,00',
            'jumlah_paket' => 4,
            'ket_trx_pl' => 'Admin Trying to Edit Yesterday PL',
        ]);
        $resEdit->assertStatus(403);
        $this->assertStringContainsString('Packing List hanya dapat diubah pada hari yang sama saat dibuat. Hubungi Owner.', $resEdit->json('message') ?? $resEdit->getContent());

        $pl->delete();
    }

    public function test_admin_rejected_403_when_deleting_pl(): void
    {
        PackingList::where('kode_trx_pl', 'PL-ADM-DEL')->delete();

        $pl = PackingList::create([
            'kode_trx_pl' => 'PL-ADM-DEL',
            'no_pl_jne' => 'PL/ADM/DEL',
            'id_cab' => 'CB-001',
            'tanggal_pl' => Carbon::now('Asia/Jakarta')->format('Y-m-d'),
            'total' => 100000,
            'discount_charge' => 20000,
            'e_voucher' => 0,
            'grand_total' => 80000,
            'jumlah_paket' => 1,
            'ket_trx_pl' => 'Admin cannot delete',
            'created_by' => $this->admin->id_user,
        ]);

        $resDelete = $this->actingAs($this->admin)->deleteJson("/transaksi/packing-list/{$pl->kode_trx_pl}");
        $resDelete->assertStatus(403);
        $this->assertStringContainsString('Anda tidak berwenang melakukan aksi ini.', $resDelete->json('message') ?? $resDelete->getContent());

        $pl->delete();
    }

    public function test_admin_rejected_403_on_operasional_and_komisi_edit_delete(): void
    {
        $ops = Operasional::first();
        $this->assertNotNull($ops);

        $komisi = Komisi::first();
        $this->assertNotNull($komisi);

        // 1. Admin Edit Operasional -> 403
        $resOpsEdit = $this->actingAs($this->admin)->putJson("/transaksi/operasional/{$ops->kode_trx_ops}", [
            'id_cab' => 'CB-001',
            'tanggal_trx_ops' => '05-11-2026',
            'beban_ops' => 'Listrik & Air',
            'nominal' => '100.000,00',
            'ket_trx_ops' => 'Admin try edit ops',
        ]);
        $resOpsEdit->assertStatus(403);

        // 2. Admin Hapus Operasional -> 403
        $resOpsDel = $this->actingAs($this->admin)->deleteJson("/transaksi/operasional/{$ops->kode_trx_ops}");
        $resOpsDel->assertStatus(403);

        // 3. Admin Edit Komisi -> 403
        $resKomEdit = $this->actingAs($this->admin)->putJson("/transaksi/komisi/{$komisi->kode_trx_cl}", [
            'id_cab' => 'CB-001',
            'tanggal_tf' => '05-11-2026',
            'nominal' => '150.000,00',
            'ket_trx_cl' => 'Admin try edit komisi',
        ]);
        $resKomEdit->assertStatus(403);

        // 4. Admin Hapus Komisi -> 403
        $resKomDel = $this->actingAs($this->admin)->deleteJson("/transaksi/komisi/{$komisi->kode_trx_cl}");
        $resKomDel->assertStatus(403);
    }
}
