<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\User;
use App\Models\Cabang;
use App\Models\BebanOps;
use App\Models\PackingList;
use App\Support\CodeGenerator;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreCabangRequest;
use App\Http\Requests\UpdateCabangRequest;
use App\Http\Requests\StoreOpsRequest;
use App\Http\Requests\UpdateOpsRequest;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterController extends Controller
{
    // ==========================================
    // 1. MASTER USER
    // ==========================================
    public function user()
    {
        $users = User::with(['status', 'cabang'])
            ->latest('created_at')
            ->get()
            ->map(function ($u) {
                return [
                    'id_user' => $u->id_user,
                    'nama_user' => $u->username,
                    'no_hp' => $u->no_hp,
                    'alamat' => $u->alamat,
                    'status' => $u->status ? $u->status->nama_status : 'Aktif',
                    'status_id' => $u->status_id,
                    'level' => ucfirst($u->role),
                    'role' => ucfirst($u->role),
                    'id_cab' => $u->id_cab,
                    'nama_cabang' => $u->cabang ? $u->cabang->nama_cabang : null,
                    'password' => '********',
                ];
            })
            ->toArray();

        $statuses = Status::orderBy('level')->get()->toArray();
        $cabangs = Cabang::orderBy('id_cab')->get()->toArray();
        $nextId = CodeGenerator::nextUserId();

        return view('master.user', compact('users', 'statuses', 'cabangs', 'nextId'));
    }

    public function storeUser(StoreUserRequest $request)
    {
        $statusObj = Status::where('id_status', $request->status)
            ->orWhere('nama_status', $request->status)
            ->first();
        $statusId = $statusObj ? $statusObj->id_status : 'ST-001';

        $idUser = $request->id_user ?: CodeGenerator::nextUserId();
        $role = strtolower($request->role ?? $request->level ?? 'admin');

        // Bagian A.4: Bila Role = Owner, id_cab dipaksa NULL di backend, apa pun yang dikirim form.
        $idCab = ($role === 'admin') ? $request->id_cab : null;

        $user = User::create([
            'id_user' => $idUser,
            'username' => $request->nama_user,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'status_id' => $statusId,
            'role' => $role,
            'id_cab' => $idCab,
            'password' => Hash::make($request->password),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna baru berhasil ditambahkan.',
                'data' => $user,
            ]);
        }

        return redirect()->route('master.user')->with('success', 'Pengguna baru berhasil disimpan.');
    }

    public function updateUser(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        $statusObj = Status::where('id_status', $request->status)
            ->orWhere('nama_status', $request->status)
            ->first();
        $statusId = $statusObj ? $statusObj->id_status : $user->status_id;
        $targetLevel = $statusObj ? (int) $statusObj->level : 1;
        $targetRole = strtolower($request->role ?? $request->level ?? $user->role);

        // Aturan Owner (BAGIAN C.5):
        // Owner tidak boleh mengubah status dirinya menjadi non-Level 1, menghapus dirinya sendiri, atau mengubah role dirinya sendiri.
        if (auth()->id() === $user->id_user && auth()->user()->role === 'owner') {
            if ($targetRole !== 'owner') {
                $msg = 'Anda tidak dapat mengubah role akun Anda sendiri.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }

            if ($targetLevel !== 1) {
                $statusName = $statusObj ? $statusObj->nama_status : 'non-Aktif';
                $msg = "Owner tidak dapat mengubah status dirinya menjadi non-Aktif ({$statusName}).";
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }
        }

        // Pastikan selalu ada minimal satu owner berstatus Aktif
        if ($user->role === 'owner' && ($targetRole !== 'owner' || $targetLevel !== 1)) {
            $otherActiveOwners = User::where('role', 'owner')
                ->where('id_user', '!=', $user->id_user)
                ->whereHas('status', fn ($q) => $q->where('level', 1))
                ->count();

            if ($otherActiveOwners < 1) {
                $msg = 'Sistem harus memiliki minimal satu Owner berstatus Aktif.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }
        }

        $idCab = ($targetRole === 'admin') ? $request->id_cab : null;

        $user->username = $request->nama_user;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->status_id = $statusId;
        $user->role = $targetRole;
        $user->id_cab = $idCab;

        if ($request->filled('password') && $request->password !== '********') {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diperbarui.',
                'data' => $user,
            ]);
        }

        return redirect()->route('master.user')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroyUser(Request $request, string $id)
    {
        if (auth()->id() === $id) {
            $msg = 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        $user = User::findOrFail($id);

        if ($user->role === 'owner') {
            $otherActiveOwners = User::where('role', 'owner')
                ->where('id_user', '!=', $user->id_user)
                ->whereHas('status', fn ($q) => $q->where('level', 1))
                ->count();

            if ($otherActiveOwners < 1) {
                $msg = 'Tidak dapat menghapus user ini karena sistem harus memiliki minimal satu Owner berstatus Aktif.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
            }
        }

        if (PackingList::where('created_by', $id)->exists()) {
            $msg = 'Tidak dapat menghapus user ini karena terdapat transaksi yang terkait dengannya.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        try {
            $user->delete();
            $msg = "Pengguna {$user->username} ({$id}) berhasil dihapus.";
            return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Tidak dapat menghapus user ini karena terdapat transaksi yang terkait dengannya.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }
    }

    // ==========================================
    // 2. MASTER CABANG
    // ==========================================
    public function cabang()
    {
        $cabangs = Cabang::latest('created_at')->get()->toArray();
        $nextId = CodeGenerator::nextCabangId();
        return view('master.cabang', compact('cabangs', 'nextId'));
    }

    public function storeCabang(StoreCabangRequest $request)
    {
        $idCab = $request->id_cab ?: CodeGenerator::nextCabangId();

        $cabang = Cabang::create([
            'id_cab' => $idCab,
            'nama_cabang' => $request->nama_cabang,
            'alamat' => $request->alamat,
            'nama_pic' => $request->nama_pic,
            'user_name_jne' => $request->user_name_jne ? trim($request->user_name_jne) : null,
            'no_hp' => $request->no_hp,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil ditambahkan.',
                'data' => $cabang,
            ]);
        }

        return redirect()->route('master.cabang')->with('success', 'Cabang baru berhasil disimpan.');
    }

    public function updateCabang(UpdateCabangRequest $request, string $id)
    {
        $cabang = Cabang::findOrFail($id);

        $cabang->update([
            'nama_cabang' => $request->nama_cabang,
            'alamat' => $request->alamat,
            'nama_pic' => $request->nama_pic,
            'user_name_jne' => $request->user_name_jne ? trim($request->user_name_jne) : null,
            'no_hp' => $request->no_hp,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data cabang berhasil diperbarui.',
                'data' => $cabang,
            ]);
        }

        return redirect()->route('master.cabang')->with('success', 'Data cabang berhasil diperbarui.');
    }

    public function destroyCabang(Request $request, string $id)
    {
        $cabang = Cabang::findOrFail($id);

        $hasUsers = $cabang->users()->exists();
        if ($hasUsers) {
            $msg = "Cabang {$cabang->nama_cabang} ({$id}) tidak dapat dihapus karena masih digunakan oleh data pengguna (Admin).";
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        $hasTransactions = $cabang->packingLists()->exists()
            || $cabang->operasionals()->exists()
            || $cabang->komisis()->exists();

        if ($hasTransactions) {
            $msg = "Cabang {$cabang->nama_cabang} ({$id}) tidak dapat dihapus karena masih digunakan dalam data transaksi.";
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        try {
            $cabang->delete();
            $msg = "Cabang {$cabang->nama_cabang} ({$id}) berhasil dihapus.";
            return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = "Cabang {$cabang->nama_cabang} ({$id}) tidak dapat dihapus karena masih memiliki relasi data terkait.";
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }
    }

    // ==========================================
    // 3. MASTER OPS
    // ==========================================
    public function ops()
    {
        $ops = BebanOps::latest('created_at')->get()->toArray();
        $nextId = CodeGenerator::nextOpsId();
        return view('master.ops', compact('ops', 'nextId'));
    }

    public function storeOps(StoreOpsRequest $request)
    {
        $idOps = $request->id_ops ?: CodeGenerator::nextOpsId();

        $ops = BebanOps::create([
            'id_ops' => $idOps,
            'beban_ops' => $request->beban_ops,
            'ket_ops' => $request->ket_ops,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori operasional berhasil ditambahkan.',
                'data' => $ops,
            ]);
        }

        return redirect()->route('master.ops')->with('success', 'Kategori operasional baru berhasil disimpan.');
    }

    public function updateOps(UpdateOpsRequest $request, string $id)
    {
        $ops = BebanOps::findOrFail($id);

        $ops->update([
            'beban_ops' => $request->beban_ops,
            'ket_ops' => $request->ket_ops,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data operasional berhasil diperbarui.',
                'data' => $ops,
            ]);
        }

        return redirect()->route('master.ops')->with('success', 'Data operasional berhasil diperbarui.');
    }

    public function destroyOps(Request $request, string $id)
    {
        $ops = BebanOps::findOrFail($id);

        if ($ops->operasionals()->exists()) {
            $msg = "Kategori operasional {$ops->beban_ops} ({$id}) tidak dapat dihapus karena masih digunakan pada data transaksi operasional.";
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        try {
            $ops->delete();
            $msg = "Kategori ops {$ops->beban_ops} ({$id}) berhasil dihapus.";
            return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Kategori operasional ini tidak dapat dihapus karena masih digunakan pada data transaksi operasional.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }
    }

    // ==========================================
    // 4. MASTER STATUS
    // ==========================================
    public function status()
    {
        $statuses = Status::orderBy('level')->latest('created_at')->get()->toArray();
        $nextId = CodeGenerator::nextStatusId();
        return view('master.status', compact('statuses', 'nextId'));
    }

    public function storeStatus(StoreStatusRequest $request)
    {
        $idStatus = $request->id_status ?: CodeGenerator::nextStatusId();

        $status = Status::create([
            'id_status' => $idStatus,
            'nama_status' => $request->nama_status,
            'level' => $request->level,
            'ket_status' => $request->ket_status,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil ditambahkan.',
                'data' => $status,
            ]);
        }

        return redirect()->route('master.status')->with('success', 'Status baru berhasil disimpan.');
    }

    public function updateStatus(UpdateStatusRequest $request, string $id)
    {
        $status = Status::findOrFail($id);

        // BAGIAN C.3: Status Level 1 (Aktif) tidak boleh diubah levelnya
        if ((int) $status->level === 1 && (int) $request->level !== 1) {
            $msg = 'Level status untuk status Level 1 (Aktif) tidak boleh diubah.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        $status->update([
            'nama_status' => $request->nama_status,
            'level' => $request->level,
            'ket_status' => $request->ket_status,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data status berhasil diperbarui.',
                'data' => $status,
            ]);
        }

        return redirect()->route('master.status')->with('success', 'Data status berhasil diperbarui.');
    }

    public function destroyStatus(Request $request, string $id)
    {
        $status = Status::findOrFail($id);

        // BAGIAN C.3: Status Level 1 (Aktif) tidak boleh dihapus
        if ((int) $status->level === 1 || $status->id_status === 'ST-001') {
            $msg = 'Status Level 1 (Aktif) tidak boleh dihapus.';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        // BAGIAN C.4: Status yang masih dipakai user tidak boleh dihapus
        if ($status->users()->exists()) {
            $msg = "Status {$status->nama_status} ({$id}) tidak dapat dihapus karena masih digunakan pada data pengguna (User).";
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }

        try {
            $status->delete();
            $msg = "Status {$status->nama_status} ({$id}) berhasil dihapus.";
            return $request->wantsJson() ? response()->json(['success' => true, 'message' => $msg]) : back()->with('success', $msg);
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Status ini tidak dapat dihapus karena masih digunakan pada data pengguna (User).';
            return $request->wantsJson() ? response()->json(['success' => false, 'message' => $msg], 422) : back()->with('error', $msg);
        }
    }
}
