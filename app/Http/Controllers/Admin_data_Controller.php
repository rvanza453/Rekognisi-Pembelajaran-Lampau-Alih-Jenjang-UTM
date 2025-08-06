<?php

namespace App\Http\Controllers;
use App\Models\Jurusan;
use App\Models\Admin;
use App\Models\User;
use App\Models\Calon_mahasiswa;
use App\Models\Assessor;
use App\Models\Cpmk;
use App\Models\Matkul;
use App\Models\Transkrip;
use App\Models\Assessment;
use App\Models\Periode;
use App\Models\CpmkAssessment;
use App\Models\Matkul_score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/**
 * Controller untuk mengelola semua fungsi Admin
 * Menangani profil, manajemen akun, dan pengaturan sistem untuk admin jurusan
 */
class Admin_data_Controller extends Controller
{
    /**
     * Mendapatkan ID jurusan dari admin yang sedang login
     */
    private function getAdminJurusan()
    {
        $admin = Admin::where('user_id', Auth::id())->first();
        return $admin ? $admin->jurusan_id : null;
    }

    /**
     * Menampilkan halaman profil admin
     */
    public function profile_view_admin(){
        $user = Auth::user();
        $admin = $user->admin ?? new Admin(['user_id' => $user->id]);
        return view('Admin/profile-admin', compact('admin','user'));
    }

    /**
     * Menampilkan form edit profil admin
     */
    public function profile_edit_admin_view($id){
        $admin = Admin::where('user_id', Auth::id())->findOrFail($id);
        return view('Admin/profile-edit-admin', compact('admin'));
    }

    /**
     * Memproses update data profil admin
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

        return redirect()->route('profile-view-admin');
    }

    /**
     * Menampilkan tabel data assessor untuk jurusan admin
     */
    public function account_assessor_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $users_assessor = User::where('role', 'assessor')
            ->whereHas('assessor', function($query) use ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            })->get();

        return view('Admin/account-assessor-table', compact('users_assessor'));
    }

    /**
     * Menampilkan form tambah akun assessor
     */
    public function account_assessor_add(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        return view('Admin/account-assessor-add', compact('jurusan_id'));
    }

    /**
     * Memproses pembuatan akun assessor baru
     * Membuat user dan data assessor secara bersamaan
     */
    public function account_assessor_add_data(Request $request){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
        ]);

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
            'jurusan_id' => $jurusan_id,
        ]);

        return redirect()->route('account-assessor-table')->with('success', 'Assessor created successfully!');
    }

    /**
     * Menampilkan tabel data user/pendaftar untuk jurusan admin
     * Hanya menampilkan data dari periode yang aktif
     */
    public function account_user_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $active_periodes = Periode::where('is_active', true)->get();
        $users_camaba = [];
        
        if ($active_periodes->isNotEmpty()) {
            $users_camaba = User::where('role', 'pendaftar')
                ->whereHas('calon_mahasiswa', function($query) use ($jurusan_id, $active_periodes) {
                    $query->where('jurusan_id', $jurusan_id)
                          ->whereIn('periode_id', $active_periodes->pluck('id'));
                })->get();
        }

        return view('Admin/account-user-table', compact('users_camaba'));
    }

    /**
     * Menampilkan form tambah akun user/pendaftar
     */
    public function account_user_add(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $periodes = Periode::all();
        return view('Admin/account-user-add', compact('jurusan_id', 'periodes'));
    }

    /**
     * Memproses pembuatan akun user/pendaftar baru
     * Membuat user dan data calon mahasiswa secara bersamaan
     */
    public function account_user_add_data(Request $request){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
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
            'jenis_mahasiswa' => 'camaba_alihjenjang',
            'jurusan_id' => $jurusan_id,
            'periode_id' => $request->periode_id,
        ]);

        return redirect()->route('account-user-table')->with('success', 'Pendaftar created successfully!');
    }

    /**
     * Menampilkan tabel data admin untuk jurusan admin
     */
    public function account_admin_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $users_admin = User::where('role', 'admin')
            ->whereHas('admin', function($query) use ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            })->get();

        return view('Admin/account-admin-table', compact('users_admin'));
    }

    /**
     * Menampilkan form tambah akun admin
     */
    public function account_admin_add(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        return view('Admin/account-admin-add', compact('jurusan_id'));
    }

    /**
     * Memproses pembuatan akun admin baru
     * Membuat user dan data admin secara bersamaan
     */
    public function account_admin_add_data(Request $request){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $request->validate([
            'email' => 'required|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'nama' => 'required|string|max:255',
        ]);

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
            'jurusan_id' => $jurusan_id,
        ]);

        return redirect()->route('account-admin-table')->with('success', 'Admin created successfully!');
    }

    /**
     * Menampilkan halaman kelola assessor
     */
    public function kelola_assessor_table(){
        return view('Admin/kelola-assessor-table');
    }
    
    /**
     * Menampilkan halaman kelola assessor untuk mahasiswa
     * Menampilkan data mahasiswa dan assessor dari jurusan admin
     */
    public function kelola_assessor_mahasiswa(Request $request)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $calon_mahasiswa = Calon_mahasiswa::with([
                'jurusan', 
                'assessment.assessor1', 
                'assessment.assessor2', 
                'assessment.assessor3'
            ])
            ->where('jurusan_id', $jurusan_id)
            ->get();

        $assessor = Assessor::where('jurusan_id', $jurusan_id)->get();

        return view('Admin/kelola-assessor-mahasiswa', compact('calon_mahasiswa', 'assessor'));
    }

    /**
     * Memproses penambahan assessor untuk mahasiswa
     * Validasi assessor harus dari jurusan yang sama dan tidak boleh duplikat
     */
/**
     * Memproses penambahan atau pembaruan assessor untuk mahasiswa
     * dengan logika reset status submit jika assessor diganti.
     */
    public function kelola_assessor_mahasiswa_add(Request $request)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }
    
        $validated = $request->validate([
            'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
            'assessor1_id' => 'required|exists:assessor,id',
            'assessor2_id' => 'nullable|exists:assessor,id',
            'assessor3_id' => 'nullable|exists:assessor,id',
            'deadline' => 'nullable|date|after_or_equal:now',
        ]);
    
        $mahasiswa = \App\Models\Calon_mahasiswa::find($validated['calon_mahasiswa_id']);
        if ($mahasiswa->jurusan_id !== $jurusan_id) {
            return back()->withErrors(['message' => 'Akses tidak sah ke mahasiswa ini.']);
        }
    
        $assessorIds = array_filter([
            $validated['assessor1_id'],
            $validated['assessor2_id'],
            $validated['assessor3_id'],
        ]);
    
        foreach ($assessorIds as $assessorId) {
            $assessor = \App\Models\Assessor::find($assessorId);
            if ($assessor->jurusan_id !== $jurusan_id) {
                return back()->withErrors(['message' => 'Asesor harus berasal dari jurusan yang sama.']);
            }
        }
    
        if (count($assessorIds) !== count(array_unique($assessorIds))) {
            return back()->withErrors(['message' => 'Asesor tidak boleh sama.']);
        }
    
        // Ambil data assessment yang sudah ada (jika ada)
        $existingAssessment = \App\Models\Assessment::where('calon_mahasiswa_id', $validated['calon_mahasiswa_id'])->first();
    
        // Siapkan data yang akan di-update
        $dataToUpdate = [
            'jurusan_id' => $jurusan_id,
            'assessor_id_1' => $validated['assessor1_id'],
            'assessor_id_2' => $validated['assessor2_id'],
            'assessor_id_3' => $validated['assessor3_id'],
            'deadline' => $validated['deadline'] ? \Carbon\Carbon::parse($validated['deadline']) : null,
        ];
    
        // Logika untuk mereset status submit jika asesor diganti
        if ($existingAssessment) {
            if ($existingAssessment->assessor_id_1 != $validated['assessor1_id']) {
                $dataToUpdate['assessor_1_submitted_at'] = null;
            }
            if ($existingAssessment->assessor_id_2 != $validated['assessor2_id']) {
                $dataToUpdate['assessor_2_submitted_at'] = null;
            }
            if ($existingAssessment->assessor_id_3 != $validated['assessor3_id']) {
                $dataToUpdate['assessor_3_submitted_at'] = null;
            }
        }
    
        // Gunakan updateOrCreate pada tabel 'assessment' yang benar
        \App\Models\Assessment::updateOrCreate(
            ['calon_mahasiswa_id' => $validated['calon_mahasiswa_id']],
            $dataToUpdate
        );

        return redirect()->route('kelola-assessor-mahasiswa')->with('success', 'Penugasan asesor berhasil diperbarui!');
    }

    /**
     * Memublikasikan hasil penilaian ke mahasiswa
     * Validasi semua assessor sudah submit dan update status RPL
     */
    public function publishResults(Request $request, $mahasiswa_id)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        try {
            $mahasiswa = Calon_mahasiswa::where('id', $mahasiswa_id)->where('jurusan_id', $jurusan_id)->firstOrFail();
            $assessment = Assessment::where('calon_mahasiswa_id', $mahasiswa->id)->firstOrFail();

            // Cek apakah semua asesor sudah submit
            $all_submitted = true;
            if ($assessment->assessor_id_1 && !$assessment->assessor_1_submitted_at) $all_submitted = false;
            if ($assessment->assessor_id_2 && !$assessment->assessor_2_submitted_at) $all_submitted = false;
            if ($assessment->assessor_id_3 && !$assessment->assessor_3_submitted_at) $all_submitted = false;

            if ($all_submitted) {
                $assessment->published_at = now();
                if (in_array($assessment->rpl_status, ['ditinjau admin', 'banding'])) {
                    $assessment->rpl_status = 'selesai';
                }
                $assessment->save();

                // Update nilai_akhir di matkul_score
                $matkuls = \App\Models\Matkul::where('jurusan_id', $jurusan_id)->get();
                foreach ($matkuls as $matkul) {
                    // <<< PERUBAHAN LOGIKA DI SINI >>>
                    // Menghapus ->where('status', 'Lolos') untuk mengambil semua nilai
                    $scores = \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa->id)
                        ->where('matkul_id', $matkul->id)
                        ->whereNotNull('nilai') // Tetap pastikan ada nilai dari asesor
                        ->get();
                    
                    if ($scores->count() > 0) {
                        $avg = round($scores->avg('nilai'), 2);
                        // Update semua baris matkul_score untuk matkul & mahasiswa tsb
                        \App\Models\Matkul_score::where('calon_mahasiswa_id', $mahasiswa->id)
                            ->where('matkul_id', $matkul->id)
                            ->update(['nilai_akhir' => $avg]);
                    }
                }

                return redirect()->back()->with('success', 'Hasil penilaian berhasil dipublikasikan ke mahasiswa.');
            } else {
                return redirect()->back()->with('error', 'Belum semua asesor menyelesaikan penilaian.');
            }

        } catch (\Exception $e) {
            \Log::error('Error publishing results by admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan tabel mata kuliah untuk jurusan admin
     */
    public function kelola_matkul_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $matkuls = Matkul::where('jurusan_id', $jurusan_id)->get();
        return view('Admin/kelola-matkul-table', compact('matkuls'));
    }

    /**
     * Memproses penambahan mata kuliah baru
     * Melakukan validasi, translasi nama mata kuliah, dan generate sinonim
     */
     public function kelola_matkul_add_data(Request $request)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan.');
        }
    
        try {
            // 1. Validasi Input
            $validator = \Validator::make($request->all(), [
                'nama_matkul' => [
                    'required',
                    'string',
                    'max:255',
                    // Rule untuk memastikan nama_matkul unik per jurusan
                    function ($attribute, $value, $fail) use ($jurusan_id) {
                        if (Matkul::where('jurusan_id', $jurusan_id)->where('nama_matkul', $value)->exists()) {
                            $fail('Mata kuliah dengan nama ini sudah ada di jurusan Anda.');
                        }
                    }
                ],
                'kode_matkul' => [
                    'nullable',
                    'string',
                    'max:20',
                    // Rule untuk memastikan kode_matkul unik per jurusan (jika diisi)
                    function ($attribute, $value, $fail) use ($jurusan_id) {
                        if ($value && Matkul::where('jurusan_id', $jurusan_id)->where('kode_matkul', $value)->exists()) {
                            $fail('Mata kuliah dengan kode ini sudah ada di jurusan Anda.');
                        }
                    }
                ],
                'sks' => 'nullable|integer|min:1|max:6',
            ]);
    
            // Jika validasi gagal, kembalikan ke halaman sebelumnya dengan error
            if ($validator->fails()) {
                return redirect()->route('kelola-matkul-table')
                    ->withErrors($validator)
                    ->withInput();
            }
    
            // 2. Buat dan Simpan Data Mata Kuliah
            Matkul::create([
                'nama_matkul' => $request->nama_matkul,
                'kode_matkul' => $request->kode_matkul,
                'sks'         => $request->sks,
                'jurusan_id'  => $jurusan_id,
                // 'sinonim' tidak lagi diisi karena proses mapping dihilangkan
            ]);
    
            // 3. Kembalikan ke halaman tabel mata kuliah dengan pesan sukses
            return redirect()->route('kelola-matkul-table')
                ->with('success', 'Mata Kuliah berhasil ditambahkan.');
    
        } catch (\Exception $e) {
            // Tangani jika ada error tak terduga
            \Log::error('Error in kelola_matkul_add_data: ' . $e->getMessage());
            return redirect()->route('kelola-matkul-table')
                ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah. Silakan coba lagi.');
        }
    }
     
    // public function kelola_matkul_add_data(Request $request)
    // {
    //     $jurusan_id = $this->getAdminJurusan();
    //     if (!$jurusan_id) {
    //         return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
    //     }

    //     try {
    //         \Log::info('Validating mata kuliah data', [
    //             'nama_matkul' => $request->nama_matkul,
    //             'kode_matkul' => $request->kode_matkul,
    //             'jurusan_id' => $jurusan_id
    //         ]);

    //         $validator = \Validator::make($request->all(), [
    //             'nama_matkul' => [
    //                 'required',
    //                 'string',
    //                 'max:255',
    //                 function ($attribute, $value, $fail) use ($jurusan_id) {
    //                     $exists = Matkul::where('jurusan_id', $jurusan_id)
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
    //             'kode_matkul' => [
    //                 'nullable',
    //                 'string',
    //                 'max:20',
    //                 function ($attribute, $value, $fail) use ($jurusan_id) {
    //                     if ($value) {
    //                         $exists = Matkul::where('jurusan_id', $jurusan_id)
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
    //             return redirect()->route('kelola-matkul-table')
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
    //                     \Log::info("Mata Kuliah: {$matkulName} -> Translated: {$translatedName}");
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
    //                     \Log::info("Fallback translation: {$matkulName} -> {$translatedName}");
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
    //                 \Log::info('WordNet synonyms for ' . $translatedName . ': ' . json_encode($wordnetSynonyms));
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

    //         $synonyms[] = $matkulName;
    //         $synonyms[] = $translatedName;
    //         $synonyms = array_unique(array_filter($synonyms));
            
    //         \Log::info('Final mapping untuk ' . $matkulName . ': ' . json_encode($synonyms));

    //         $matkul = Matkul::create([
    //             'nama_matkul' => $matkulName,
    //             'jurusan_id' => $jurusan_id,
    //             'sinonim' => json_encode($synonyms),
    //             'kode_matkul' => $request->kode_matkul,
    //             'sks' => $request->sks
    //         ]);

    //         return redirect()->route('kelola-matkul-table')
    //             ->with('success', 'Mata Kuliah berhasil ditambahkan dengan ' . count($synonyms) . ' sinonim!');

    //     } catch (\Exception $e) {
    //         \Log::error('Error in kelola_matkul_add_data: ' . $e->getMessage());
    //         return redirect()->route('kelola-matkul-table')
    //             ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah. Silakan coba lagi.');
    //     }
    // }

    public function edit_matkul(Request $request, Matkul $matkul)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        if ($matkul->jurusan_id !== $jurusan_id) {
            return back()->with('error', 'Unauthorized access to this course.');
        }

        $request->validate([
            'nama_matkul' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($jurusan_id, $matkul) {
                    if (Matkul::where('jurusan_id', $jurusan_id)
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
                function ($attribute, $value, $fail) use ($jurusan_id, $matkul) {
                    if ($value && Matkul::where('jurusan_id', $jurusan_id)
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
                        \Log::info("Mata Kuliah: {$matkulName} -> Translated: {$translatedName}");
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Google Translate error: ' . $e->getMessage());
                
                try {
                    $response = Http::withoutVerifying()->post('https://libretranslate.de/translate', [
                        'q' => $matkulName,
                        'source' => 'id',
                        'target' => 'en'
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        $translatedName = $result['translatedText'];
                        \Log::info("Fallback translation: {$matkulName} -> {$translatedName}");
                    }
                } catch (\Exception $e) {
                    \Log::error('Fallback translation error: ' . $e->getMessage());
                }
            }

            $synonyms = [$translatedName];
            
            try {
                $response = Http::get('http://localhost:5000/synonyms', [
                    'word' => strtolower($translatedName)
                ]);

                if ($response->successful()) {
                    $wordnetSynonyms = $response->json();
                    if (!empty($wordnetSynonyms)) {
                        $synonyms = array_merge($synonyms, $wordnetSynonyms);
                    }
                    \Log::info('WordNet synonyms for ' . $translatedName . ': ' . json_encode($wordnetSynonyms));
                } else {
                    \Log::warning('WordNet service returned non-successful status: ' . $response->status());
                }
            } catch (\Exception $e) {
                \Log::error('WordNet service error: ' . $e->getMessage());
                
                // Fallback: Add common synonyms based on keywords
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

            $synonyms[] = $matkulName;
            $synonyms[] = $translatedName;
            $synonyms = array_unique(array_filter($synonyms));
            
            \Log::info('Final mapping for ' . $matkulName . ': ' . json_encode($synonyms));

            $matkul->update([
                'nama_matkul' => $matkulName,
                'kode_matkul' => $request->kode_matkul,
                'sks' => $request->sks,
                'sinonim' => json_encode($synonyms) // Update the synonym field
            ]);

            return redirect()->route('kelola-matkul-table')
                ->with('success', 'Mata Kuliah berhasil diperbarui dengan ' . count($synonyms) . ' sinonim!');
        } catch (\Exception $e) {
            \Log::error('Error in edit_matkul: ' . $e->getMessage());
            return redirect()->route('kelola-matkul-table')
                ->with('error', 'Terjadi kesalahan saat memperbarui mata kuliah. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan tabel CPMK untuk mata kuliah tertentu
     * Validasi mata kuliah harus dari jurusan admin
     */
    public function kelola_cpmk_table($matkul_id){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $matkul = Matkul::where('jurusan_id', $jurusan_id)->findOrFail($matkul_id);
        $cpmks = Cpmk::where('matkul_id', $matkul_id)->get();
        return view('Admin/kelola-cpmk-table', compact('matkul', 'cpmks'));
    }
   
    /**
     * Menampilkan form tambah CPMK untuk mata kuliah tertentu
     */
    public function create_data_cpmk($matkul_id)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $matkul = Matkul::where('jurusan_id', $jurusan_id)->findOrFail($matkul_id);
        return view('Admin/kelola-cpmk-table', compact('matkul'));
    }

    /**
     * Memproses penambahan CPMK baru
     */
    public function add_data_cpmk(Request $request)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $request->validate([
            'penjelasan' => 'required|string',
            'matkul_id' => 'required|exists:matkul,id',
            'kode_cpmk' => 'required|string|max:20',
        ]);

        $matkul = Matkul::where('jurusan_id', $jurusan_id)->findOrFail($request->matkul_id);

        Cpmk::create([
            'penjelasan' => $request->penjelasan,
            'matkul_id' => $request->matkul_id,
            'kode_cpmk' => $request->kode_cpmk,
        ]);

        return redirect()->route('kelola-cpmk-table', $request->matkul_id)->with('success', 'CPMK created successfully!');
    }

    /**
     * Menghapus data CPMK
     */
    public function delete(Cpmk $cpmk)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $matkul = Matkul::where('jurusan_id', $jurusan_id)->findOrFail($cpmk->matkul_id);
        $cpmk->delete();
        return back()->with('success', 'CPMK deleted successfully!');
    }

    /**
     * Menghapus data CPMK (alias untuk delete)
     */
    public function delete_cpmk(Cpmk $cpmk)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $matkul = Matkul::where('jurusan_id', $jurusan_id)->findOrFail($cpmk->matkul_id);
        $cpmk->delete();
        return back()->with('success', 'CPMK deleted successfully!');
    }
    
    /**
     * Menghapus data mata kuliah
     * Validasi mata kuliah harus dari jurusan admin
     */
    public function delete_matkul(Matkul $matkul)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        if ($matkul->jurusan_id !== $jurusan_id) {
            return back()->with('error', 'Unauthorized access to this course.');
        }

        $matkul->delete();
        return back()->with('success', 'Matkul deleted successfully!');
    }

    /**
     * Menghapus data user
     * Validasi user harus dari jurusan admin
     */
    public function delete_user(User $user)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        if ($user->role === 'pendaftar') {
            $calon_mahasiswa = $user->calon_mahasiswa;
            if ($calon_mahasiswa && $calon_mahasiswa->jurusan_id !== $jurusan_id) {
                return back()->with('error', 'Unauthorized access to this user.');
            }
        } elseif ($user->role === 'assessor') {
            $assessor = $user->assessor;
            if ($assessor && $assessor->jurusan_id !== $jurusan_id) {
                return back()->with('error', 'Unauthorized access to this user.');
            }
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully!');
    }

    /**
     * Menampilkan tabel data user/pendaftar untuk jurusan admin
     * Hanya menampilkan data dari periode yang aktif
     */
    public function data_user_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $active_periodes = Periode::where('is_active', true)->get();
        $users_camaba = [];
        
        if ($active_periodes->isNotEmpty()) {
            $users_camaba = User::where('role', 'pendaftar')
                ->whereHas('calon_mahasiswa', function($query) use ($jurusan_id, $active_periodes) {
                    $query->where('jurusan_id', $jurusan_id)
                          ->whereIn('periode_id', $active_periodes->pluck('id'));
                })->get();
        }

        return view('Admin/data-user-table', compact('users_camaba'));
    }

    /**
     * Menampilkan tabel data assessor untuk jurusan admin
     */
    public function data_assessor_table(){
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        $assessor = Assessor::where('jurusan_id', $jurusan_id)->get();
        $users_assessor = User::where('role', 'assessor')
            ->whereHas('assessor', function($query) use ($jurusan_id) {
                $query->where('jurusan_id', $jurusan_id);
            })->get();

        return view('Admin/data-assessor-table', compact('users_assessor', 'assessor'));
    }

    /**
     * Menampilkan daftar mahasiswa yang ditugaskan ke assessor tertentu
     * Validasi assessor harus dari jurusan admin
     */
    public function view_assessor_student($assessor_id)
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        try {
            // Verifikasi assessor berasal dari jurusan admin
            $assessor = Assessor::where('id', $assessor_id)
                ->where('jurusan_id', $jurusan_id)
                ->firstOrFail();

            // Ambil semua mahasiswa yang ditugaskan ke assessor ini
            $assignedStudents = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip', 'bukti_alih_jenjang'])
                ->whereHas('assessment', function($query) use ($assessor_id) {
                    $query->where('assessor_id_1', $assessor_id)
                          ->orWhere('assessor_id_2', $assessor_id)
                          ->orWhere('assessor_id_3', $assessor_id);
                })
                ->where('jurusan_id', $jurusan_id)
                ->get();

            return view('Admin/view-assessor-students', compact('assessor', 'assignedStudents'));
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
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }

        try {
            // Verifikasi data dasar
            $assessor = Assessor::where('id', $assessor_id)->where('jurusan_id', $jurusan_id)->firstOrFail();
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip', 'bukti_alih_jenjang', 'assessment'])->where('id', $student_id)->where('jurusan_id', $jurusan_id)->firstOrFail();
            $assessment = $camaba->assessment;

            // Ambil semua mata kuliah jurusan
            $allMatkulJurusan = Matkul::with('cpmk')->where('jurusan_id', $jurusan_id)->get();

            // <<< PERUBAHAN UTAMA: Ambil data penilaian per CPMK >>>
            $cpmkAssessments = CpmkAssessment::where('calon_mahasiswa_id', $student_id)
                ->with(['matkul', 'cpmk'])
                ->get()
                ->groupBy('matkul.nama_matkul'); // Kelompokkan berdasarkan nama matkul untuk tampilan

            // Ambil semua data nilai akhir (hasil agregat)
            $matkulScores = Matkul_score::where('calon_mahasiswa_id', $student_id)->get()->keyBy('matkul_id');
            $allAssessorScores = Matkul_score::where('calon_mahasiswa_id', $student_id)->with('assessor')->get()->groupBy('matkul_id');
            
            // Ambil semua asesor yang ditugaskan
            $assignedAssessors = collect();
            if ($assessment) {
                $assessorIds = array_filter([$assessment->assessor_id_1, $assessment->assessor_id_2, $assessment->assessor_id_3]);
                $assignedAssessors = Assessor::whereIn('id', $assessorIds)->get()->keyBy('id');
            }

            // Siapkan data status untuk tab "Konversi Nilai"
            $matkulWithStatus = $allMatkulJurusan->map(function($matkul) use ($matkulScores, $allAssessorScores, $assignedAssessors) {
                $scoreInfo = $matkulScores->get($matkul->id);
                if ($scoreInfo) {
                    $matkul->isLolos = ($scoreInfo->status === 'Lolos');
                    $matkul->isComplete = true;
                    $matkul->finalScore = $scoreInfo->nilai_akhir;
                    $matkul->completedAssessmentsCount = optional($allAssessorScores->get($matkul->id))->count() ?? 0;
                } else {
                    $matkul->isLolos = false;
                    $matkul->isComplete = false;
                    $matkul->finalScore = null;
                    $matkul->completedAssessmentsCount = 0;
                }
                $matkul->requiredAssessments = $assignedAssessors->count();
                return $matkul;
            });

            return view('Admin.view-student-as-assessor', compact(
                'camaba', 
                'assessor', 
                'matkulWithStatus', 
                'allMatkulJurusan', 
                'matkulScores',
                'allAssessorScores',
                'assignedAssessors',
                'cpmkAssessments' // Kirim data baru ke view
            ));

        } catch (\Exception $e) {
            \Log::error('Error in view_student_as_assessor: ' . $e->getMessage());
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
     * Hanya menampilkan data dari jurusan admin
     */
    public function daftar_banding()
    {
        $jurusan_id = $this->getAdminJurusan();
        if (!$jurusan_id) {
            return redirect()->back()->with('error', 'Jurusan tidak ditemukan');
        }
        
        $mahasiswa_banding = \App\Models\Matkul_score::where('is_banding', true)
            ->whereHas('matkul', function($q) use ($jurusan_id) {
                $q->where('jurusan_id', $jurusan_id);
            })
            // <<< PERBAIKAN DI SINI: Pastikan mahasiswa terkait masih ada di database >>>
            ->whereHas('calon_mahasiswa') 
            ->with('calon_mahasiswa')
            ->get()
            ->groupBy('calon_mahasiswa_id');
            
        return view('Admin.daftar-banding', ['mahasiswa_banding' => $mahasiswa_banding]);
    }

    /**
     * Tampilkan detail banding untuk satu mahasiswa
     */
    public function detail_banding_mahasiswa($camaba_id)
    {
        $camaba = \App\Models\Calon_mahasiswa::with(['ijazah', 'bukti_alih_jenjang', 'transkrip'])->findOrFail($camaba_id);
        $existing_transkrip = Transkrip::where('calon_mahasiswa_id', $camaba->id)->first();
        $banding_matkul = \App\Models\Matkul_score::with('matkul')
            ->where('calon_mahasiswa_id', $camaba_id)
            ->where('is_banding', true)
            ->get();
        return view('Admin.detail-banding-mahasiswa', compact('camaba', 'banding_matkul','existing_transkrip'));
    }

    /**
     * Proses banding: update nilai_akhir dan status banding untuk semua matkul banding satu mahasiswa
     */
    public function proses_banding(Request $request, $camaba_id)
    {
        $nilai_akhir = $request->input('nilai_akhir', []);
        $banding_status = $request->input('banding_status', []);
        $has_banding = false;
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
                $has_banding = true;
            } elseif ($status === 'ditolak') {
                // Hanya update banding_status saja, nilai_akhir tidak diubah
                \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba_id)
                    ->where('matkul_id', $matkul_id)
                    ->where('is_banding', true)
                    ->update([
                        'banding_status' => $status,
                    ]);
                $has_banding = true;
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
        // Otomatisasi status RPL: jika ada banding (diterima/ditolak), update assessment->rpl_status menjadi 'banding'
        if ($has_banding) {
            $assessment = Assessment::where('calon_mahasiswa_id', $camaba_id)->first();
            if ($assessment && $assessment->rpl_status !== 'banding') {
                $assessment->rpl_status = 'banding';
                $assessment->save();
            }
        }
        return redirect()->back()->with('success', 'Semua banding berhasil diproses.');
    }

    public function publishResultsBanding(Request $request, $mahasiswa_id)
    {
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

    public function setRplStatusSelesai($mahasiswa_id)
    {
        $jurusan_id = $this->getAdminJurusan();
        $assessment = \App\Models\Assessment::where('calon_mahasiswa_id', $mahasiswa_id)
            ->where('jurusan_id', $jurusan_id)
            ->first();
        if ($assessment) {
            $assessment->rpl_status = 'selesai';
            $assessment->save();
            return redirect()->back()->with('success', 'Status RPL mahasiswa berhasil diubah menjadi selesai.');
        }
        return redirect()->back()->with('error', 'Assessment tidak ditemukan.');
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
}
