<?php

namespace App\Http\Controllers;

use App\Models\Calon_mahasiswa;
use App\Models\Self_assessment_camaba;
use App\Models\Jurusan;
use App\Models\Ijazah;
use App\Models\Matkul;
use App\Models\Matkul_score;
use App\Models\User;
use App\Models\Cpmk;
use App\Models\Bukti;
use App\Models\bukti_alih_jenjang;
use App\Models\Transkrip;
use App\Models\MatkulAssessment;
use App\Models\CpmkAssessment;
use GuzzleHttp\Promise\Create;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Http;

// Controller untuk mengelola data dan aktivitas user/calon mahasiswa
class User_data_Controller extends Controller
{
    // Menampilkan profil calon mahasiswa yang sedang login
    public function profile_view_camaba(){
        $user = Auth::user();
        $calon_mahasiswa = $user->calon_mahasiswa;
        return view('User/profile-view-camaba', compact('calon_mahasiswa','user'));
    }
    // Menampilkan halaman edit profil calon mahasiswa
    public function profile_edit_camaba_view($id){
        $calon_mahasiswa = Calon_mahasiswa::findOrFail($id);
        return view('User/profile-edit-camaba', compact('calon_mahasiswa'));
    }
    // Menyimpan perubahan data profil calon mahasiswa
    public function profile_edit_camaba(Request $request, $id){
        $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'nomor_rumah' => 'nullable|string|max:255',
            'nomor_kantor' => 'nullable|string|max:255',
            'kelamin' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
            'kebangsaan' => 'nullable|string|max:255',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5049',
            'nomor_telepon' => 'nullable|string|max:255',
        ]);

        $calon_mahasiswa = Calon_Mahasiswa::findOrFail($id);
        if ($request->hasFile('foto')) {
            $imageName = time().'.'.$request->foto->extension();  
            $request->foto->move(public_path('Data/profile_pict_camaba'), $imageName);
    
            // Hapus foto lama jika ada
            if ($calon_mahasiswa->foto) {
                $oldImagePath = public_path('Data/profile_pict_camaba/' . $calon_mahasiswa->foto);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
    
            $calon_mahasiswa->foto = $imageName; 
            $calon_mahasiswa->save(); // Simpan perubahan pada model secara terpisah
        }
    
        $calon_mahasiswa->update($request->except('foto'));

        return redirect()->route('profile-view-camaba');
    }
    
    // Menampilkan ijazah milik calon mahasiswa
    public function view_ijazah(){
        $user = Auth::user();
    
        // Cek apakah camaba yang login memiliki ijazah
        if ($user->calon_mahasiswa && $user->calon_mahasiswa->ijazah) {
            $ijazah = $user->calon_mahasiswa->ijazah;
            return view('User.view-ijazah', compact('ijazah'));
        } else {
            // Jika camaba belum memiliki ijazah, redirect ke halaman tambah ijazah
            return redirect()->route('ijazah-add-view')->with('message', 'Silakan tambah ijazah terlebih dahulu.');
        }
    }
    
    // Menampilkan halaman edit ijazah
    public function ijazah_edit_view($id)
    {
        $user = auth()->user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }
    
        $ijazah = Ijazah::where('id', $id)
                        ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
                        ->firstOrFail();
    
        return view('User.ijazah-edit', compact('ijazah'));
    }
    
    // Menampilkan halaman tambah ijazah
    public function ijazah_add_view(){
        // Get the logged-in user's calon_mahasiswa data
    $user = Auth::user();
    $calon_mahasiswa = $user->calon_mahasiswa;
    
        // Pass calon_mahasiswa data to the view if needed
    return view('User.ijazah-add', compact('calon_mahasiswa'));
    }
    
    
    // Menyimpan data ijazah baru
    public function ijazah_add(Request $request){
    $request->validate([
            'institusi_pendidikan' => 'nullable|string|max:255',
            'jenjang' => 'nullable|string|max:10',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'negara' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'ipk_nilai' => 'nullable|string|max:10',
            'tahun_lulus' => 'nullable|integer|min:1900|max:'.(date('Y')),
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);
    
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $filePath = 'Data/Ijazah/' . $filename;
    
        $file->move(public_path('Data/Ijazah'), $filename);
    
        Ijazah::create([
            'calon_mahasiswa_id' => Auth::user()->calon_mahasiswa->id,
            'institusi_pendidikan' => $request->institusi_pendidikan,
            'jenjang' => $request->jenjang,
            'kota' => $request->kota,
            'provinsi' => $request->provinsi,
            'negara' => $request->negara,
            'fakultas' => $request->fakultas,
            'jurusan' => $request->jurusan,
            'ipk_nilai' => $request->ipk_nilai,
            'tahun_lulus' => $request->tahun_lulus,
            'file' => $filePath,
        ]);
    
        return redirect()->route('view-ijazah')->with('success', 'Ijazah berhasil ditambahkan.');
    }
    
    // Menyimpan perubahan data ijazah
    public function ijazah_edit(Request $request, $id)
    {
        $request->validate([
            'institusi_pendidikan' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:255',
            'negara' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'ipk_nilai' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|Integer',
            'file' => 'sometimes|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
    
        $ijazah = Ijazah::findOrFail($id);
    
        if ($request->hasFile('file')) {
            $fileExtension = $request->file('file')->extension();
    
            $filename = time() . '.' . $fileExtension;
            $filePath = 'Data/Ijazah/' . $filename;
    
            $request->file('file')->move(public_path('Data/Ijazah/'), $filename);
    
            if ($ijazah->file) {
                $oldFilePath = public_path($ijazah->file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
    
            $ijazah->file = $filePath; 
        }
    
        $ijazah->update($request->except('file'));
        
        return redirect()->route('view-ijazah');
    }

    // Mengunduh file ijazah
    public function download_ijazah($filename)
    {
        $user = Auth::user();
        $ijazah = Ijazah::where('file', 'Data/Ijazah/' . $filename)
            ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
            ->firstOrFail();

        $path = public_path('Data/Ijazah/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    // Menampilkan halaman self assessment
    public function self_assessment(Request $request){
        $calon_mahasiswa = auth()->user()->calon_mahasiswa;
        $matkul = Matkul::where('jurusan_id', $calon_mahasiswa->jurusan_id)->select('id','nama_matkul')->get();
        $matkul_id = $request->matkul_id ?? ($matkul->isEmpty() ? null : $matkul[0]->id);
        $cpmks = [];
        if ($matkul_id) {
            $cpmks = Cpmk::where('matkul_id', $matkul_id)->get();
        }
        return view('User/self-assessment',compact('matkul','cpmks','matkul_id'));
    }

    // Menyimpan data self assessment beserta bukti
    public function add_self_assessment(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'matkul_dasar' => 'required|array',
            'nilai_matkul_dasar' => 'required|array',
            'self_assessment_value' => 'required|array',
        ]);

        $calon_mahasiswa = $user->calon_mahasiswa;
        $matkul_id = $request->matkul_id;

        foreach ($request->matkul_dasar as $cpmk_id => $matkulDasar) {
            $nilaiMatkulDasar = $request->nilai_matkul_dasar[$cpmk_id] ?? null;
            $selfAssessmentValue = $request->self_assessment_value[$cpmk_id] ?? null;
            if (!$matkulDasar || !$nilaiMatkulDasar || !$selfAssessmentValue) continue;

            \App\Models\CpmkAssessment::updateOrCreate(
                [
                    'calon_mahasiswa_id' => $calon_mahasiswa->id,
                    'matkul_id' => $matkul_id,
                    'cpmk_id' => $cpmk_id,
                ],
                [
                    'matkul_dasar' => $matkulDasar,
                    'nilai_matkul_dasar' => $nilaiMatkulDasar,
                    'self_assessment_value' => $selfAssessmentValue,
                ]
            );
        }

        return redirect()->route('self-assessment-table')->with('success', 'Penilaian berhasil disimpan.');
    }

    // Menampilkan tabel self assessment
    public function self_assessment_table(Request $request){
        $user = auth()->user();
        if ($user->role !== 'pendaftar'){
            abort(403, 'Unauthorized action');
        }
        $calon_mahasiswa = $user->calon_mahasiswa;
        $matkuls = Matkul::where('jurusan_id', $calon_mahasiswa->jurusan_id)->select('id', 'nama_matkul')->get();
        $matkul_id = $request->get('matkul_id') ?? ($matkuls->isEmpty() ? null : $matkuls[0]->id);

        // Data untuk edit mode
        $editMode = $request->get('edit_mode', false);
        $existingAssessments = [];
        
        if ($editMode && $matkul_id) {
            $existingAssessments = Self_assessment_camaba::with(['bukti', 'cpmk'])
                ->where('calon_mahasiswa_id', $calon_mahasiswa->id)
                ->whereHas('cpmk', function($query) use ($matkul_id) {
                    $query->where('matkul_id', $matkul_id);
                })
                ->get()
                ->keyBy('cpmk_id');
        }

        $cpmks = [];
        $assessments = [];
        if ($matkul_id) {
            $cpmks = Cpmk::where('matkul_id', $matkul_id)->get();
            $assessments = $calon_mahasiswa->self_assessment_camaba()->with('cpmk')->whereHas('cpmk', function($query) use ($matkul_id) {
                $query->where('matkul_id', $matkul_id);
            })->whereNotNull('nilai')->get();
        }
        return view('User/self-assessment-table', compact('assessments','matkuls','matkul_id','cpmks','editMode','existingAssessments'));
    }
    // public function view_nilai(){
    //     $calon_mahasiswa_id = auth()->user()->calon_mahasiswa->id;
    //     $assessments = DB::table('self_assessment_camaba')  // Menggunakan nama tabel yang benar
    //         ->join('cpmk', 'self_assessment_camaba.cpmk_id', '=', 'cpmk.id')
    //         ->select('cpmk.matkul_id')
    //         ->where('self_assessment_camaba.calon_mahasiswa_id', $calon_mahasiswa_id)
    //         ->groupBy('cpmk.matkul_id')
    //         ->get();
                
    //     // array untuk menyimpan hasil penilaian
    //     $matkulScores = [];
        
    //     foreach($assessments as $assessment) {
    //         // Cek nilai dari ketiga assessor untuk setiap mata kuliah
    //         $scores = Matkul_score::where('calon_mahasiswa_id', $calon_mahasiswa_id)
    //             ->where('matkul_id', $assessment->matkul_id)
    //             ->get();
                
    //         $matkul = Matkul::find($assessment->matkul_id);
            
    //         // Hitung jumlah assessor yang sudah menilai
    //         $assessorCount = $scores->count();
    //         // Hitung jumlah status 'Lolos'
    //         $lolosCount = $scores->where('status', 'Lolos')->count();
            
    //         // Tentukan status berdasarkan mayoritas
    //         $status = 'Belum Ditentukan';
    //         if ($assessorCount >= 2) { 
    //             if ($lolosCount >= 2) {
    //                 $status = 'Lolos';
    //             } elseif (($assessorCount - $lolosCount) >= 2) {
    //                 $status = 'Gagal';
    //             }
    //         }

    //         $matkulScores[] = [
    //             'matkul' => $matkul,
    //             'status' => $status,
    //             'nilai' => $assessorCount == 3 ? number_format($scores->pluck('nilai')->avg(), 2) : '-',
    //             'is_complete' => $assessorCount >= 2 // Ubah ke minimal 2 assessor
    //         ];
    //     }
    //     return view('User.view-nilai', compact('matkulScores'));
    // }
    public function view_nilai()
    {
        $user = Auth::user();
        
        // Check if user exists and has calon_mahasiswa data
        if (!$user || !$user->calon_mahasiswa) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $camaba = $user->calon_mahasiswa;
        $assessment = $camaba->assessment;
        $final_results = []; // Initialize as empty array

        // Check if results are published
        if ($assessment && $assessment->published_at) {
            // Get all matkuls for the student's major
            $matkuls = Matkul::where('jurusan_id', $camaba->jurusan_id)->get();

            foreach ($matkuls as $matkul) {
                // Get all scores for this matkul for this student
                $scores = Matkul_score::where('calon_mahasiswa_id', $camaba->id)
                    ->where('matkul_id', $matkul->id)
                    ->get();

                if ($scores->isNotEmpty()) {
                    // Status should be the same across all records for this matkul, so we take it from the first one.
                    $status = $scores->first()->status;

                    $final_score = $scores->firstWhere('nilai_akhir', '!=', null)->nilai_akhir ?? '-';

                    $final_results[] = [
                        'matkul' => $matkul,
                        'status' => $status,
                        'nilai' => $final_score ?? '-', 
                    ];
                }
            }
        }

        // Pass `published_at` to the view to show appropriate messages
        return view('User.view-nilai', [
            'final_results' => $final_results,
            'published_at' => $assessment ? $assessment->published_at : null
        ]);
    }
    // Menghapus data self assessment tertentu
    public function delete_self_assessment($id)
    {
        $user = auth()->user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }
    
        try {
            // Ambil data assessment berdasarkan ID dan user (melalui calon_mahasiswa)
            $assessment = $user->calon_mahasiswa
                             ->self_assessment_camaba()
                             ->findOrFail($id);
            
            // Hapus file bukti jika ada
            if ($assessment->bukti && Storage::exists($assessment->bukti)) {
                Storage::delete($assessment->bukti);
            }
            
            $assessment->delete();
            
            return redirect()->back()
                            ->with('success', 'Penilaian berhasil dihapus');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal menghapus penilaian');
        }
    }

    // Menampilkan halaman input transkrip
    public function input_transkrip(){
        $user = Auth::user();
        $calon_mahasiswa = $user->calon_mahasiswa;
        
        // Cek apakah mahasiswa sudah memiliki transkrip
        $existing_transkrip = Transkrip::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first();
        
        return view('User.input-transkrip', compact('calon_mahasiswa', 'existing_transkrip'));
    }
    
    // Menyimpan file transkrip yang diupload
    public function transkrip_add_data(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }
        
        try {
            // Validasi input file
            $request->validate([
                'file' => 'required|mimes:pdf|max:2048',
            ], [
                'file.required' => 'File transkrip harus dipilih.',
                'file.mimes' => 'File yang dipilih tidak sesuai. Hanya file PDF yang diperbolehkan.',
                'file.max' => 'Ukuran file tidak boleh lebih dari 2MB.'
            ]);

            // Proses upload file
            $file = $request->file('file');
            
            // Additional validation to ensure it's actually a PDF
            $mimeType = $file->getMimeType();
            if ($mimeType !== 'application/pdf') {
                return redirect()->back()
                    ->with('error', 'File yang dipilih tidak sesuai. Hanya file PDF yang diperbolehkan.')
                    ->withInput();
            }
            
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('Data/Transkrip'), $filename);

            // Buat record transkrip baru tanpa mata kuliah
            $transkrip = Transkrip::create([
                'file' => $filename,
                'calon_mahasiswa_id' => $user->calon_mahasiswa->id,
                'mata_kuliah_transkrip' => [], // Array kosong untuk diisi nanti
                'mapping_results' => null
            ]);

            return redirect()->route('input-transkrip')
                ->with('success', 'File transkrip berhasil diunggah. Silakan input mata kuliah.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors specifically
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Handle other unexpected errors
            \Log::error('Error uploading transkrip: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengunggah file. Silakan coba lagi.');
        }
    }

    // Menampilkan halaman input mata kuliah transkrip
    public function input_matkul_transkrip($id)
    {
        $transkrip = Transkrip::findOrFail($id);
        return view('User.input-matkul-transkrip', compact('transkrip'));
    }

    // Menyimpan data mata kuliah transkrip
    public function store_matkul_transkrip(Request $request, $id)
    {
        $transkrip = Transkrip::findOrFail($id);
        
        $request->validate([
            'mata_kuliah.nama' => 'required|string',
            'mata_kuliah.nilai' => 'required|string'
        ]);

        try {
            // Proses mata kuliah baru
            $matkulBaru = [
                'nama' => $request->input('mata_kuliah.nama'),
                'nilai' => $request->input('mata_kuliah.nilai')
            ];
            
            // Deteksi bahasa dan translasi
            $matkulName = $matkulBaru['nama'];
            $translatedName = $matkulName;

            try {
                // Gunakan Google Translate API
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
                        \Log::info("Translated: {$matkulName} -> {$translatedName}");
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Translation error: ' . $e->getMessage());
            }

            // Get WordNet synonyms
            $mapping = [$translatedName]; // Default include translated name
            try {
                $wordnetResponse = Http::get('http://wordnet.igsindonesia.org/synonyms', [
                    'word' => strtolower($translatedName)
                ]);

                if ($wordnetResponse->successful()) {
                    $wordnetSynonyms = $wordnetResponse->json();
                    if (!empty($wordnetSynonyms)) {
                        $mapping = array_merge($mapping, $wordnetSynonyms);
                    }
                    \Log::info("WordNet synonyms for {$translatedName}: " . json_encode($wordnetSynonyms));
                }
            } catch (\Exception $e) {
                \Log::error('WordNet error: ' . $e->getMessage());
                
                // Fallback: Tambahkan sinonim umum berdasarkan kata kunci
                $commonSynonyms = [
                    'programming' => ['coding', 'software development', 'computer programming'],
                    'database' => ['db', 'data management', 'data storage', 'dbms'],
                    'network' => ['networking', 'computer network', 'data communication'],
                    'algorithm' => ['algorithmic', 'computational method', 'problem solving'],
                    'security' => ['cybersecurity', 'information security', 'computer security'],
                    'system' => ['information system', 'computing system', 'it system'],
                    // ... tambahkan sinonim lainnya sesuai kebutuhan
                ];

                foreach ($commonSynonyms as $key => $values) {
                    if (stripos($translatedName, $key) !== false) {
                        $mapping = array_merge($mapping, $values);
                    }
                }
            }

            // Add original name to mapping
            $mapping[] = $matkulName;
            $mapping = array_unique(array_filter($mapping));

            // Prepare new mata kuliah data
            $newMatkul = [
                'nama' => $matkulName,
                'nama_en' => $translatedName,
                'nilai' => $matkulBaru['nilai'],
                'mapping' => $mapping
            ];

            // Get existing mata kuliah dan tambahkan yang baru
            $existingMatkul = $transkrip->mata_kuliah_transkrip ?? [];
            $existingMatkul[] = $newMatkul;

            // Update transkrip
            $transkrip->update([
                'mata_kuliah_transkrip' => $existingMatkul
            ]);

            \Log::info('Mata kuliah berhasil ditambahkan: ' . json_encode($newMatkul));

            return redirect()->route('input-transkrip')
                ->with('success', 'Mata kuliah berhasil ditambahkan');

        } catch (\Exception $e) {
            \Log::error('Error in store_matkul_transkrip: ' . $e->getMessage());
            return redirect()->route('input-transkrip')
                ->with('error', 'Terjadi kesalahan saat menambahkan mata kuliah');
        }
    }
    
    // Menampilkan file transkrip (pdf/gambar)
    public function view_transkrip($filename)
    {
        // Validasi akses
        $user = Auth::user();
        $transkrip = Transkrip::where('file', $filename)
                             ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
                             ->firstOrFail();
    
        $path = public_path('Data/Transkrip/' . $filename);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
        if ($fileExtension === 'pdf') {
            // Untuk file PDF, tampilkan menggunakan response()->file()
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        } else {
            // Untuk file gambar
            return response()->file($path, [
                'Content-Type' => mime_content_type($path)
            ]);
        }
    }
    
    // Menghapus file transkrip
    public function delete_transkrip($id)
    {
        $user = auth()->user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }
    
        $transkrip = Transkrip::where('id', $id)
                             ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
                             ->firstOrFail();
    
        // Hapus file fisik
        $path = public_path('Data/Transkrip/' . $transkrip->file);
        if (file_exists($path)) {
            unlink($path);
        }
    
        // Hapus record dari database
        $transkrip->delete();
    
        return redirect()->route('input-transkrip')
            ->with('success', 'Transkrip berhasil dihapus');
    }

    // Menampilkan daftar bukti alih jenjang
    public function bukti_alih_jenjang_view()
    {
        $user = Auth::user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }

        $bukti_list = bukti_alih_jenjang::where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('User.bukti-alih-jenjang', compact('bukti_list'));
    }

    // Menyimpan bukti alih jenjang baru
    public function bukti_alih_jenjang_add(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'jenis_dokumen' => 'required|string|in:Screenshot PDDIKTI,Panduan Kurikulum,Surat ket. pernah kuliah,Lainnya',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Pastikan direktori ada
            $uploadPath = public_path('Data/Bukti_alih_jenjang');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $file->move($uploadPath, $filename);

            bukti_alih_jenjang::create([
                'jenis_dokumen' => $request->jenis_dokumen,
                'calon_mahasiswa_id' => $user->calon_mahasiswa->id,
                'file' => $filename
            ]);

            return redirect()->route('bukti-alih-jenjang-view')
                ->with('success', 'Bukti berhasil ditambahkan');
        } catch (\Exception $e) {
            \Log::error('Error adding bukti: ' . $e->getMessage());
            return redirect()->route('bukti-alih-jenjang-view')
                ->with('error', 'Gagal menambahkan bukti: ' . $e->getMessage());
        }
    }

    // Menampilkan file bukti alih jenjang (pdf/gambar)
    public function bukti_alih_jenjang_view_file($filename)
    {
        $user = Auth::user();
        $bukti = bukti_alih_jenjang::where('file', $filename)
            ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
            ->firstOrFail();

        $path = public_path('Data/Bukti_alih_jenjang/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($fileExtension === 'pdf') {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        } else {
            return response()->file($path, [
                'Content-Type' => mime_content_type($path)
            ]);
        }
    }

    // Menghapus bukti alih jenjang
    public function bukti_alih_jenjang_delete($id)
    {
        $user = Auth::user();
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }

        try {
            $bukti = bukti_alih_jenjang::where('nomor_dokumen', $id)
                ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
                ->firstOrFail();

            // Hapus file fisik
            $path = public_path('Data/Bukti_alih_jenjang/' . $bukti->file);
            if (file_exists($path)) {
                unlink($path);
            }

            // Hapus record dari database
            $bukti->delete();

            return redirect()->route('bukti-alih-jenjang-view')
                ->with('success', 'Bukti berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Error deleting bukti: ' . $e->getMessage());
            return redirect()->route('bukti-alih-jenjang-view')
                ->with('error', 'Gagal menghapus bukti: ' . $e->getMessage());
        }
    }

    public function matkul_self_assessment_view(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'pendaftar' || !$user->calon_mahasiswa) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $calon_mahasiswa = $user->calon_mahasiswa;
        
        $existing_transkrip = Transkrip::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first();
        
        $assessment = \App\Models\Assessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first();
        
        $matkuls = Matkul::where('jurusan_id', $calon_mahasiswa->jurusan_id)->with('cpmk')->get();

        // Data penilaian per CPMK
        $allExistingCpmkAssessments = CpmkAssessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)
            ->get()->groupBy('matkul_id');

        // Data penilaian per Matkul (Mengajukan/Tidak)
        $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)
            ->get()->keyBy('matkul_id');

        $matkulsForJs = $matkuls->mapWithKeys(function ($matkul) {
            // ... (logika ini tidak perlu diubah)
            $cpmk_data = $matkul->cpmk;
            if ($cpmk_data && !$cpmk_data instanceof \Illuminate\Database\Eloquent\Collection) {
                $cpmk_data = collect([$cpmk_data]);
            }
            return [
                $matkul->id => [
                    'id' => $matkul->id,
                    'nama_matkul' => $matkul->nama_matkul,
                    'cpmk' => $cpmk_data ? $cpmk_data->map(fn($cpmk) => ['id' => $cpmk->id, 'penjelasan' => $cpmk->penjelasan])->values() : [] 
                ]
            ];
        });

        return view('User.matkul-self-assessment', compact(
            'matkuls', 
            'allExistingCpmkAssessments', 
            'matkulsForJs', 
            'assessment',
            'matkulAssessments',
            'existing_transkrip'
        ));
    }
    
    // Metode store_matkul_choice() tidak lagi dibutuhkan dan bisa dihapus.

    /**
     * Menyimpan pilihan "Mengajukan/Tidak" dan data CPMK sekaligus.
     */
    public function store_matkul_self_assessment(Request $request)
    {
        $user = auth()->user();
        $calon_mahasiswa = $user->calon_mahasiswa;

        $assessmentStatus = \App\Models\Assessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first();
        if ($assessmentStatus && $assessmentStatus->self_assessment_submitted_at) {
            return redirect()->back()->with('error', 'Self-assessment sudah pernah disubmit dan tidak dapat diubah lagi.');
        }

        $request->validate([
            'matkul_id' => 'required|exists:matkul,id',
            'choice' => 'required|string|in:Mengajukan,Tidak Mengajukan',
        ]);

        $matkul_id = $request->matkul_id;
        $choice = $request->choice;

        // Simpan pilihan utama (Mengajukan/Tidak Mengajukan)
        MatkulAssessment::updateOrCreate(
            [
                'calon_mahasiswa_id' => $calon_mahasiswa->id,
                'matkul_id' => $matkul_id,
            ],
            [
                'self_assessment_value' => $choice
            ]
        );

        // Jika memilih "Mengajukan", validasi dan simpan data CPMK
        if ($choice === 'Mengajukan') {
            $request->validate([
                'assessments' => 'required|array',
                'assessments.*.self_assessment_value' => 'required|string|in:Sangat Baik,Baik,Tidak Pernah',
            ]);

            foreach ($request->assessments as $cpmk_id => $data) {
                CpmkAssessment::updateOrCreate(
                    [
                        'calon_mahasiswa_id' => $calon_mahasiswa->id,
                        'matkul_id' => $matkul_id,
                        'cpmk_id' => $cpmk_id,
                    ],
                    [
                        'matkul_dasar' => $data['matkul_dasar'] ?? null,
                        'nilai_matkul_dasar' => $data['nilai_matkul_dasar'] ?? null,
                        'self_assessment_value' => $data['self_assessment_value'] ?? null,
                    ]
                );
            }
        } else {
            // Jika memilih "Tidak Mengajukan", hapus data CPMK yang mungkin sudah ada
            CpmkAssessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)
                          ->where('matkul_id', $matkul_id)
                          ->delete();
        }
        return redirect()->back()->with('success', 'Penilaian untuk mata kuliah berhasil disimpan.');
    }


    // Mengambil data CPMK berdasarkan matkul
    public function getCpmkByMatkul($matkulId)
    {
        \Log::info('Akses getCpmkByMatkul', ['matkul_id' => $matkulId]);
        try {
            $cpmks = Cpmk::where('matkul_id', $matkulId)->get();
            \Log::info('Data CPMK berhasil diambil', ['jumlah' => $cpmks->count()]);
            return response()->json($cpmks);
        } catch (\Exception $e) {
            \Log::error('Terjadi error di getCpmkByMatkul', ['error' => $e->getMessage(), 'matkul_id' => $matkulId]);
            return response()->json(['message' => 'Gagal mengambil data CPMK'], 500);
        }
    }

    // Menghapus data mata kuliah pada transkrip
    public function delete_matkul_transkrip($transkripId, $matkulIndex)
    {
        $user = auth()->user();
        // Ensure the user is a 'pendaftar'
        if ($user->role !== 'pendaftar') {
            abort(403, 'Unauthorized action');
        }

        try {
            $transkrip = Transkrip::where('id', $transkripId)
                               ->where('calon_mahasiswa_id', $user->calon_mahasiswa->id)
                               ->firstOrFail();

            $mataKuliahTranskrip = $transkrip->mata_kuliah_transkrip;

            // Check if the index is valid
            if (!is_array($mataKuliahTranskrip) || !isset($mataKuliahTranskrip[$matkulIndex])) {
                return response()->json(['success' => false, 'message' => 'Invalid Matkul index.'], 400);
            }

            // Remove the item from the array
            array_splice($mataKuliahTranskrip, $matkulIndex, 1);

            // Update the transkrip record
            $transkrip->mata_kuliah_transkrip = $mataKuliahTranskrip;
            $transkrip->save();

            return response()->json(['success' => true, 'message' => 'Mata kuliah berhasil dihapus.']);

        } catch (\Exception $e) {
            \Log::error('Error deleting matkul transkrip: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus mata kuliah.'], 500);
        }
    }

    /**
     * Handle pengajuan banding nilai oleh mahasiswa
     */
    public function submit_banding(Request $request)
    {
        // Mengajukan banding nilai untuk satu atau beberapa matkul
        $user = Auth::user();
        $camaba = $user->calon_mahasiswa;
        if (!$camaba) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan.');
        }
        $assessment = $camaba->assessment;
        if (!$assessment || !$assessment->published_at) {
            return redirect()->back()->with('error', 'Nilai belum dipublikasikan.');
        }
        $matkul_ids = $request->input('matkul_ids', []);
        $keterangan = $request->input('keterangan', []); // array: matkul_id => keterangan
        if (empty($matkul_ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu mata kuliah.');
        }
        // Cek apakah sudah pernah banding
        $sudah_banding = \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba->id)
            ->whereIn('matkul_id', $matkul_ids)
            ->where('is_banding', true)
            ->exists();
        if ($sudah_banding) {
            return redirect()->back()->with('error', 'Anda sudah pernah mengajukan banding untuk salah satu mata kuliah yang dipilih.');
        }
        // Update semua matkul_score yang dipilih
        foreach ($matkul_ids as $matkul_id) {
            $banding_ket = isset($keterangan[$matkul_id]) ? $keterangan[$matkul_id] : null;
            \App\Models\Matkul_score::where('calon_mahasiswa_id', $camaba->id)
                ->where('matkul_id', $matkul_id)
                ->update([
                    'is_banding' => true,
                    'banding_keterangan' => $banding_ket,
                    'banding_status' => 'pending',
                ]);
        }
        // Otomatisasi status RPL: update assessment->rpl_status menjadi 'banding'
        if ($assessment && $assessment->rpl_status !== 'banding') {
            $assessment->rpl_status = 'banding';
            $assessment->save();
        }
        return redirect()->back()->with('success', 'Pengajuan banding berhasil dikirim.');
    }

    // Submit self assessment matkul dan update status RPL
    public function submitMatkulSelfAssessment(Request $request)
    {
        $user = auth()->user();
        \Log::info('SubmitMatkulSelfAssessment dipanggil', ['user_id' => $user->id, 'role' => $user->role]);
        if ($user->role !== 'pendaftar' || !$user->calon_mahasiswa) {
            \Log::warning('Akses tidak diizinkan di submitMatkulSelfAssessment', ['user_id' => $user->id]);
            abort(403, 'Akses tidak diizinkan');
        }
        $calon_mahasiswa = $user->calon_mahasiswa;
        $assessment = \App\Models\Assessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first();
        
        // Cek apakah assessment sudah ada
        if (!$assessment) {
            return redirect()->back()->with('error', 'Anda belum diatur dalam assessment. Silakan hubungi admin.');
        }

        // Cek apakah sudah pernah submit
        if ($assessment->self_assessment_submitted_at) {
            return redirect()->back()->with('error', 'Self-assessment sudah pernah disubmit dan tidak dapat diubah.');
        }

        \Log::info('Assessment ditemukan sebelum update', ['assessment_id' => $assessment->id, 'rpl_status_before' => $assessment->rpl_status]);
        $assessment->rpl_status = 'penilaian assessor';
        $assessment->self_assessment_submitted_at = now(); // Catat waktu submit
        $assessment->save();
        \Log::info('Assessment diupdate', ['assessment_id' => $assessment->id, 'rpl_status_after' => $assessment->rpl_status]);
        return redirect()->route('matkul-self-assessment-view')->with('success', 'Self-assessment berhasil disubmit. Status RPL: Penilaian Assessor.');
    }
}