<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Alur Pengalihan Rute Login Admin vs Publik (Slide 22 & 23)
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Deteksi Pengalihan: Admin / Staf diarahkan ke Backoffice, Mahasiswa ke Beranda (Slide 23)
        if ($user && $user->hasRole(['admin', 'staf', 'dosen'])) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('beranda', absolute: false));
    }

    /**
     * Auto-Login Test Accounts untuk Evaluasi Demo Dosen / Asdos di Kelas (Slide 41).
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $email = match ($role) {
            'admin' => 'admin@akademik.its.ac.id',
            'staf' => 'staf@akademik.its.ac.id',
            'dosen' => 'dosen@akademik.its.ac.id',
            default => 'mahasiswa@student.its.ac.id',
        };

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('login')
                ->withErrors(['email' => "Akun demo {$role} belum di-seed. Silakan jalankan `php artisan db:seed`."]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        if ($user->hasRole(['admin', 'staf', 'dosen'])) {
            return redirect()->route('admin.dashboard')
                ->with('info', 'Login instan berhasil sebagai '.strtoupper($role)." ({$user->name}).");
        }

        return redirect()->route('beranda')
            ->with('info', "Login instan berhasil sebagai Mahasiswa ({$user->name}).");
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
