<?php

namespace App\Http\Controllers;

use App\Models\Super_admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jurusan;
use App\Models\Admin;
use App\Models\User;
use App\Models\Calon_mahasiswa;
use App\Models\Assessor;
use App\Models\Cpmk;
use App\Models\Matkul;
use App\Models\Assessment;
use App\Models\CpmkAssessment;
use App\Models\Matkul_score;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Periode;


class SuperAdminController extends Controller
{
    /**
     * Constructor - menerapkan middleware untuk autentikasi dan otorisasi
     */
    public function __construct()
    {
        $this->middleware(['web', 'auth', 'super_admin']);
    }

    /**
     * Validasi apakah user yang login adalah super admin
     * Mengatur regenerasi session untuk keamanan
     */
    private function checkSuperAdmin()
    {
        if (!auth()->check()) {
            \Log::warning('Unauthorized access attempt - not authenticated', [
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);
            return redirect()->route('login')->with('error', 'Please login first');
        }

        if (auth()->user()->role !== 'super_admin') {
            \Log::warning('Unauthorized access attempt - wrong role', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user() ? auth()->user()->role : 'not authenticated',
                'session_id' => session()->getId()
            ]);
            return redirect()->route('login')->with('error', 'Unauthorized access');
        }

        // Regenerasi session ID setiap 30 menit untuk keamanan
        if (!session()->has('last_session_regeneration') || 
            now()->diffInMinutes(session('last_session_regeneration')) > 30) {
            session()->regenerate();
            session()->put('last_session_regeneration', now());
        }

        return true;
    }

    /**
     * Menampilkan halaman dashboard super admin
     */
    public function dashboard()
    {
        \Log::info('Accessing super admin dashboard', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        return view('Super_admin.dashboard');
    }

    /**
     * Menampilkan halaman profil super admin
     */
    public function profileView()
    {
        \Log::info('Accessing super admin profile view', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        $user = Auth::user();
        $admin = $user->admin ?? new Admin(['user_id' => $user->id]);
        return view('Super_admin.profile-admin', compact('admin','user'));
    }

    /**
     * Menampilkan form edit profil super admin
     */
    public function profile_edit_admin_view($id)
    {
        $admin = Admin::where('user_id', Auth::id())->findOrFail($id);
        return view('Super_admin.profile-edit-admin', compact('admin'));
    }

    /**
     * Memproses update data profil super admin
     * Menangani upload foto profil jika ada
     */
    public function profile_edit_admin(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $admin = Admin::where('user_id', Auth::id())->findOrFail($id);

        // Proses upload foto jika ada file yang diupload
        if ($request->hasFile('foto')) {
            $imageName = time().'.'.$request->foto->extension();  
            $request->foto->move(public_path('Data/profile_pict_admin'), $imageName);
            
            // Hapus foto lama jika ada
            if ($admin->foto) {
                $oldImagePath = public_path('Data/profile_pict_admin/' . $admin->foto);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
    
            $admin->foto = $imageName; 
            $admin->save();
        }

        $admin->update($request->except('foto'));

        return redirect()->route('super.profile-view');
    }

    /**
     * Menampilkan tabel data assessor dengan filter jurusan
     */
    public function account_assessor_table(Request $request)
    {
        \Log::info('Accessing account_assessor_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        $users_camaba = [];
        
        $query = User::where('role', 'assessor');
        
        // Terapkan filter jurusan jika dipilih
        if ($jurusan_id) {
            $query->whereHas('assessor', function($query) use ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            });
        }
        
        $users_assessor = $query->get();
        
        return view('Super_admin.account-assessor-table', compact('users_assessor','jurusans','jurusan_id'));
    }

    /**
     * Menampilkan form tambah akun assessor
     */
    public function account_assessor_add()
    {
        \Log::info('Accessing account_assessor_add', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        $jurusan = Jurusan::select('id','nama_jurusan')->get();
        return view('Super_admin.account-assessor-add', compact('jurusan'));
    }

    /**
     * Memproses pembuatan akun assessor baru
     * Membuat user dan data assessor secara bersamaan
     */
    public function account_assessor_add_data(Request $request)
    {
        \Log::info('Attempting to add assessor', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_data' => $request->except(['password'])
        ]);
        $this->checkSuperAdmin();
        $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id',
        ]);

        try {
            // Buat user baru dengan role assessor
            $user = User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'assessor'
            ]);

            // Buat data assessor yang terkait dengan user
            Assessor::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'jurusan_id' => $request->jurusan_id,
            ]);

            \Log::info('Assessor created successfully', [
                'user_id' => $user->id,
                'assessor_name' => $request->nama
            ]);

            return redirect()->route('super.account-assessor-table')->with('success', 'Assessor created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating assessor', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create assessor: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan tabel data user/pendaftar dengan filter jurusan
     * Hanya menampilkan data dari periode yang aktif
     */
    public function account_user_table(Request $request)
    {
        \Log::info('Accessing account_user_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        $active_periodes = Periode::where('is_active', true)->get();
        $users_camaba = [];
        
        if ($active_periodes->isNotEmpty()) {
            $query = User::where('role', 'pendaftar')
                ->whereHas('calon_mahasiswa', function($query) use ($active_periodes) {
                    $query->whereIn('periode_id', $active_periodes->pluck('id'));
                });

            // Terapkan filter jurusan jika dipilih
            if ($jurusan_id) {
                $query->whereHas('calon_mahasiswa', function($query) use ($jurusan_id) {
                    $query->where('jurusan_id', $jurusan_id);
                });
            }

            $users_camaba = $query->get();
        }
        
        return view('Super_admin.account-user-table', compact('users_camaba','jurusans','jurusan_id'));
    }

    /**
     * Menampilkan form tambah akun user/pendaftar
     */
    public function account_user_add()
    {
        \Log::info('Accessing account_user_add', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        $jurusan = Jurusan::all();
        $periodes = Periode::all();
        return view('Super_admin.account-user-add', compact('jurusan', 'periodes'));
    }

    /**
     * Memproses pembuatan akun user/pendaftar baru
     * Membuat user dan data calon mahasiswa secara bersamaan
     */
    public function account_user_add_data(Request $request)
    {
        \Log::info('Attempting to add user', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_data' => $request->except(['password'])
        ]);
        $this->checkSuperAdmin();
        try {
            $request->validate([
                'email' => 'required|email|max:255|unique:users',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6',
                'nama' => 'required|string|max:255',
                'jurusan_id' => 'required|exists:jurusan,id',
                'periode_id' => 'required|exists:periode,id',
            ]);

            // Buat user baru dengan role pendaftar
            $user = User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'pendaftar'
            ]);

            // Buat data calon mahasiswa yang terkait dengan user
            Calon_mahasiswa::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'jurusan_id' => $request->jurusan_id,
                'jenis_mahasiswa' => 'camaba_alihjenjang',
                'periode_id' => $request->periode_id,
            ]);

            \Log::info('User created successfully', [
                'user_id' => $user->id,
                'user_name' => $request->nama
            ]);

            return redirect()->route('super.account-user-table')->with('success', 'Pendaftar created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan tabel data admin dengan filter jurusan
     */
    public function account_admin_table(Request $request)
    {
        \Log::info('Accessing account_admin_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        $users_admin = [];
        
        $query = User::where('role', 'admin');
        
        // Terapkan filter jurusan jika dipilih
        if ($jurusan_id) {
            $query->whereHas('admin', function($query) use ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            });
        }
        
        $users_admin = $query->get();
        
        return view('Super_admin.account-admin-table', compact('users_admin','jurusans','jurusan_id'));
    }

    /**
     * Menampilkan form tambah akun admin
     */
    public function account_admin_add()
    {
        \Log::info('Accessing account_admin_add', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        $jurusan = Jurusan::all();
        return view('Super_admin.account-admin-add', compact('jurusan'));
    }

    /**
     * Memproses pembuatan akun admin baru
     * Membuat user dan data admin secara bersamaan
     */
    public function account_admin_add_data(Request $request)
    {
        \Log::info('Attempting to add admin', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_data' => $request->except(['password'])
        ]);
        $this->checkSuperAdmin();
        $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id',
        ]);

        try {
            // Buat user baru dengan role admin
            $user = User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'admin'
            ]);

            // Buat data admin yang terkait dengan user
            Admin::create([
                'user_id' => $user->id,
                'nama' => $request->nama,
                'jurusan_id' => $request->jurusan_id,
            ]);

            \Log::info('Admin created successfully', [
                'user_id' => $user->id,
                'admin_name' => $request->nama
            ]);

            return redirect()->route('super.account-admin-table')->with('success', 'Admin created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating admin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman kelola assessor
     */
    public function kelola_assessor_table()
    {
        \Log::info('Accessing kelola_assessor_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        $this->checkSuperAdmin();
        return view('Super_admin.kelola-assessor-table');
    }

    /**
     * Menampilkan halaman kelola assessor untuk mahasiswa
     * Menampilkan data mahasiswa dan assessor berdasarkan jurusan yang dipilih
     */
    public function kelola_assessor_mahasiswa(Request $request)
    {
        \Log::info('Accessing kelola_assessor_mahasiswa', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        $calon_mahasiswa = [];
        $active_periodes = Periode::where('is_active', true)->get();

        if ($active_periodes->isNotEmpty()) {
            // Ambil mahasiswa dari jurusan yang dipilih dan periode aktif
            $query = Calon_mahasiswa::with([
                    'jurusan', 
                    'periode',
                    'assessment.assessor1', 
                    'assessment.assessor2', 
                    'assessment.assessor3'
                ])
                ->whereIn('periode_id', $active_periodes->pluck('id'));

            // Apply jurusan filter if selected
            if ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            }

            $calon_mahasiswa = $query->get();

            // Ambil assessor dari jurusan yang dipilih atau semua assessor
            if ($jurusan_id) {
                $assessor = Assessor::where('jurusan_id', $jurusan_id)->get();
            } else {
                $assessor = Assessor::all();
            }
        } else {
            $assessor = collect();
        }

        return view('Super_admin.kelola-assessor-mahasiswa', compact('calon_mahasiswa', 'assessor', 'jurusans', 'jurusan_id'));
    }

    /**
     * Memproses penambahan assessor untuk mahasiswa
     * Validasi assessor harus dari jurusan yang sama dan tidak boleh duplikat
     */
    public function kelola_assessor_mahasiswa_add(Request $request)
    {
        \Log::info('Attempting to add assessor to mahasiswa', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_data' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $validated = $request->validate([
            'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
            'assessor1_id' => 'required|exists:assessor,id',
            'assessor2_id' => 'nullable|exists:assessor,id',
            'assessor3_id' => 'nullable|exists:assessor,id',
            'deadline' => 'nullable|date|after:now',
        ]);

        try {
            $mahasiswa = Calon_mahasiswa::find($validated['calon_mahasiswa_id']);
            $jurusan_id = $mahasiswa->jurusan_id;

            // Cek apakah assessor yang ditugaskan berasal dari jurusan yang sama
            $assessorIds = [
                $validated['assessor1_id'],
                $validated['assessor2_id'],
                $validated['assessor3_id'],
            ];

            foreach ($assessorIds as $assessorId) {
                if ($assessorId) { // Hanya cek jika assessor ditugaskan
                    $assessor = Assessor::find($assessorId);
                    if ($assessor->jurusan_id !== $jurusan_id) {
                        return back()->withErrors(['message' => 'Assessor harus berasal dari jurusan yang sama dengan mahasiswa.']);
                    }
                }
            }

            // Cek assessor duplikat, abaikan nilai null
            $assignedAssessorIds = array_filter($assessorIds); // Hapus nilai null
            if (count($assignedAssessorIds) !== count(array_unique($assignedAssessorIds))) {
                return back()->withErrors(['message' => 'Assessor tidak boleh sama.']);
            }

            // Gunakan updateOrCreate untuk menyimpan atau update record Assessment
            $assessment = Assessment::updateOrCreate(
                ['calon_mahasiswa_id' => $validated['calon_mahasiswa_id']],
                [
                    'jurusan_id' => $jurusan_id,
                    'assessor_id_1' => $validated['assessor1_id'],
                    'assessor_id_2' => $validated['assessor2_id'],
                    'assessor_id_3' => $validated['assessor3_id'],
                    'deadline' => $validated['deadline'] ? \Carbon\Carbon::parse($validated['deadline']) : null,
                ]
            );

            // Update tabel matkul_assessments untuk semua matkul jurusan mahasiswa ini
            $matkuls = Matkul::where('jurusan_id', $jurusan_id)->get();

            foreach ($matkuls as $matkul) {
                // Cari atau buat record MatkulAssessment
                $matkulAssessment = \App\Models\MatkulAssessment::firstOrNew([
                    'calon_mahasiswa_id' => $validated['calon_mahasiswa_id'],
                    'matkul_id' => $matkul->id,
                ]);

                // Jika record baru, set nilai awal untuk field assessment agar tidak error NOT NULL
                if (!$matkulAssessment->exists) {
                    $matkulAssessment->self_assessment_value = ''; // Atau default yang sesuai dengan schema
                    $matkulAssessment->assessor1_assessment = ''; // Atau default yang sesuai
                    $matkulAssessment->assessor2_assessment = ''; // Atau default yang sesuai
                    $matkulAssessment->assessor3_assessment = ''; // Atau default yang sesuai
                }

                // Update ID assessor secara eksplisit
                $matkulAssessment->assessor1_id = $validated['assessor1_id'];
                $matkulAssessment->assessor2_id = $validated['assessor2_id'];
                $matkulAssessment->assessor3_id = $validated['assessor3_id'];

                // Simpan record MatkulAssessment. Ini akan membuat record baru jika belum ada atau update jika sudah ada
                $matkulAssessment->save();
            }

            \Log::info('Assessment created successfully', [
                'calon_mahasiswa_id' => $validated['calon_mahasiswa_id'],
                'jurusan_id' => $jurusan_id,
                'deadline' => $validated['deadline'] ?? 'not set'
            ]);

            return redirect()->route('super.kelola-assessor-mahasiswa')->with('success', 'Assessment berhasil dibuat!');
        } catch (\Exception $e) {
            \Log::error('Error creating assessment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create assessment: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan tabel mata kuliah dengan filter jurusan
     */
    public function kelola_matkul_table(Request $request)
    {
        \Log::info('Accessing kelola_matkul_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_params' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        $matkuls = [];
        
        if ($jurusan_id) {
            $matkuls = Matkul::where('jurusan_id', $jurusan_id)->get();
        } else {
            // Tampilkan semua mata kuliah ketika "Semua Jurusan" dipilih
            $matkuls = Matkul::all();
        }
        
        return view('Super_admin.kelola-matkul-table', compact('matkuls','jurusans','jurusan_id'));
    }

    /**
     * Memproses penambahan mata kuliah baru
     * Melakukan validasi, translasi nama mata kuliah, dan generate sinonim
     */
     public function kelola_matkul_add_data(Request $request)
    {
        try {
            // 1. Validasi Input dari Form Super Admin
            $validator = \Validator::make($request->all(), [
                'jurusan_id' => 'required|exists:jurusan,id', // Wajib untuk Super Admin
                'nama_matkul' => [
                    'required',
                    'string',
                    'max:255',
                    // Rule untuk memastikan nama_matkul unik per jurusan yang dipilih
                    function ($attribute, $value, $fail) use ($request) {
                        if (Matkul::where('jurusan_id', $request->jurusan_id)->where('nama_matkul', $value)->exists()) {
                            $fail('Mata kuliah dengan nama ini sudah ada di jurusan yang dipilih.');
                        }
                    }
                ],
                'kode_matkul' => [
                    'nullable',
                    'string',
                    'max:20',
                    // Rule untuk memastikan kode_matkul unik per jurusan (jika diisi)
                    function ($attribute, $value, $fail) use ($request) {
                        if ($value && Matkul::where('jurusan_id', $request->jurusan_id)->where('kode_matkul', $value)->exists()) {
                            $fail('Mata kuliah dengan kode ini sudah ada di jurusan yang dipilih.');
                        }
                    }
                ],
                'sks' => 'nullable|integer|min:1|max:6',
            ]);
    
            // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan error
            if ($validator->fails()) {
                return redirect()->route('super.kelola-matkul-table') // Arahkan ke route super admin
                    ->withErrors($validator)
                    ->withInput();
            }
    
            // 2. Buat dan Simpan Data Mata Kuliah
            Matkul::create([
                'nama_matkul' => $request->nama_matkul,
                'kode_matkul' => $request->kode_matkul,
                'sks'         => $request->sks,
                'jurusan_id'  => $request->jurusan_id,
            ]);
    
            // 3. Kembalikan ke halaman tabel mata kuliah dengan pesan sukses
            return redirect()->route('super.kelola-matkul-table')
                ->with('success', 'Mata Kuliah berhasil ditambahkan.');
    
        } catch (\Exception $e) {
            // Tangani jika ada error tak terduga
            \Log::error('Error in kelola_matkul_add_data (Super Admin): ' . $e->getMessage());
            return redirect()->route('super.kelola-matkul-table')
                ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah. Silakan coba lagi.');
        }
    }
     
    // public function kelola_matkul_add_data(Request $request)
    // {
    //     try {
    //         \Log::info('Validating mata kuliah data', [
    //             'nama_matkul' => $request->nama_matkul,
    //             'kode_matkul' => $request->kode_matkul,
    //             'jurusan_id' => $request->jurusan_id
    //         ]);

    //         $validator = \Validator::make($request->all(), [
    //             'nama_matkul' => [
    //                 'required',
    //                 'string',
    //                 'max:255',
    //                 function ($attribute, $value, $fail) use ($request) {
    //                     $exists = Matkul::where('jurusan_id', $request->jurusan_id)
    //                         ->where('nama_matkul', $value)
    //                         ->exists();
    //                     \Log::info('Checking nama_matkul uniqueness', [
    //                         'value' => $value,
    //                         'exists' => $exists
    //                     ]);
    //                     if ($exists) {
    //                         $fail('Mata kuliah dengan nama ini sudah ada di jurusan ini.');
    //                     }
    //                 }
    //             ],
    //             'jurusan_id' => 'required|exists:jurusan,id',
    //             'kode_matkul' => [
    //                 'nullable',
    //                 'string',
    //                 'max:20',
    //                 function ($attribute, $value, $fail) use ($request) {
    //                     if ($value) {
    //                         $exists = Matkul::where('jurusan_id', $request->jurusan_id)
    //                             ->where('kode_matkul', $value)
    //                             ->exists();
    //                         \Log::info('Checking kode_matkul uniqueness', [
    //                             'value' => $value,
    //                             'exists' => $exists
    //                         ]);
    //                         if ($exists) {
    //                             $fail('Mata kuliah dengan kode ini sudah ada di jurusan ini.');
    //                         }
    //                     }
    //                 }
    //             ],
    //             'sks' => 'nullable|integer|min:1|max:6',
    //         ]);

    //         if ($validator->fails()) {
    //             \Log::info('Validation failed', [
    //                 'errors' => $validator->errors()->toArray()
    //             ]);
    //             return redirect()->route('super.kelola-matkul-table')
    //                 ->withErrors($validator)
    //                 ->withInput();
    //         }

    //         $matkulName = $request->nama_matkul;
    //         $translatedName = $matkulName;

    //         // Coba translasi menggunakan Google Translate
    //         try {
    //             $response = Http::withoutVerifying()->get('https://translate.googleapis.com/translate_a/single', [
    //                 'client' => 'gtx',
    //                 'sl' => 'auto',
    //                 'tl' => 'en',
    //                 'dt' => 't',
    //                 'q' => $matkulName
    //             ]);

    //             if ($response->successful()) {
    //                 $result = $response->json();
    //                 if (!empty($result[0][0][0])) {
    //                     $translatedName = $result[0][0][0];
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             \Log::error('Google Translate error: ' . $e->getMessage());
                
    //             // Fallback ke LibreTranslate jika Google Translate gagal
    //             try {
    //                 $response = Http::withoutVerifying()->post('https://libretranslate.de/translate', [
    //                     'q' => $matkulName,
    //                     'source' => 'id',
    //                     'target' => 'en'
    //                 ]);

    //                 if ($response->successful()) {
    //                     $result = $response->json();
    //                     $translatedName = $result['translatedText'];
    //                 }
    //             } catch (\Exception $e) {
    //                 \Log::error('Fallback translation error: ' . $e->getMessage());
    //             }
    //         }

    //         $synonyms = [$translatedName];
            
    //         // Coba ambil sinonim dari WordNet service
    //         try {
    //             $response = Http::get('http://localhost:5000/synonyms', [
    //                 'word' => strtolower($translatedName)
    //             ]);

    //             if ($response->successful()) {
    //                 $wordnetSynonyms = $response->json();
    //                 if (!empty($wordnetSynonyms)) {
    //                     $synonyms = array_merge($synonyms, $wordnetSynonyms);
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             \Log::error('WordNet service error: ' . $e->getMessage());
                
    //             // Gunakan sinonim umum jika WordNet tidak tersedia
    //             $commonSynonyms = [
    //                 'programming' => ['coding', 'software development', 'computer programming'],
    //                 'database' => ['db', 'data management', 'data storage', 'dbms'],
    //                 'network' => ['networking', 'computer network', 'data communication'],
    //                 'algorithm' => ['algorithmic', 'computational method', 'problem solving'],
    //                 'security' => ['cybersecurity', 'information security', 'computer security'],
    //                 'system' => ['information system', 'computing system', 'it system'],
    //                 'analysis' => ['analytics', 'data analysis', 'system analysis'],
    //                 'design' => ['system design', 'software design', 'application design'],
    //                 'web' => ['website', 'web application', 'web development'],
    //                 'mobile' => ['mobile application', 'mobile development', 'app development'],
    //                 'artificial intelligence' => ['ai', 'machine learning', 'deep learning'],
    //                 'operating system' => ['os', 'system software', 'platform'],
    //                 'discrete' => ['discrete mathematics', 'discrete structure', 'finite mathematics'],
    //                 'calculus' => ['mathematical analysis', 'integral calculus', 'differential calculus'],
    //                 'statistics' => ['statistical analysis', 'data statistics', 'probability'],
    //             ];

    //             foreach ($commonSynonyms as $key => $values) {
    //                 if (stripos($translatedName, $key) !== false) {
    //                     $synonyms = array_merge($synonyms, $values);
    //                 }
    //             }
    //         }

    //         // Tambahkan nama asli dan hasil translasi ke daftar sinonim
    //         $synonyms[] = $matkulName;
    //         $synonyms[] = $translatedName;
    //         $synonyms = array_unique(array_filter($synonyms));

    //         // Buat record mata kuliah baru
    //         $matkul = Matkul::create([
    //             'nama_matkul' => $matkulName,
    //             'jurusan_id' => $request->jurusan_id,
    //             'sinonim' => json_encode($synonyms),
    //             'kode_matkul' => $request->kode_matkul,
    //             'sks' => $request->sks
    //         ]);

    //         return redirect()->route('super.kelola-matkul-table')
    //             ->with('success', 'Mata Kuliah berhasil ditambahkan dengan ' . count($synonyms) . ' sinonim!');

    //     } catch (\Exception $e) {
    //         \Log::error('Error in kelola_matkul_add_data: ' . $e->getMessage());
    //         return redirect()->route('super.kelola-matkul-table')
    //             ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah. Silakan coba lagi.');
    //     }
    // }

    /**
     * Memproses edit data mata kuliah
     * Melakukan validasi, translasi, dan update sinonim
     */
    public function edit_matkul(Request $request, Matkul $matkul)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'nama_matkul' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($matkul) {
                    if (Matkul::where('jurusan_id', $matkul->jurusan_id)
                        ->where('nama_matkul', $value)
                        ->where('id', '!=', $matkul->id)
                        ->exists()) {
                        $fail('Mata kuliah dengan nama ini sudah ada di jurusan ini.');
                    }
                }
            ],
            'kode_matkul' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($matkul) {
                    if ($value && Matkul::where('jurusan_id', $matkul->jurusan_id)
                        ->where('kode_matkul', $value)
                        ->where('id', '!=', $matkul->id)
                        ->exists()) {
                        $fail('Mata kuliah dengan kode ini sudah ada di jurusan ini.');
                    }
                }
            ],
            'sks' => 'nullable|integer|min:1|max:6',
        ]);

        try {
            $matkulName = $request->nama_matkul;
            $translatedName = $matkulName;

            // Coba translasi menggunakan Google Translate
            try {
                $response = Http::withoutVerifying()->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => 'auto',
                    'tl' => 'en',
                    'dt' => 't',
                    'q' => $matkulName
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    if (!empty($result[0][0][0])) {
                        $translatedName = $result[0][0][0];
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Google Translate error: ' . $e->getMessage());
                
                // Fallback ke LibreTranslate jika Google Translate gagal
                try {
                    $response = Http::withoutVerifying()->post('https://libretranslate.de/translate', [
                        'q' => $matkulName,
                        'source' => 'id',
                        'target' => 'en'
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        $translatedName = $result['translatedText'];
                    }
                } catch (\Exception $e) {
                    \Log::error('Fallback translation error: ' . $e->getMessage());
                }
            }

            $synonyms = [$translatedName];
            
            // Coba ambil sinonim dari WordNet service
            try {
                $response = Http::get('http://localhost:5000/synonyms', [
                    'word' => strtolower($translatedName)
                ]);

                if ($response->successful()) {
                    $wordnetSynonyms = $response->json();
                    if (!empty($wordnetSynonyms)) {
                        $synonyms = array_merge($synonyms, $wordnetSynonyms);
                    }
                } else {
                     \Log::warning('WordNet service returned non-successful status: ' . $response->status());
                }
            } catch (\Exception $e) {
                \Log::error('WordNet service error: ' . $e->getMessage());
                
                // Gunakan sinonim umum jika WordNet tidak tersedia
                $commonSynonyms = [
                    'programming' => ['coding', 'software development', 'computer programming'],
                    'database' => ['db', 'data management', 'data storage', 'dbms'],
                    'network' => ['networking', 'computer network', 'data communication'],
                    'algorithm' => ['algorithmic', 'computational method', 'problem solving'],
                    'security' => ['cybersecurity', 'information security', 'computer security'],
                    'system' => ['information system', 'computing system', 'it system'],
                    'analysis' => ['analytics', 'data analysis', 'system analysis'],
                    'design' => ['system design', 'software design', 'application design'],
                    'web' => ['website', 'web application', 'web development'],
                    'mobile' => ['mobile application', 'mobile development', 'app development'],
                    'artificial intelligence' => ['ai', 'machine learning', 'deep learning'],
                    'operating system' => ['os', 'system software', 'platform'],
                    'discrete' => ['discrete mathematics', 'discrete structure', 'finite mathematics'],
                    'calculus' => ['mathematical analysis', 'integral calculus', 'differential calculus'],
                    'statistics' => ['statistical analysis', 'data statistics', 'probability'],
                ];

                foreach ($commonSynonyms as $key => $values) {
                    if (stripos($translatedName, $key) !== false) {
                        $synonyms = array_merge($synonyms, $values);
                    }
                }
            }

            // Tambahkan nama asli dan hasil translasi ke daftar sinonim
            $synonyms[] = $matkulName;
            $synonyms[] = $translatedName;
            $synonyms = array_unique(array_filter($synonyms));

            // Update data mata kuliah
            $matkul->update([
                'nama_matkul' => $matkulName,
                'kode_matkul' => $request->kode_matkul,
                'sks' => $request->sks,
                'sinonim' => json_encode($synonyms)
            ]);

            return redirect()->route('super.kelola-matkul-table')
                ->with('success', 'Mata Kuliah berhasil diperbarui dengan ' . count($synonyms) . ' sinonim!');

        } catch (\Exception $e) {
            \Log::error('Error in edit_matkul: ' . $e->getMessage());
            return redirect()->route('super.kelola-matkul-table')
                ->with('error', 'Terjadi kesalahan saat memperbarui mata kuliah. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan tabel CPMK untuk mata kuliah tertentu
     */
    public function kelola_cpmk_table($matkul_id)
    {
        \Log::info('Accessing kelola_cpmk_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'matkul_id' => $matkul_id
        ]);
        $this->checkSuperAdmin();
        $matkul = Matkul::findOrFail($matkul_id);
        $cpmks = Cpmk::where('matkul_id',$matkul_id)->get();
        return view('Super_admin.kelola-cpmk-table', compact('matkul','cpmks'));
    }

    /**
     * Menampilkan form tambah CPMK untuk mata kuliah tertentu
     */
    public function create_data_cpmk($matkul_id)
    {
        \Log::info('Accessing create_data_cpmk', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'matkul_id' => $matkul_id
        ]);
        $this->checkSuperAdmin();
        $matkul = Matkul::findOrFail($matkul_id);
        return view('Super_admin.kelola-cpmk-table', compact('matkul'));
    }

    /**
     * Memproses penambahan CPMK baru
     */
    public function add_data_cpmk(Request $request)
    {
        \Log::info('Attempting to add CPMK', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'request_data' => $request->all()
        ]);
        $this->checkSuperAdmin();
        $request->validate([
            'penjelasan' => 'required|string',
            'matkul_id' => 'required|exists:matkul,id',
        ]);

        try {
            Cpmk::create([
                'penjelasan' => $request->penjelasan,
                'kode_cpmk' => $request->kode_cpmk,
                'matkul_id' => $request->matkul_id,
            ]);

            \Log::info('CPMK created successfully', [
                'matkul_id' => $request->matkul_id
            ]);

            return redirect()->route('super.kelola-cpmk-table', $request->matkul_id)
                ->with('success', 'CPMK created successfully!');
        } catch (\Exception $e) {
            \Log::error('Error creating CPMK', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to create CPMK: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data CPMK
     */
    public function delete_cpmk(Cpmk $cpmk)
    {
        \Log::info('Attempting to delete CPMK', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'cpmk_id' => $cpmk->id
        ]);
        $this->checkSuperAdmin();
        try {
            $cpmk->delete();
            \Log::info('CPMK deleted successfully', [
                'cpmk_id' => $cpmk->id
            ]);
            return back()->with('success', 'CPMK deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting CPMK', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete CPMK: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data mata kuliah
     */
    public function delete_matkul(Matkul $matkul)
    {
        \Log::info('Attempting to delete matkul', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'matkul_id' => $matkul->id
        ]);
        $this->checkSuperAdmin();
        try {
            $matkul->delete();
            \Log::info('Matkul deleted successfully', [
                'matkul_id' => $matkul->id
            ]);
            return back()->with('success', 'Matkul deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting matkul', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete matkul: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data user
     */
    public function delete_user(User $user)
    {
        \Log::info('Attempting to delete user', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'target_user_id' => $user->id
        ]);
        $this->checkSuperAdmin();
        try {
            $user->delete();
            \Log::info('User deleted successfully', [
                'target_user_id' => $user->id
            ]);
            return back()->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan tabel data user/pendaftar dengan filter jurusan
     * Hanya menampilkan data dari periode yang aktif
     */
    public function data_user_table(Request $request)
    {
        \Log::info('Accessing data_user_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        
        $this->checkSuperAdmin();
        
        $active_periodes = Periode::where('is_active', true)->get();
        $users_camaba = collect();
        $jurusans = Jurusan::all(); // Ambil semua data jurusan
        $jurusan_id = $request->jurusan_id; // Ambil jurusan_id yang dipilih dari request

        if ($active_periodes->isNotEmpty()) {
            $query = User::where('role', 'pendaftar')
                ->with(['calon_mahasiswa' => function($query) use ($active_periodes) {
                    $query->whereIn('periode_id', $active_periodes->pluck('id'))
                        ->with(['jurusan', 'periode']);
                }])
                ->whereHas('calon_mahasiswa', function($query) use ($active_periodes) {
                    $query->whereIn('periode_id', $active_periodes->pluck('id'));
                });

            // Terapkan filter jurusan jika dipilih
            if ($jurusan_id) {
                $query->whereHas('calon_mahasiswa', function($query) use ($jurusan_id) {
                    $query->where('jurusan_id', $jurusan_id);
                });
            }

            $users_camaba = $query->get();
        }

        return view('Super_admin.data-user-table', compact('users_camaba', 'jurusans', 'jurusan_id'));
    }

    /**
     * Menampilkan tabel data assessor dengan filter jurusan
     */
    public function data_assessor_table(Request $request)
    {
        \Log::info('Accessing data_assessor_table', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role
        ]);
        
        $this->checkSuperAdmin();
        
        $assessor = Assessor::all();
        $users_assessor = User::where('role','assessor');

        // Terapkan filter jurusan jika dipilih
        if ($request->jurusan_id) {
            $users_assessor->whereHas('assessor', function($query) use ($request) {
                $query->where('jurusan_id', $request->jurusan_id);
            });
        }

        $users_assessor = $users_assessor->get();
        $jurusans = Jurusan::all(); // Ambil semua data jurusan

        return view('Super_admin.data-assessor-table', compact('users_assessor', 'assessor', 'jurusans'));
    }

    /**
     * Menampilkan daftar mahasiswa yang ditugaskan ke assessor tertentu
     */
    public function view_assessor_student($assessor_id)
    {
        \Log::info('Accessing view_assessor_student', [
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role,
            'assessor_id' => $assessor_id
        ]);
        $this->checkSuperAdmin();

        try {
            // Ambil data assessor
            $assessor = Assessor::findOrFail($assessor_id);

            // Ambil semua mahasiswa yang ditugaskan ke assessor ini
            $assignedStudents = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip', 'bukti_alih_jenjang'])
                ->whereHas('assessment', function($query) use ($assessor_id) {
                    $query->where('assessor_id_1', $assessor_id)
                          ->orWhere('assessor_id_2', $assessor_id)
                          ->orWhere('assessor_id_3', $assessor_id);
                })
                ->get();

            return view('Super_admin.view-assessor-students', compact('assessor', 'assignedStudents'));
        } catch (\Exception $e) {
            \Log::error('Error in view_assessor_student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail mahasiswa dari perspektif assessor
     * Menampilkan data penilaian dan status mata kuliah
     */
    public function view_student_as_assessor($assessor_id, $student_id)
    {
        $this->checkSuperAdmin();

        try {
            // Verifikasi data dasar
            $assessor = Assessor::findOrFail($assessor_id);
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip', 'bukti_alih_jenjang', 'assessment'])->findOrFail($student_id);
            $assessment = $camaba->assessment;

            // Ambil semua mata kuliah jurusan
            $allMatkulJurusan = Matkul::with('cpmk')->where('jurusan_id', $camaba->jurusan_id)->get();

            // Ambil data penilaian per CPMK (Sistem Baru) untuk tab "Self Assessment"
            $cpmkAssessments = CpmkAssessment::where('calon_mahasiswa_id', $student_id)
                ->with(['matkul', 'cpmk'])
                ->get()
                ->groupBy('matkul.nama_matkul');

            // Ambil data penilaian dari sistem lama (untuk tab "Konversi Nilai" jika masih dipakai)
            $matkulAssessmentsLama = \App\Models\MatkulAssessment::where('calon_mahasiswa_id', $student_id)->get();

            // Ambil SEMUA skor dari SEMUA asesor
            $allAssessorScores = Matkul_score::where('calon_mahasiswa_id', $student_id)->with('assessor')->get()->groupBy('matkul_id');
            
            // Ambil semua asesor yang ditugaskan
            $assignedAssessors = collect();
            if ($assessment) {
                $assessorIds = array_filter([$assessment->assessor_id_1, $assessment->assessor_id_2, $assessment->assessor_id_3]);
                $assignedAssessors = Assessor::whereIn('id', $assessorIds)->get()->keyBy('id');
            }

            // Siapkan data status untuk tab "Konversi Nilai"
            $matkulWithStatus = $allMatkulJurusan->map(function($matkul) use ($allAssessorScores, $assignedAssessors) {
                $scoresForThisMatkul = $allAssessorScores->get($matkul->id);
                if ($scoresForThisMatkul && $scoresForThisMatkul->isNotEmpty()) {
                    $firstScore = $scoresForThisMatkul->first();
                    $matkul->isLolos = ($firstScore->status === 'Lolos');
                    $matkul->isComplete = true;
                    $matkul->finalScore = $firstScore->nilai_akhir;
                    $matkul->completedAssessmentsCount = $scoresForThisMatkul->count();
                } else {
                    $matkul->isLolos = false;
                    $matkul->isComplete = false;
                    $matkul->finalScore = null;
                    $matkul->completedAssessmentsCount = 0;
                }
                $matkul->requiredAssessments = $assignedAssessors->count();
                return $matkul;
            });

            return view('Super_admin.view-student-as-assessor', compact(
                'camaba', 
                'assessor', 
                'matkulWithStatus', 
                'allMatkulJurusan',
                'allAssessorScores',
                'assignedAssessors',
                'cpmkAssessments',
                'matkulAssessments' // Ini adalah variabel yang menyebabkan error, sekarang diisi dengan data yang benar
            ));

        } catch (\Exception $e) {
            \Log::error('Error in view_student_as_assessor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Menampilkan tabel data super admin
     */
    public function account_super_admin_table()
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin accessed account_super_admin_table', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role
            ]);

            $users_super_admin = User::where('role', 'super_admin')
                ->with('admin')
                ->get();

            return view('Super_admin.account-super-admin-table', compact('users_super_admin'));
        } catch (\Exception $e) {
            Log::error('Error in account_super_admin_table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengakses halaman.');
        }
    }

    /**
     * Menampilkan form tambah akun super admin
     */
    public function account_super_admin_add()
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin accessed account_super_admin_add', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role
            ]);

            return view('Super_admin.account-super-admin-add');
        } catch (\Exception $e) {
            Log::error('Error in account_super_admin_add: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengakses halaman.');
        }
    }

    /**
     * Memproses pembuatan akun super admin baru
     * Membuat user dan data super admin secara bersamaan menggunakan transaction
     */
    public function account_super_admin_add_data(Request $request)
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin attempting to add new super admin', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role,
                'request_data' => $request->except(['password'])
            ]);

            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:3'
            ]);

            DB::beginTransaction();

            // Buat user baru
            $user = User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'super_admin'
            ]);

            // Buat profil admin tanpa jurusan_id untuk super admin
            Super_admin::create([
                'user_id' => $user->id,
                'nama' => $request->nama
            ]);

            DB::commit();

            Log::info('Super admin successfully added new super admin', [
                'user_id' => auth()->id(),
                'new_super_admin_id' => $user->id
            ]);

            return redirect()->route('super.account-super-admin-table')
                ->with('success', 'Super admin berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in account_super_admin_add_data: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan super admin: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman kelola periode
     */
    public function kelola_periode()
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin accessed kelola_periode', [
                'user_id' => auth()->id(),
                'user_role' => auth()->user()->role
            ]);

            $periodes = Periode::all();
            return view('Super_admin.kelola-periode', compact('periodes'));
        } catch (\Exception $e) {
            Log::error('Error in kelola_periode: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengakses halaman.');
        }
    }

    /**
     * Memproses penambahan periode baru
     */
    public function add_periode(Request $request)
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin attempting to add new periode', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $request->validate([
                'tahun_ajaran' => 'required|string|unique:periode,tahun_ajaran'
            ]);

            Periode::create([
                'tahun_ajaran' => $request->tahun_ajaran,
                'is_active' => false
            ]);

            Log::info('Periode created successfully', [
                'tahun_ajaran' => $request->tahun_ajaran
            ]);

            return redirect()->route('super.kelola-periode')
                ->with('success', 'Periode berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error in add_periode: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Mengaktifkan periode tertentu
     * Tidak menonaktifkan periode lain yang sudah aktif
     */
    public function activate_periode($id)
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin attempting to activate periode', [
                'user_id' => auth()->id(),
                'periode_id' => $id
            ]);

            // Aktifkan periode yang dipilih tanpa menonaktifkan yang lain
            $periode = Periode::findOrFail($id);
            $periode->update(['is_active' => true]);

            Log::info('Periode activated successfully', [
                'periode_id' => $id
            ]);

            return redirect()->route('super.kelola-periode')
                ->with('success', 'Periode berhasil diaktifkan.');
        } catch (\Exception $e) {
            Log::error('Error in activate_periode: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengaktifkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus periode
     * Validasi periode tidak sedang aktif dan tidak memiliki data mahasiswa
     */
    public function delete_periode($id)
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin attempting to delete periode', [
                'user_id' => auth()->id(),
                'periode_id' => $id
            ]);

            $periode = Periode::findOrFail($id);

            // Cek apakah periode sedang aktif
            if ($periode->is_active) {
                throw new \Exception('Tidak dapat menghapus periode yang sedang aktif.');
            }

            // Cek apakah periode memiliki data calon mahasiswa
            if ($periode->calon_mahasiswa()->exists()) {
                throw new \Exception('Tidak dapat menghapus periode yang memiliki data mahasiswa.');
            }

            $periode->delete();

            Log::info('Periode deleted successfully', [
                'periode_id' => $id
            ]);

            return redirect()->route('super.kelola-periode')
                ->with('success', 'Periode berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in delete_periode: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus periode: ' . $e->getMessage());
        }
    }

    /**
     * Menonaktifkan periode tertentu
     */
    public function deactivate_periode($id)
    {
        try {
            $this->checkSuperAdmin();
            Log::info('Super admin attempting to deactivate periode', [
                'user_id' => auth()->id(),
                'periode_id' => $id
            ]);

            $periode = Periode::findOrFail($id);
            $periode->update(['is_active' => false]);

            Log::info('Periode deactivated successfully', [
                'periode_id' => $id
            ]);

            return redirect()->route('super.kelola-periode')
                ->with('success', 'Periode berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            Log::error('Error in deactivate_periode: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menonaktifkan periode: ' . $e->getMessage());
        }
    }

    /**
     * Memublikasikan hasil penilaian ke mahasiswa
     * Validasi semua assessor sudah submit dan update status RPL
     */
    public function publishResults(Request $request, $mahasiswa_id)
    {
        $this->checkSuperAdmin();
        try {
            $assessment = Assessment::where('calon_mahasiswa_id', $mahasiswa_id)->firstOrFail();
            // Cek apakah semua asesor sudah submit
            $all_submitted = true;
            if ($assessment->assessor_id_1 && !$assessment->assessor_1_submitted_at) $all_submitted = false;
            if ($assessment->assessor_id_2 && !$assessment->assessor_2_submitted_at) $all_submitted = false;
            if ($assessment->assessor_id_3 && !$assessment->assessor_3_submitted_at) $all_submitted = false;
    
            if ($all_submitted) {
                $assessment->published_at = now();
                // Otomatisasi status RPL: jika status sebelumnya 'ditinjau admin' atau 'banding', ubah ke 'selesai'
                if (in_array($assessment->rpl_status, ['ditinjau admin', 'banding'])) {
                    $assessment->rpl_status = 'selesai';
                }
                $assessment->save();
    
                // Update nilai_akhir di matkul_score
                $mahasiswa = \App\Models\Calon_mahasiswa::find($mahasiswa_id);
                if ($mahasiswa) {
                    $matkuls = \App\Models\Matkul::where('jurusan_id', $mahasiswa->jurusan_id)->get();
                    foreach ($matkuls as $matkul) {
                        $scores = \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa->id)
                            ->where('matkul_id', $matkul->id)
                            ->whereNotNull('nilai')
                            ->get();
                        if ($scores->count() > 0) {
                            $avg = round($scores->avg('nilai'), 2);
                            \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa->id)
                                ->where('matkul_id', $matkul->id)
                                ->update(['nilai_akhir' => $avg]);
                        }
                    }
                }
    
                return redirect()->back()->with('success', 'Hasil penilaian berhasil dipublikasikan ke mahasiswa.');
            } else {
                return redirect()->back()->with('error', 'Belum semua asesor menyelesaikan penilaian.');
            }
    
        } catch (\Exception $e) {
            \Log::error('Error publishing results: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * API untuk mengambil data CPMK berdasarkan mata kuliah
     */
    public function getCpmkByMatkul($matkulId)
    {
        \Log::info('Accessing getCpmkByMatkul', ['matkul_id' => $matkulId]);
        try {
            $cpmks = Cpmk::where('matkul_id', $matkulId)->get();
            \Log::info('CPMK fetched successfully', ['count' => $cpmks->count()]);
            return response()->json($cpmks);
        } catch (\Exception $e) {
            \Log::error('Error in getCpmkByMatkul', ['error' => $e->getMessage(), 'matkul_id' => $matkulId]);
            return response()->json(['message' => 'Error fetching CPMK'], 500);
        }
    }

    /**
     * Menampilkan daftar mahasiswa yang mengajukan banding
     * Dengan filter jurusan
     */
    public function daftar_banding(Request $request)
    {
        $jurusans = Jurusan::all();
        $jurusan_id = $request->jurusan_id;
        
        $query = \App\Models\Matkul_score::where('is_banding', true)
            ->whereHas('calon_mahasiswa')
            ->with('calon_mahasiswa.jurusan');
            
        // Terapkan filter jurusan jika dipilih
        if ($jurusan_id) {
            $query->whereHas('calon_mahasiswa', function($q) use ($jurusan_id) {
                $q->where('jurusan_id', $jurusan_id);
            });
        }
        
        $mahasiswa_banding = $query->get()->groupBy('calon_mahasiswa_id');
        
        return view('Super_admin.daftar-banding', compact('mahasiswa_banding', 'jurusans'));
    }

    /**
     * Menampilkan detail banding untuk satu mahasiswa
     */
    public function detail_banding_mahasiswa($camaba_id)
    {
        $camaba = \App\Models\Calon_mahasiswa::with(['ijazah', 'bukti_alih_jenjang', 'transkrip', 'jurusan', 'user'])->findOrFail($camaba_id);

        // Ambil hanya satu baris per matkul_id
        $banding_matkul = \App\Models\Matkul_score::with('matkul')
            ->where('calon_mahasiswa_id', $camaba_id)
            ->where('is_banding', true)
            ->get()
            ->groupBy('matkul_id')
            ->map(function($items) {
                return $items->first(); // Ambil satu saja per matkul
            });

        return view('Super_admin.detail-banding-mahasiswa', compact('camaba', 'banding_matkul'));
    }

    /**
     * Memproses banding: update nilai_akhir dan status banding untuk semua matkul banding satu mahasiswa
     */
    public function proses_banding(Request $request, $camaba_id)
    {
        $nilai_akhir = $request->input('nilai_akhir', []);
        $banding_status = $request->input('banding_status', []);
        foreach ($nilai_akhir as $matkul_id => $nilai) {
            $status = $banding_status[$matkul_id] ?? 'pending';
            if ($status === 'diterima') {
                // Update nilai_akhir dan status jika diterima
                $updateData = [
                    'nilai_akhir' => $nilai,
                    'banding_status' => $status,
                    'status' => 'Lolos',
                ];
                \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba_id)
                    ->where('matkul_id', $matkul_id)
                    ->where('is_banding', true)
                    ->update($updateData);
            } elseif ($status === 'ditolak') {
                // Hanya update banding_status saja, nilai_akhir tidak diubah
                \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba_id)
                    ->where('matkul_id', $matkul_id)
                    ->where('is_banding', true)
                    ->update([
                        'banding_status' => $status,
                    ]);
            } else {
                // Untuk pending, update seperti sebelumnya
                \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba_id)
                    ->where('matkul_id', $matkul_id)
                    ->where('is_banding', true)
                    ->update([
                        'nilai_akhir' => $nilai,
                        'banding_status' => $status,
                    ]);
            }
        }
        return redirect()->back()->with('success', 'Semua banding berhasil diproses.');
    }

    /**
     * Memublikasikan hasil banding ke mahasiswa
     * Update status RPL dan reset flag is_banding
     */
    public function publishResultsBanding(Request $request, $mahasiswa_id)
    {
        $this->checkSuperAdmin();
        try {
            $assessment = Assessment::where('calon_mahasiswa_id', $mahasiswa_id)->firstOrFail();
            $assessment->published_at = now();
            // Otomatisasi status RPL: jika status sebelumnya 'banding', ubah ke 'selesai'
            if ($assessment->rpl_status === 'banding') {
                $assessment->rpl_status = 'selesai';
            }
            $assessment->save();
            \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa_id)
                ->where('is_banding', true)
                ->update(['is_banding' => false]);

            return redirect()->back()->with('success', 'Hasil banding berhasil dipublikasikan ke mahasiswa.');
        } catch (\Exception $e) {
            \Log::error('Error publishing banding results: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function rekapNilaiAkhir($mahasiswa_id)
    {
        $mahasiswa = \App\Models\Calon_mahasiswa::with('jurusan')->findOrFail($mahasiswa_id);
        $assessment = \App\Models\Assessment::where('calon_mahasiswa_id', $mahasiswa_id)->first();
        $matkuls = \App\Models\Matkul::where('jurusan_id', $mahasiswa->jurusan_id)->get();

        $final_results = [];
        foreach ($matkuls as $matkul) {
            $scores = \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa_id)
                ->where('matkul_id', $matkul->id)
                ->whereNotNull('nilai')
                ->get();
            $avg = $scores->count() > 0 ? round($scores->avg('nilai'), 2) : '-';
            $status = $avg >= 75 ? 'Lolos' : 'Tidak Lolos';
            $final_results[] = [
                'matkul' => $matkul->nama_matkul,
                'nilai' => $avg,
                'status' => $status,
            ];
        }
        return response()->json([
            'final_results' => $final_results,
            'published_at' => $assessment ? $assessment->published_at : null,
        ]);
    }

    public function ubahStatusRpl(Request $request, $mahasiswa_id)
    {
        $request->validate([
            'rpl_status' => 'required|in:self-assessment,penilaian assessor,ditinjau admin,selesai,banding'
        ]);
        $assessment = \App\Models\Assessment::where('calon_mahasiswa_id', $mahasiswa_id)->firstOrFail();
        $now = now();
        $status = $request->rpl_status;
        if ($status === 'penilaian assessor') {
            $assessment->assessor_1_submitted_at = null;
            $assessment->assessor_2_submitted_at = null;
            $assessment->assessor_3_submitted_at = null;
        } else {
            if ($assessment->assessor_id_1) $assessment->assessor_1_submitted_at = $now;
            if ($assessment->assessor_id_2) $assessment->assessor_2_submitted_at = $now;
            if ($assessment->assessor_id_3) $assessment->assessor_3_submitted_at = $now;
        }
        if ($status === 'ditinjau admin') {
            $assessment->published_at = null;
        } else {
            if ($assessment->published_at) $assessment->published_at = $now;
        }
        $assessment->rpl_status = $status;
        $assessment->save();
        return back()->with('success', 'Status RPL berhasil diubah.');
    }
} 