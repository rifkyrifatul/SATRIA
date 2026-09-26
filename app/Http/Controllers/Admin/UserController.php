<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffDivision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public const DEFAULT_PASSWORD = '@Password1_';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        
        $users = User::with('division')
            ->where('role', '!=', 'super_admin')
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query, $roleFilter) {
                return $query->where('role', $roleFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));
            
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $divisions = StaffDivision::all();
        return view('admin.users.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'division_id' => ['nullable', 'exists:staff_divisions,id'],

            'role' => ['required', 'in:super_admin,admin,staff'],
            'admin_level' => ['nullable', 'required_if:role,admin', 'in:admin_1,admin_2,admin_3'],
        ]);

        $validator->after(function ($v) use ($request) {
            if ($request->role === 'super_admin') {
                if (User::where('role', 'super_admin')->exists()) {
                    $v->errors()->add('role', 'Hanya boleh ada 1 akun dengan role Super Admin.');
                }
            }
        });

        $validator->validate();

        $user = User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'password'             => Hash::make(self::DEFAULT_PASSWORD),
            'role'                 => $request->role,
            'admin_level'          => $request->role === 'admin' ? $request->admin_level : null,
            'division_id'          => $request->role === 'staff' ? $request->division_id : null,
            'must_change_password' => true, // Wajib ganti password saat login pertama
        ]);

        return redirect()->route('super_admin.users.index')
            ->with('success', 'User berhasil ditambahkan. Password default: ' . self::DEFAULT_PASSWORD);
    }

    public function destroy(User $user)
    {
        // Jangan biarkan user menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        // Mencegah super admin menghapus super admin lainnya
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Tidak dapat menghapus akun Super Admin lain.');
        }

        try {
            // Hapus file foto profil fisik dari server jika ada
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }

            $user->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return back()->with('error', 'Tidak dapat menghapus akun ini karena memiliki riwayat surat atau log yang terkait.');
            }
            return back()->with('error', 'Terjadi kesalahan saat menghapus akun: ' . $e->getMessage());
        }

        return back()->with('success', 'Akun berhasil dihapus.');
    }

    public function edit(User $user)
    {
        $divisions = StaffDivision::all();
        return view('admin.users.edit', compact('user', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'division_id' => ['nullable', 'exists:staff_divisions,id'],
            'role' => ['required', 'in:super_admin,admin,staff'],
            'admin_level' => ['nullable', 'required_if:role,admin', 'in:admin_1,admin_2,admin_3'],
        ]);

        $validator->after(function ($v) use ($request, $user) {
            if ($request->role === 'super_admin') {
                if (User::where('role', 'super_admin')->where('id', '!=', $user->id)->exists()) {
                    $v->errors()->add('role', 'Hanya boleh ada 1 akun dengan role Super Admin.');
                }
            }
        });

        $validator->validate();

        // Mencegah modifikasi akun super admin lain
        if ($user->role === 'super_admin' && $user->id !== auth()->id()) {
            return redirect()->route('super_admin.users.index')
                ->with('error', 'Tidak dapat mengubah data akun Super Admin lain.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->division_id = $request->role === 'staff' ? $request->division_id : null;
        $user->role = $request->role;
        $user->admin_level = $request->role === 'admin' ? $request->admin_level : null;

        $user->save();

        return redirect()->route('super_admin.users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function resetPassword(User $user)
    {
        // Jangan biarkan admin me-reset password dirinya sendiri dari sini
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Silakan ubah kata sandi Anda sendiri melalui menu Profil.');
        }

        $defaultPassword = self::DEFAULT_PASSWORD;
        
        $user->password             = Hash::make($defaultPassword);
        $user->must_change_password = true; // Wajib ganti password setelah di-reset
        $user->save();

        return back()->with('success', "Password berhasil di-reset ke default: {$defaultPassword}");
    }
}
