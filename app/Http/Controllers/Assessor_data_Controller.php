<?php

namespace App\Http\Controllers;
// Model yang digunakan controller ini
use App\Models\Assessor;
use App\Models\Jurusan;
use App\Models\Assessment;
use App\Models\Matkul;
use App\Models\Matkul_score;
use App\Models\Transkrip;
use App\Models\Cpmk;
use App\Models\Self_assessment_camaba;
use App\Models\Calon_mahasiswa;
use App\Models\bukti_alih_jenjang;
use App\Models\Periode;
use App\Models\CpmkAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MatkulAssessment;

// Controller untuk mengelola data dan aktivitas assessor
class Assessor_data_Controller extends Controller
{
    // Menampilkan daftar mahasiswa yang dinilai oleh assessor pada periode aktif
    public function list_name_table(Request $request)
    {
        try {
            // Ambil user yang sedang login
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
            }

            // Cari data assessor berdasarkan user
            $assessor = Assessor::where('user_id', $user->id)->first();
            if ($assessor) {
                // Ambil semua periode yang aktif
                $active_periodes = Periode::where('is_active', true)->get();
                
                if ($active_periodes->isEmpty()) {
                    return redirect()->back()->with('warning', 'Tidak ada periode yang aktif saat ini.');
                }

                // Ambil semua assessment yang melibatkan assessor ini
                $assessments = Assessment::where(function ($query) use ($assessor) {
                    $query->where('assessor_id_1', $assessor->id)
                        ->orWhere('assessor_id_2', $assessor->id)
                        ->orWhere('assessor_id_3', $assessor->id);
                })->get();

                $mahasiswaIds = $assessments->pluck('calon_mahasiswa_id');
                
                // Filter calon mahasiswa berdasarkan periode aktif
                $camaba = Calon_mahasiswa::with(['jurusan', 'assessment'])
                    ->whereIn('id', $mahasiswaIds)
                    ->whereIn('periode_id', $active_periodes->pluck('id'))
                    ->get();

                // Log untuk debugging (bisa dihapus di produksi)
                \Log::info('Mahasiswa IDs:', ['ids' => $mahasiswaIds->toArray()]);
                \Log::info('Camaba count:', ['count' => $camaba->count()]);

                return view('Assessor/list-name-table', compact('camaba'));
            }

            return redirect()->back()->with('error', 'Data assessor tidak ditemukan');
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            \Log::error('Terjadi error di list_name_table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menampilkan detail mahasiswa yang dinilai oleh assessor
public function detail_user($id)
{
    try {
        $user = Auth::user();
        if (!$user || !$user->assessor) {
            return redirect()->route('login')->with('error', 'Data asesor tidak ditemukan atau Anda belum login.');
        }

        $assessor = $user->assessor;
        $camaba = Calon_mahasiswa::with(['jurusan', 'assessment', 'ijazah', 'bukti_alih_jenjang', 'transkrip'])->findOrFail($id);
        
        $existing_transkrip = Transkrip::where('calon_mahasiswa_id', $camaba->id)->first();
        
        // --- LOGIKA PENENTUAN STATUS SUBMIT ---
        $isSubmitted = false;
        if ($camaba->assessment) {
            if ($camaba->assessment->assessor_id_1 == $assessor->id && $camaba->assessment->assessor_1_submitted_at) {
                $isSubmitted = true;
            } elseif ($camaba->assessment->assessor_id_2 == $assessor->id && $camaba->assessment->assessor_2_submitted_at) {
                $isSubmitted = true;
            } elseif ($camaba->assessment->assessor_id_3 == $assessor->id && $camaba->assessment->assessor_3_submitted_at) {
                $isSubmitted = true;
            }
        }
        // --- AKHIR LOGIKA PENENTUAN STATUS SUBMIT ---

        $allMatkulJurusan = Matkul::with('cpmk')->where('jurusan_id', $camaba->jurusan_id)->get();
        $cpmkAssessments = CpmkAssessment::where('calon_mahasiswa_id', $id)
            ->with('cpmk')->get()->groupBy('matkul_id');
        $allAssessorScores = Matkul_score::where('calon_mahasiswa_id', $id)
                                        ->with('assessor')->get()->groupBy('matkul_id');
        $assignedAssessors = collect();
        if ($camaba->assessment) {
            $assessorIds = array_filter([
                $camaba->assessment->assessor_id_1,
                $camaba->assessment->assessor_id_2,
                $camaba->assessment->assessor_id_3
            ]);
            $assignedAssessors = Assessor::whereIn('id', $assessorIds)->get()->keyBy('id');
        }
        
        $matkulWithStatus = $allMatkulJurusan->map(function ($mk) use ($allAssessorScores, $assignedAssessors) {
            $scoresForThisMatkul = $allAssessorScores->get($mk->id);
            if ($scoresForThisMatkul && $scoresForThisMatkul->isNotEmpty()) {
                $firstScore = $scoresForThisMatkul->first();
                $mk->isLolos = ($firstScore->status === 'Lolos');
                $mk->isComplete = true; 
                $mk->finalScore = $firstScore->nilai_akhir;
                $mk->completedAssessmentsCount = $scoresForThisMatkul->count();
            } else {
                $mk->isLolos = false;
                $mk->isComplete = false;
                $mk->finalScore = null;
                $mk->completedAssessmentsCount = 0;
            }
            $mk->requiredAssessments = $assignedAssessors->count();
            return $mk;
        });
        
        $matkulAssessmentsLama = MatkulAssessment::where('calon_mahasiswa_id', $id)->get();

        return view('Assessor.detail-user', [
            'camaba' => $camaba,
            'assessor' => $assessor,
            'matkul' => $matkulWithStatus,
            'matkul2' => $allMatkulJurusan,
            'existing_transkrip' => $existing_transkrip, // Ini baris yang diperbaiki
            'rekomendasi' => [],
            'matkulAssessments' => $matkulAssessmentsLama,
            'cpmkAssessments' => $cpmkAssessments,
            'allAssessorScores' => $allAssessorScores,
            'assignedAssessors' => $assignedAssessors,
            'isSubmitted' => $isSubmitted
        ]);

    } catch (\Exception $e) {
        Log::error('Terjadi error di detail_user: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    /**
     * Menyimpan nilai numerik per CPMK dari assessor. (VERSI PERBAIKAN FINAL)
     */
    public function store_cpmk_scores(Request $request)
    {
        $request->validate([
            'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
            'matkul_id' => 'required|exists:matkul,id',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $assessor = Auth::user()->assessor;
        $camabaId = $request->calon_mahasiswa_id;
        $matkulId = $request->matkul_id;

        $assessment = Assessment::where('calon_mahasiswa_id', $camabaId)->firstOrFail();
        
        $assessorSlot = null;
        if ($assessment->assessor_id_1 == $assessor->id) $assessorSlot = 'nilai_assessor1';
        elseif ($assessment->assessor_id_2 == $assessor->id) $assessorSlot = 'nilai_assessor2';
        elseif ($assessment->assessor_id_3 == $assessor->id) $assessorSlot = 'nilai_assessor3';

        if (!$assessorSlot) {
            return redirect()->back()->with('error', 'Anda tidak ditugaskan untuk menilai mahasiswa ini.');
        }

        foreach ($request->scores as $cpmkId => $score) {
            CpmkAssessment::updateOrCreate(
                ['calon_mahasiswa_id' => $camabaId, 'matkul_id' => $matkulId, 'cpmk_id' => $cpmkId],
                [$assessorSlot => $score]
            );
        }
        
        $avgScoreCurrentAssessor = array_sum($request->scores) / count($request->scores);

        $allAssessorScores = Matkul_score::where('calon_mahasiswa_id', $camabaId)
            ->where('matkul_id', $matkulId)
            ->where('assessor_id', '!=', $assessor->id)
            ->pluck('nilai');
        
        $allAssessorScores->push($avgScoreCurrentAssessor);

        $finalScore = $allAssessorScores->avg();
        $status = $finalScore >= 75 ? 'Lolos' : 'Gagal';

        Matkul_score::updateOrCreate(
            ['calon_mahasiswa_id' => $camabaId, 'matkul_id' => $matkulId, 'assessor_id' => $assessor->id],
            ['nilai' => $avgScoreCurrentAssessor, 'status' => $status, 'nilai_akhir' => $finalScore]
        );

        Matkul_score::where('calon_mahasiswa_id', $camabaId)
            ->where('matkul_id', $matkulId)
            ->where('assessor_id', '!=', $assessor->id)
            ->update(['status' => $status, 'nilai_akhir' => $finalScore]);
        
        return redirect()->back()->with('success', 'Nilai untuk mata kuliah berhasil disimpan.');
    }

    // public function form_user($matkul_id){
    //     $matkul = Matkul::findOrFail($matkul_id);

    //     $self_assessment_camaba = Self_assessment_camaba::with(['bukti', 'cpmk'])
    //         ->whereHas('cpmk', function ($query) use ($matkul_id) {
    //             $query->where('matkul_id', $matkul_id);
    //         })
    //         ->get();

    //     return view('Assessor/form-user', compact('matkul', 'self_assessment_camaba'));
    // }
    
    // public function form_user($matkul_id){
    //     $matkul = Matkul::findOrFail($matkul_id);
    
    //     $self_assessment_camaba = Self_assessment_camaba::with('cpmk')
    //         ->whereHas('cpmk', function ($query) use ($matkul_id) {
    //             $query->where('matkul_id', $matkul_id);
    //         })
    //         ->get();
    
    //     // Ubah cara mendapatkan assessor_id
    //     $user = auth()->user();
    //     $assessor = Assessor::where('user_id', $user->id)->first();
    //     $assessor_id = $assessor->id; // Ini akan mengambil id dari tabel assessors
            
    //     $camaba = $self_assessment_camaba->first()->calon_mahasiswa;
    //     $calon_mahasiswa_id = $camaba->id;
        
    //     // Tambahkan query untuk mengambil matkul score
    //     $matkulScore = Matkul_score::where([
    //         'matkul_id' => $matkul_id,
    //         'assessor_id' => $assessor_id,
    //         'calon_mahasiswa_id' => $calon_mahasiswa_id
    //     ])->first();
        
    //     return view('Assessor/form-user', compact('matkul', 'self_assessment_camaba', 'camaba', 'calon_mahasiswa_id', 'assessor_id', 'matkulScore'));
    // }
    // Menghitung dan menyimpan status kelulusan matkul berdasarkan penilaian assessor
    public function input_calculate(Request $request)
    {
        try {
            Log::info('Request data:', [
                'assessor_id' => $request->assessor_id,
                'all_data' => $request->all()
            ]);
    
            // Validasi input
            $validated = $request->validate([
                'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
                'matkul_id' => 'required|exists:matkul,id',
                'assessor_id' => 'required|exists:assessor,id',
            ]);
    
            // Cari MatkulAssessment untuk mata kuliah dan mahasiswa ini
            $matkulAssessment = \App\Models\MatkulAssessment::where([
                'matkul_id' => $request->matkul_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$matkulAssessment) {
                 // Jika MatkulAssessment tidak ada, kita tidak bisa menghitung status berdasarkan penilaian
                 // Kita bisa menangani kasus ini, misalnya dengan mengatur status menjadi 'Belum Dinilai' atau sejenisnya
                $status = 'Belum Dinilai';
                $percentage = 0;
            } else {
                // Hitung status berdasarkan penilaian asesor
                $assignedAssessorsCount = 0;
                $completedAssessmentsCount = 0;
                $positiveAssessmentsCount = 0;
                $requiredAssessments = 0;
                
                // Hitung asesor yang ditugaskan
                if ($matkulAssessment->assessor1_id) $assignedAssessorsCount++;
                if ($matkulAssessment->assessor2_id) $assignedAssessorsCount++;
                if ($matkulAssessment->assessor3_id) $assignedAssessorsCount++;

                $requiredAssessments = $assignedAssessorsCount;

                // Hitung penilaian yang selesai dan positif (hanya asesor)
                if ($matkulAssessment->assessor1_assessment) {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor1_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                if ($matkulAssessment->assessor2_assessment) {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor2_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                if ($matkulAssessment->assessor3_assessment) {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor3_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                // Tentukan penyelesaian
                $isComplete = ($requiredAssessments > 0) && ($completedAssessmentsCount === $requiredAssessments);

                // Hitung persentase dan status Lolos/Gagal hanya jika sudah selesai
                $percentage = $isComplete && $requiredAssessments > 0 ? ($positiveAssessmentsCount / $requiredAssessments) * 100 : 0;
                $status = $isComplete ? ($percentage >= 50 ? 'Lolos' : 'Gagal') : 'Menunggu Penilaian';
            }

            // Simpan ke database
            $result = Matkul_score::updateOrCreate(
                [
                    'matkul_id' => $request->matkul_id,
                    'assessor_id' => $request->assessor_id,
                    'calon_mahasiswa_id' => $request->calon_mahasiswa_id,
                ],
                [
                    'status' => $status,
                    'score' => $percentage,
                    'updated_at' => now()
                ]
            );

            // Redirect berdasarkan status
            return redirect()->route('detail-user', ['id' => $request->calon_mahasiswa_id])
                ->with('success', 'Status penilaian berhasil diperbarui. Status: ' . $status);

        } catch (\Exception $e) {
            Log::error('Error saving score: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menyimpan nilai numerik matkul, hanya jika status sudah Lolos
    public function input_nilai_matkul(Request $request)
    {
        try {
            // Log data request untuk debugging
            Log::info('Request data:', [
                'assessor_id' => $request->assessor_id,
                'all_data' => $request->all()
            ]);

            // Validasi input
            $validated = $request->validate([
                'nilai' => 'required|integer|min:0|max:100',
                'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
                'matkul_id' => 'required|exists:matkul,id',
                'assessor_id' => 'required|exists:assessor,id',
            ]);

            // Periksa apakah mata kuliah telah melewati tahap penilaian
            $matkulAssessment = \App\Models\MatkulAssessment::where([
                'matkul_id' => $request->matkul_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$matkulAssessment) {
                throw new \Exception('Data penilaian tidak ditemukan untuk mata kuliah ini.');
            }

            // Dapatkan status saat ini dari Matkul_score
            $currentScore = \App\Models\Matkul_score::where([
                'matkul_id' => $request->matkul_id,
                'assessor_id' => $request->assessor_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$currentScore || $currentScore->status !== 'Lolos') {
                throw new \Exception('Tidak dapat memasukkan nilai numerik untuk mata kuliah yang belum lulus tahap penilaian.');
            }

            // Perbarui atau buat catatan Matkul_score
            $matkulScore = \App\Models\Matkul_score::updateOrCreate(
                [
                    'matkul_id' => $request->matkul_id,
                    'assessor_id' => $request->assessor_id,
                    'calon_mahasiswa_id' => $request->calon_mahasiswa_id,
                ],
                [
                    'nilai' => $request->nilai,
                    'status' => 'Lolos', // Pertahankan status sebagai Lolos
                    'updated_at' => now()
                ]
            );

            // Arahkan kembali ke halaman detail untuk menampilkan nilai yang diperbarui
            return redirect()->route('detail-user', ['id' => $request->calon_mahasiswa_id])
                ->with('success', 'Nilai berhasil disimpan.');
            
        } catch (\Exception $e) {
            Log::error('Error menyimpan nilai: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Menyimpan nilai matkul secara langsung oleh assessor
    public function handleMatkulInput(Request $request)
    {
        try {
            // Dapatkan assessor_id yang benar
            $user = Auth::user();
            $assessor = Assessor::where('user_id', $user->id)->first();
            
            if (!$assessor) {
                throw new \Exception('Assessor tidak ditemukan');
            }
    
            $validated = $request->validate([
                'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
                'matkul_id' => 'required|exists:matkul,id',
                'nilai' => 'required|numeric'
            ]);
    
            $result = Matkul_score::updateOrCreate(
                [
                    'matkul_id' => $request->matkul_id,
                    'assessor_id' => $assessor->id, // Gunakan assessor_id yang benar
                    'calon_mahasiswa_id' => $request->calon_mahasiswa_id,
                ],
                [
                    'status' => 'Lolos',
                    'nilai' => $request->nilai,
                    'score' => 80,
                    'updated_at' => now(),
                ]
            );
    
            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // Menampilkan daftar matkul yang sudah dinilai oleh assessor untuk mahasiswa tertentu
    public function showMatkulList($calon_mahasiswa_id)
    {
        // Ambil mata kuliah yang sudah dinilai oleh assessor saat ini
        $matkul_dinilai = Matkul_score::with('matkul') // Pastikan relasi 'matkul' didefinisikan di model
            ->where('calon_mahasiswa_id', $calon_mahasiswa_id)
            ->where('assessor_id', auth()->user()->id)
            ->get();
    
        // Data untuk dropdown tambah mata kuliah
        $matkul = Matkul::all();
    
        // Calon mahasiswa terkait
        $camaba = CalonMahasiswa::findOrFail($calon_mahasiswa_id);
    
        return view('nama_view', compact('matkul_dinilai', 'matkul', 'camaba'));
    }

    // Menampilkan profil assessor yang sedang login
    public function profile_view_assessor()
    {
        $user = Auth::user();
    
        // Pastikan user login ada
        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }
    
        // Pastikan assessor ditemukan
        $assessor = Assessor::where('user_id', $user->id)->first();
        if (!$assessor) {
            return redirect()->route('profile-view-assessor')->with('error', 'Assessor tidak ditemukan.');
        }
    
        return view('Assessor/profile-view-assessor', compact('assessor', 'user'));
    }

    // Menampilkan halaman edit profil assessor
    public function profile_assessor_edit_view($id){
        $assessor = Assessor::findOrFail($id);
        return view('Assessor/profile-edit-assessor', compact('assessor'));
    }
    
    // Menyimpan perubahan data profil assessor
    public function profile_edit_assessor(Request $request, $id){
        $request->validate([
            'nama' => 'required|string|max:225',
            'alamat' => 'required|string|max:225',
            'no_hp' => 'required|string|max:225',
            'foto' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi untuk gambar
        ]);

        $assessor = Assessor::findOrFail($id);

        if ($request->hasFile('foto')) {
        $imageName = time().'.'.$request->foto->extension();
        $request->foto->move(public_path('Data/profile_pict_assesor'), $imageName);
    
        // Hapus gambar lama jika ada
        if ($assessor->foto) {
            Storage::delete('Data/profile_pict_assesor/' . $assessor->foto);
        }
    
        $assessor->foto = $imageName;
        }

        $assessor->update($request->except('foto'));

        return redirect()->route('profile-view-assessor');
    }

    // Menampilkan file transkrip mahasiswa (pdf/gambar)
    public function view_transkrip($filename)
    {
        // Cari transkrip berdasarkan filename
        $transkrip = Transkrip::where('file', $filename)->firstOrFail();
    
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

    // Fungsi untuk menghitung kemiripan Jaccard antara dua set kata
    private function hitungJaccardSimilarity($set1, $set2) 
    {
        // Pastikan kedua set adalah array dan hapus duplikat
        $set1 = array_unique(array_filter($set1));
        $set2 = array_unique(array_filter($set2));

        if (empty($set1) || empty($set2)) {
            return 0;
        }

        // Ubah semua elemen menjadi huruf kecil untuk perbandingan
        $set1 = array_map('mb_strtolower', $set1);
        $set2 = array_map('mb_strtolower', $set2);

        $intersection = array_intersect($set1, $set2);
        $union = array_unique(array_merge($set1, $set2));

        return count($intersection) / count($union);
    }

    // Fungsi untuk menghitung kemiripan Cosine antara dua set kata
    private function hitungCosineSimilarity($set1, $set2) 
    {
        // Pastikan kedua set adalah array dan hapus duplikat
        $set1 = array_unique(array_filter($set1));
        $set2 = array_unique(array_filter($set2));

        if (empty($set1) || empty($set2)) {
            return 0;
        }

        // Ubah semua elemen menjadi huruf kecil untuk perbandingan
        $set1 = array_map('mb_strtolower', $set1);
        $set2 = array_map('mb_strtolower', $set2);

        // Buat vektor
        $allTerms = array_unique(array_merge($set1, $set2));
        $vector1 = array_fill_keys($allTerms, 0);
        $vector2 = array_fill_keys($allTerms, 0);

        // Hitung frekuensi istilah
        foreach ($set1 as $term) {
            $vector1[$term]++;
        }
        foreach ($set2 as $term) {
            $vector2[$term]++;
        }

        // Hitung produk titik dan magnitudo
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        foreach ($allTerms as $term) {
            $dotProduct += $vector1[$term] * $vector2[$term];
            $magnitude1 += $vector1[$term] * $vector1[$term];
            $magnitude2 += $vector2[$term] * $vector2[$term];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    // Menampilkan file bukti alih jenjang (pdf/gambar)
    public function view_bukti_alih_jenjang($filename)
    {
        // Cari bukti berdasarkan filename
        $bukti = bukti_alih_jenjang::where('file', $filename)->firstOrFail();
    
        $path = public_path('Data/Bukti_alih_jenjang/' . $filename);
    
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

    // Mengunduh file bukti alih jenjang
    public function download_bukti_alih_jenjang($filename)
    {
        // Cari bukti berdasarkan filename
        $bukti = bukti_alih_jenjang::where('file', $filename)->firstOrFail();
    
        $path = public_path('Data/Bukti_alih_jenjang/' . $filename);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->download($path, $filename);
    }

    // Menyimpan penilaian dan nilai matkul oleh assessor (penilaian kolektif)
    public function saveMatkulAssessorAssessment(Request $request)
    {
        \Log::info('Memulai penilaian terpadu dan penyimpanan nilai', [
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
    
        try {
            $assessorId = (int)$request->input('assessor_id');
            $calonMahasiswaId = $request->input('calon_mahasiswa_id');
            $assessmentsData = $request->input('assessments', []);
    
            if (empty($assessmentsData)) {
                return redirect()->back()->with('warning', 'Tidak ada data penilaian yang dikirim.');
            }
    
            $matkulIdsToUpdate = [];
    
            // TAHAP 1: Simpan Penilaian Asesor (Radio Buttons)
            foreach ($assessmentsData as $assessmentId => $data) {
                if (empty($data['value'])) continue;
    
                $matkulAssessment = MatkulAssessment::find($data['matkul_assessment_id']);
                if (!$matkulAssessment) continue;
    
                $assessorSlot = null;
                if ($matkulAssessment->assessor1_id == $assessorId) $assessorSlot = 'assessor1_assessment';
                elseif ($matkulAssessment->assessor2_id == $assessorId) $assessorSlot = 'assessor2_assessment';
                elseif ($matkulAssessment->assessor3_id == $assessorId) $assessorSlot = 'assessor3_assessment';
    
                if ($assessorSlot) {
                    $matkulAssessment->$assessorSlot = $data['value'];
                    $matkulAssessment->save();
                    $matkulIdsToUpdate[] = $matkulAssessment->matkul_id;
                }
            }
    
            // TAHAP 2: Perbarui Status Kelulusan Kolektif (Lolos/Gagal)
            foreach (array_unique($matkulIdsToUpdate) as $matkulId) {
                $this->updateMatkulStatus($calonMahasiswaId, $matkulId);
            }
    
            // TAHAP 3: Simpan atau Perbarui Nilai Numerik Secara Independen
            foreach ($assessmentsData as $assessmentId => $data) {
                if (!isset($data['nilai']) || $data['nilai'] === null || $data['nilai'] === '') {
                    continue;
                }
    
                $matkulAssessment = MatkulAssessment::find($data['matkul_assessment_id']);
                if (!$matkulAssessment) {
                    continue;
                }
    
                Matkul_score::updateOrCreate(
                    [
                        'matkul_id' => $matkulAssessment->matkul_id,
                        'assessor_id' => $assessorId,
                        'calon_mahasiswa_id' => $calonMahasiswaId,
                    ],
                    [
                        'nilai' => $data['nilai'],
                    ]
                );
    
                \Log::info('Nilai numerik untuk asesor berhasil disimpan/diperbarui.', [
                    'matkul_id' => $matkulAssessment->matkul_id, 
                    'assessor_id' => $assessorId,
                    'nilai' => $data['nilai']
                ]);
            }
    
            return redirect()->back()->with('success', 'Penilaian dan nilai berhasil disimpan.');
    
        } catch (\Exception $e) {
            \Log::error('Error in saveMatkulAssessorAssessment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    // Mengupdate status kelulusan matkul berdasarkan penilaian assessor
    private function updateMatkulStatus($calonMahasiswaId, $matkulId)
    {
        $matkulAssessment = \App\Models\MatkulAssessment::where([
            'matkul_id' => $matkulId,
            'calon_mahasiswa_id' => $calonMahasiswaId
        ])->first();

        if (!$matkulAssessment) {
            return;
        }

        $assignedAssessorsCount = 0;
        $completedAssessmentsCount = 0;
        $positiveAssessmentsCount = 0;

        // Hitung jumlah assessor yang ditugaskan
        if ($matkulAssessment->assessor1_id) $assignedAssessorsCount++;
        if ($matkulAssessment->assessor2_id) $assignedAssessorsCount++;
        if ($matkulAssessment->assessor3_id) $assignedAssessorsCount++;

        // Hitung penilaian selesai dan positif dari assessor
        if ($matkulAssessment->assessor1_assessment) {
            $completedAssessmentsCount++;
            if (in_array($matkulAssessment->assessor1_assessment, ['Baik', 'Sangat Baik'])) {
                $positiveAssessmentsCount++;
            }
        }

        if ($matkulAssessment->assessor2_assessment) {
            $completedAssessmentsCount++;
            if (in_array($matkulAssessment->assessor2_assessment, ['Baik', 'Sangat Baik'])) {
                $positiveAssessmentsCount++;
            }
        }

        if ($matkulAssessment->assessor3_assessment) {
            $completedAssessmentsCount++;
            if (in_array($matkulAssessment->assessor3_assessment, ['Baik', 'Sangat Baik'])) {
                $positiveAssessmentsCount++;
            }
        }

        // Tentukan apakah semua penilaian yang dibutuhkan sudah lengkap
        $requiredAssessments = $assignedAssessorsCount;
        $isComplete = ($requiredAssessments > 0) && ($completedAssessmentsCount === $requiredAssessments);

        // Hitung persentase dan tentukan status
        $percentage = $isComplete && $requiredAssessments > 0 ? ($positiveAssessmentsCount / $requiredAssessments) * 100 : 0;
        $status = $isComplete ? ($percentage >= 50 ? 'Lolos' : 'Gagal') : 'Menunggu Penilaian';

        // Perbarui catatan Matkul_score untuk semua assessor
        $assessors = [$matkulAssessment->assessor1_id, $matkulAssessment->assessor2_id, $matkulAssessment->assessor3_id];
        foreach ($assessors as $assessorId) {
            if ($assessorId) {
                \App\Models\Matkul_score::updateOrCreate(
                    [
                        'matkul_id' => $matkulId,
                        'assessor_id' => $assessorId,
                        'calon_mahasiswa_id' => $calonMahasiswaId,
                    ],
                    [
                        'status' => $status,
                        'score' => $percentage,
                        'updated_at' => now()
                    ]
                );
            }
        }
    }

    // Mengambil data CPMK berdasarkan matkul
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

    // Mengunduh file ijazah mahasiswa
    public function downloadIjazah($calon_mahasiswa_id)
    {
        try {
            // Temukan data calon mahasiswa
            $camaba = \App\Models\Calon_mahasiswa::findOrFail($calon_mahasiswa_id);

            // Pastikan calon mahasiswa memiliki data ijazah
            if (!$camaba->ijazah || !$camaba->ijazah->file) {
                return back()->with('error', 'File ijazah mahasiswa tidak ditemukan.');
            }

            $filePath = $camaba->ijazah->file;
            $path = public_path($filePath);

            // Periksa apakah file ada di server
            if (!file_exists($path)) {
                return back()->with('error', 'File ijazah tidak ditemukan di server.');
            }

            $filename = basename($filePath);
            $mimeType = mime_content_type($path);

            return response()->download($path, $filename, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error downloading ijazah for assessor: ' . $e->getMessage(), ['calon_mahasiswa_id' => $calon_mahasiswa_id]);
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file ijazah. Silakan coba lagi.');
        }
    }

    // Submit penilaian akhir oleh assessor, cek semua matkul sudah dinilai
    public function submitFinalAssessment(Request $request, $camaba_id)
    {
        try {
            $assessor = Auth::user()->assessor;
            if (!$assessor) {
                return redirect()->back()->with('error', 'Data asesor tidak ditemukan.');
            }
    
            $assessment = Assessment::where('calon_mahasiswa_id', $camaba_id)->firstOrFail();
    
            // JIKA BUKAN SUBMIT PAKSA, LAKUKAN VALIDASI
            if (!$request->has('force_submit')) {
                // Cek mata kuliah yang belum dinilai oleh asesor ini
                $matkuls = Matkul::where('jurusan_id', $assessment->jurusan_id)->get();
                $unassessedMatkulIds = collect();
    
                foreach ($matkuls as $matkul) {
                    // Periksa apakah ada skor untuk asesor ini di matkul_score
                    $scoreExists = Matkul_score::where('calon_mahasiswa_id', $camaba_id)
                        ->where('matkul_id', $matkul->id)
                        ->where('assessor_id', $assessor->id)
                        ->whereNotNull('nilai') // Pastikan kolom nilai tidak null
                        ->exists();
    
                    if (!$scoreExists) {
                        $unassessedMatkulIds->push($matkul->nama_matkul);
                    }
                }
    
                if ($unassessedMatkulIds->isNotEmpty()) {
                    $request->session()->flash('unassessed_matkuls', $unassessedMatkulIds->toArray());
                    return redirect()->back()->with('show_warning_modal', true);
                }
            }
    
            // --- PROSES SUBMIT (BAIK NORMAL MAUPUN PAKSA) ---
            $assessor_slot = null;
            if ($assessment->assessor_id_1 == $assessor->id) $assessor_slot = 'assessor_1_submitted_at';
            elseif ($assessment->assessor_id_2 == $assessor->id) $assessor_slot = 'assessor_2_submitted_at';
            elseif ($assessment->assessor_id_3 == $assessor->id) $assessor_slot = 'assessor_3_submitted_at';
    
            if ($assessor_slot) {
                if (is_null($assessment->$assessor_slot)) {
                    $assessment->$assessor_slot = now();
                    
                    if ($this->checkAllAssessorsSubmitted($assessment) && $assessment->rpl_status === 'penilaian assessor') {
                        $assessment->rpl_status = 'ditinjau admin';
                    }
                    
                    $assessment->save();
                    return redirect()->back()->with('success', 'Penilaian akhir berhasil disubmit.');
                } else {
                    return redirect()->back()->with('warning', 'Anda sudah pernah submit penilaian akhir sebelumnya.');
                }
            } else {
                return redirect()->back()->with('error', 'Anda tidak ditugaskan untuk mahasiswa ini.');
            }
    
        } catch (\Exception $e) {
            \Log::error('Error submitting final assessment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Check if all assigned assessors have submitted their final assessment
     */
    private function checkAllAssessorsSubmitted($assessment)
    {
        $assignedAssessors = [];
        
        // Hitung assessor yang ditugaskan
        if ($assessment->assessor_id_1) $assignedAssessors[] = 'assessor_1_submitted_at';
        if ($assessment->assessor_id_2) $assignedAssessors[] = 'assessor_2_submitted_at';
        if ($assessment->assessor_id_3) $assignedAssessors[] = 'assessor_3_submitted_at';
        
        // Periksa apakah semua assessor yang ditugaskan sudah submit
        foreach ($assignedAssessors as $slot) {
            if (is_null($assessment->$slot)) {
                return false;
            }
        }
        
        return true;
    }
}
