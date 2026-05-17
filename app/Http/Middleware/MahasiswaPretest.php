<?php

namespace App\Http\Middleware;

use App\Models\PretestResult;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan mahasiswa sudah mengerjakan pre-test sebelum mengakses
 * halaman lain. Jika belum, redirect ke /pretest.
 */
class MahasiswaPretest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('mahasiswa')) {
            $hasPretest = PretestResult::where('user_id', Auth::id())->exists();

            $isOnPretestRoute = $request->routeIs('mahasiswa.pretest')
                || $request->routeIs('mahasiswa.pretest.submit')
                || $request->routeIs('mahasiswa.pretest.hasil');

            if (! $hasPretest && ! $isOnPretestRoute) {
                return redirect()->route('mahasiswa.pretest');
            }
        }

        return $next($request);
    }
}
