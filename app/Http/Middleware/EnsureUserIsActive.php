<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user()->fresh('status');
            if (!$user || !$user->status || (int) $user->status->level !== 1) {
                $statusName = $user && $user->status ? $user->status->nama_status : 'Non-Aktif';
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = "Sesi Anda berakhir karena status akun Anda saat ini sedang {$statusName}. Hubungi Owner.";

                if ($request->wantsJson()) {
                    return response()->json(['message' => $message], 403);
                }

                return redirect()->route('login')->withErrors(['username' => $message]);
            }
        }

        return $next($request);
    }
}
