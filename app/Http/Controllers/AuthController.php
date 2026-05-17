<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Kelas;
use App\Models\PretestResult;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /** Tampilkan halaman login. Jika sudah login, langsung redirect. */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('login');
    }

    /** Proses login. */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user());
    }

    /** Tampilkan halaman registrasi. */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        $kelasList = Kelas::where('is_active', true)->orderBy('name')->get();

        return view('register', compact('kelasList'));
    }

    /** Proses registrasi mahasiswa baru. */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'nim'                   => ['required', 'string', 'max:50', 'unique:users,nim'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'kelas_id'              => ['required', 'exists:classes,id'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah terdaftar.',
            'nim.required'          => 'NIM wajib diisi.',
            'nim.unique'            => 'NIM sudah terdaftar.',
            'kelas_id.required'     => 'Pilih kelas terlebih dahulu.',
            'kelas_id.exists'       => 'Kelas yang dipilih tidak valid.',
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'nim'      => $validated['nim'],
            'phone'    => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('mahasiswa');

        ClassEnrollment::create([
            'class_id' => $validated['kelas_id'],
            'user_id'  => $user->id,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('mahasiswa.pretest')
            ->with('info', 'Akun berhasil dibuat. Selamat datang, ' . $user->name . '!');
    }

    /** Logout. */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole('dosen')) {
            // Filament panel handles dosen/admin dashboard
            return redirect('/dosen');
        }

        if($user->hasRole('admin')) {
            return redirect('/admin');
        }
        

        if ($user->hasRole('mahasiswa')) {
            // Jika belum pernah mengerjakan pre-test → arahkan ke pre-test
            $hasDonePretest = PretestResult::where('user_id', $user->id)->exists();

            return $hasDonePretest
                ? redirect()->route('mahasiswa.dashboard')
                : redirect()->route('mahasiswa.pretest');
        }

        // Fallback: logout dan kembali ke login
        Auth::logout();
        return redirect()->route('login')->withErrors(['email' => 'Akun Anda tidak memiliki akses.']);
    }
}
