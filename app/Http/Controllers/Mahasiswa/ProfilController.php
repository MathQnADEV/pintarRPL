<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PretestResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(): View
    {
        $user          = Auth::user();
        $pretestResult = PretestResult::where('user_id', $user->id)->latest()->first();
        $level         = PretestResult::effectiveLevel($user->id);
        $kelas         = $user->kelas()->first();

        return view('profil', compact('user', 'pretestResult', 'level', 'kelas'));
    }
}
