<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::query();

        if ($request->filled('search')) {
            $query->where('username', 'like', '%' . $request->search . '%');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');
        $allowedSorts = ['id', 'username', 'name', 'email', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'id';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'asc';

        $admins = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());

        return view('admin.account.index', compact('admins', 'sortBy', 'sortDir'));
    }


    public function create()
    {
        return view('admin.account.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required|min:6',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Cek apakah username sudah ada
        if (Admin::where('username', $data['username'])->exists()) {
            return redirect()->back()->withErrors(['username' => 'Username already exists.'])->withInput();
        }

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Simpan foto jika ada
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('assets/profile', 'public_html_storage'); // simpan ke storage/app/public/profile
            $data['photo'] = Storage::url($path); // hasilnya: /storage/profile/namafile.jpg
        }

        // Simpan data admin ke database
        Admin::create($data);

        return redirect()->route('account.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.account.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $data = $request->validate([
            'username' => 'required|unique:admins,username,' . $id,
            'password' => 'nullable|min:8',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            // hapus foto lama jika ada
            if ($admin->photo) {
                $oldPath = str_replace('/storage', 'public', $admin->photo);
                Storage::delete($oldPath);
            }

            $path = $request->file('photo')->store('assets/profile', 'public_html_storage'); // simpan ke storage/app/public/assets/profile
            $data['photo'] = Storage::url($path);
        }

        $admin->update($data);

        return redirect()->route('account.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
         if (auth()->id() == $admin->id) {
        return redirect()->route('account.index')->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
         }
        $admin->delete();

        return redirect()->route('account.index')->with('success', 'Data admin berhasil dihapus.');
    }


}
