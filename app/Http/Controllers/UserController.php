<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with(['guru', 'orangTua'])
            ->latest()
            ->paginate(15);
        $title = 'Daftar User';

        return view('admin.users.index', compact('users', 'title'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        // Ambil guru yang belum punya user
        $guruTersedia = Guru::whereNull('user_id')->get();
        // Ambil orang tua yang belum punya user
        $orangTuaTersedia = OrangTua::whereNull('user_id')->get();

        $title = 'Tambah User Baru';

        return view('admin.users.create', compact('guruTersedia', 'orangTuaTersedia', 'title'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,guru,orangtua',
            'guru_id' => 'nullable|exists:gurus,id|required_if:role,guru',
            'orang_tua_id' => 'nullable|exists:orang_tuas,id|required_if:role,orangtua',
        ]);

        DB::beginTransaction();
        try {
            // Validasi: Jika role guru, guru_id harus belum punya user
            if ($request->role === 'guru' && $request->guru_id) {
                $guru = Guru::find($request->guru_id);
                if ($guru->user_id) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru sudah memiliki akun user');
                }
            }

            // Validasi: Jika role orangtua, orang_tua_id harus belum punya user
            if ($request->role === 'orangtua' && $request->orang_tua_id) {
                $orangTua = OrangTua::find($request->orang_tua_id);
                if ($orangTua->user_id) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Orang tua sudah memiliki akun user');
                }
            }

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Link user ke guru atau orang tua
            if ($request->role === 'guru' && $request->guru_id) {
                Guru::where('id', $request->guru_id)->update(['user_id' => $user->id]);
            } elseif ($request->role === 'orangtua' && $request->orang_tua_id) {
                OrangTua::where('id', $request->orang_tua_id)->update(['user_id' => $user->id]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['guru.kelasWali', 'guru.mapels', 'orangTua.siswas'])
            ->findOrFail($id);
        $title = 'Detail User';

        return view('admin.users.show', compact('user', 'title'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::with(['guru', 'orangTua'])->findOrFail($id);

        // Ambil guru yang belum punya user atau guru milik user ini
        $guruTersedia = Guru::where(function ($query) use ($user) {
            $query->whereNull('user_id')
                ->orWhere('user_id', $user->id);
        })->get();

        // Ambil orang tua yang belum punya user atau orang tua milik user ini
        $orangTuaTersedia = OrangTua::where(function ($query) use ($user) {
            $query->whereNull('user_id')
                ->orWhere('user_id', $user->id);
        })->get();

        $title = 'Edit User';

        return view('admin.users.edit', compact('user', 'guruTersedia', 'orangTuaTersedia', 'title'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id)
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,guru,orangtua',
            'guru_id' => 'nullable|exists:gurus,id|required_if:role,guru',
            'orang_tua_id' => 'nullable|exists:orang_tuas,id|required_if:role,orangtua',
        ]);

        DB::beginTransaction();
        try {
            // Validasi: Jika role guru, guru_id harus belum punya user (kecuali user ini sendiri)
            if ($request->role === 'guru' && $request->guru_id) {
                $guru = Guru::find($request->guru_id);
                if ($guru->user_id && $guru->user_id != $user->id) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Guru sudah memiliki akun user lain');
                }
            }

            // Validasi: Jika role orangtua, orang_tua_id harus belum punya user (kecuali user ini sendiri)
            if ($request->role === 'orangtua' && $request->orang_tua_id) {
                $orangTua = OrangTua::find($request->orang_tua_id);
                if ($orangTua->user_id && $orangTua->user_id != $user->id) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Orang tua sudah memiliki akun user lain');
                }
            }

            // Update user
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            // Update password jika diisi
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // Lepas relasi user lama
            if ($user->guru) {
                $user->guru->update(['user_id' => null]);
            }
            if ($user->orangTua) {
                $user->orangTua->update(['user_id' => null]);
            }

            // Link user ke guru atau orang tua baru
            if ($request->role === 'guru' && $request->guru_id) {
                Guru::where('id', $request->guru_id)->update(['user_id' => $user->id]);
            } elseif ($request->role === 'orangtua' && $request->orang_tua_id) {
                OrangTua::where('id', $request->orang_tua_id)->update(['user_id' => $user->id]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            // Tidak bisa hapus admin jika hanya ada 1 admin
            if ($user->role === 'admin') {
                $adminCount = User::where('role', 'admin')->count();
                if ($adminCount <= 1) {
                    return redirect()
                        ->back()
                        ->with('error', 'Tidak dapat menghapus admin terakhir');
                }
            }

            // Lepas relasi dari guru/orangtua
            if ($user->guru) {
                $user->guru->update(['user_id' => null]);
            }
            if ($user->orangTua) {
                $user->orangTua->update(['user_id' => null]);
            }

            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Password berhasil direset');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }

    /**
     * Show form untuk bulk create users untuk guru
     */
    public function bulkCreateGuru()
    {
        $guruTersedia = Guru::whereNull('user_id')->get();
        $title = 'Buat User untuk Guru (Bulk)';

        return view('admin.users.bulk-guru', compact('guruTersedia', 'title'));
    }

    /**
     * Bulk create users untuk guru
     */
    public function bulkStoreGuru(Request $request)
    {
        $validated = $request->validate([
            'guru_ids' => 'required|array|min:1',
            'guru_ids.*' => 'exists:gurus,id',
            'default_password' => ['required', Password::min(8)],
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            $failed = [];

            foreach ($request->guru_ids as $guruId) {
                $guru = Guru::find($guruId);

                // Skip jika sudah punya user
                if ($guru->user_id) {
                    $failed[] = $guru->nama_guru . ' (Sudah memiliki user)';
                    continue;
                }

                // Generate email dari NIP
                $email = 'guru_' . $guru->nip . '@sekolah.com';

                // Cek apakah email sudah dipakai
                if (User::where('email', $email)->exists()) {
                    $failed[] = $guru->nama_guru . ' (Email sudah dipakai)';
                    continue;
                }

                // Buat user
                $user = User::create([
                    'name' => $guru->nama_guru,
                    'email' => $email,
                    'password' => Hash::make($request->default_password),
                    'role' => 'guru',
                ]);

                // Link ke guru
                $guru->update(['user_id' => $user->id]);

                $created++;
            }

            DB::commit();

            $message = "Berhasil membuat {$created} akun user.";
            if (count($failed) > 0) {
                $message .= " Gagal: " . implode(', ', $failed);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage());
        }
    }

    /**
     * Show form untuk bulk create users untuk orang tua
     */
    public function bulkCreateOrangTua()
    {
        $orangTuaTersedia = OrangTua::whereNull('user_id')->get();
        $title = 'Buat User untuk Orang Tua (Bulk)';

        return view('admin.users.bulk-orangtua', compact('orangTuaTersedia', 'title'));
    }

    /**
     * Bulk create users untuk orang tua
     */
    public function bulkStoreOrangTua(Request $request)
    {
        $validated = $request->validate([
            'orang_tua_ids' => 'required|array|min:1',
            'orang_tua_ids.*' => 'exists:orang_tuas,id',
            'default_password' => ['required', Password::min(8)],
        ]);

        DB::beginTransaction();
        try {
            $created = 0;
            $failed = [];

            foreach ($request->orang_tua_ids as $orangTuaId) {
                $orangTua = OrangTua::find($orangTuaId);

                // Skip jika sudah punya user
                if ($orangTua->user_id) {
                    $failed[] = $orangTua->nama_orang_tua . ' (Sudah memiliki user)';
                    continue;
                }

                // Generate email dari no_hp (remove non-numeric)
                $phoneClean = preg_replace('/[^0-9]/', '', $orangTua->nomor_tlp);
                $email = 'ortu_' . $phoneClean . '@sekolah.com';

                // Cek apakah email sudah dipakai
                if (User::where('email', $email)->exists()) {
                    // Tambahkan random number
                    $email = 'ortu_' . $phoneClean . '_' . rand(100, 999) . '@sekolah.com';
                }

                // Buat user
                $user = User::create([
                    'name' => $orangTua->nama_orang_tua,
                    'email' => $email,
                    'password' => Hash::make($request->default_password),
                    'role' => 'orangtua',
                ]);

                // Link ke orang tua
                $orangTua->update(['user_id' => $user->id]);

                $created++;
            }

            DB::commit();

            $message = "Berhasil membuat {$created} akun user.";
            if (count($failed) > 0) {
                $message .= " Gagal: " . implode(', ', $failed);
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage());
        }
    }
}
