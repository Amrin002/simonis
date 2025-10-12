<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Cek role utama (admin/guru/orangtua)
        if (!in_array($user->role, $roles)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Jika guru, cek tipe
        if ($user->role === 'guru' && $user->guru) {
            $guru = $user->guru;

            // Jika route butuh wali kelas
            if (in_array('walikelas', $roles) && !$guru->isWaliKelas()) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Hanya wali kelas yang bisa mengakses halaman ini');
            }

            // Jika route butuh guru mapel
            if (in_array('gurumapel', $roles) && !$guru->isGuruMapel()) {
                return redirect()->route('guru.dashboard')
                    ->with('error', 'Hanya guru mapel yang bisa mengakses halaman ini');
            }
        }

        return $next($request);
    }
}
