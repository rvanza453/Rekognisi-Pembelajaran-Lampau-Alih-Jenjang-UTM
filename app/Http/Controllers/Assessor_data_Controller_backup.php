<?php

namespace App\Http\Controllers;
use App\Models\Assessor;
use App\Models\Jurusan;
use App\Models\Assessment;
use App\Models\Matkul;
use App\Models\Matkul_score;
use App\Models\Transkrip;
use App\Models\Cpmk;
use App\Models\Self_assessment_camaba;
use App\Models\Calon_mahasiswa;
use App\Models\Bukti_alih_jenjang;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\MatkulAssessment;

class Assessor_data_Controller extends Controller
{
    public function list_name_table(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
            }

            $assessor = Assessor::where('user_id', $user->id)->first();
            if ($assessor) {
                $active_periodes = Periode::where('is_active', true)->get();
                
                if ($active_periodes->isEmpty()) {
                    return redirect()->back()->with('warning', 'Tidak ada periode yang aktif saat ini.');
                }

                $assessments = Assessment::where(function ($query) use ($assessor) {
                    $query->where('assessor_id_1', $assessor->id)
                        ->orWhere('assessor_id_2', $assessor->id)
                        ->orWhere('assessor_id_3', $assessor->id);
                })->get();

                $mahasiswaIds = $assessments->pluck('calon_mahasiswa_id');
                
                $camaba = Calon_mahasiswa::whereIn('id', $mahasiswaIds)
                    ->whereIn('periode_id', $active_periodes->pluck('id'))
                    ->get();

                \Log::info('Mahasiswa IDs (list_name_table):', ['ids' => $mahasiswaIds->toArray()]);
                \Log::info('Camaba count (list_name_table):', ['count' => $camaba->count()]);

                return view('Assessor/list-name-table', compact('camaba'));
            }

            return redirect()->back()->with('error', 'Data assessor tidak ditemukan');
        } catch (\Exception $e) {
            \Log::error('Error in list_name_table: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail_user($id) // $id adalah calon_mahasiswa_id
    {
        $loggedInAssessor = Assessor::where('user_id', $user->id)->first();
        try {
            Log::info('Accessing detail_user with Calon Mahasiswa ID: ' . $id);
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
            }

            $loggedInAssessor = Assessor::where('user_id', $user->id)->first();
            if (!$loggedInAssessor) {
                Log::error('Assessor data not found for logged in user.', ['user_id' => $user->id]);
                return redirect()->back()->with('error', 'Data asesor Anda tidak ditemukan.');
            }
            Log::info('Logged-in Assessor ID for detail_user:', ['assessor_id' => $loggedInAssessor->id]);

            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip', 'bukti_alih_jenjang'])->findOrFail($id);
            
            // Ambil SEMUA mata kuliah dari jurusan yang dituju oleh camaba
            // Ini akan menjadi dasar untuk $matkulWithStatus
            $allMatkulJurusan = Matkul::where('jurusan_id', $camaba->jurusan_id)->get();

            // Ambil SEMUA MatkulAssessment untuk mahasiswa ini, beserta relasi matkul
            // Ini PENTING untuk tab "Penilaian Self-Assessment Mata Kuliah"
            $matkulAssessmentsFromDB = MatkulAssessment::where('calon_mahasiswa_id', $id)
                                ->with('matkul')
                                ->get();
            Log::info('MatkulAssessments fetched for detail_user (raw from DB):', $matkulAssessmentsFromDB->toArray());


            // Proses $allMatkulJurusan untuk menambahkan status penilaian (isComplete, isLolos, dll.)
            // Ini digunakan untuk tab "Penilaian Konversi"
            $matkulWithStatus = $allMatkulJurusan->map(function ($mk) use ($camaba, $matkulAssessmentsFromDB) {
                // Cari MatkulAssessment yang sesuai untuk matkul saat ini ($mk)
                $matkulAssessmentInstance = $matkulAssessmentsFromDB->firstWhere('matkul_id', $mk->id);

                $assignedAssessorsCount = 0;
                $completedAssessmentsCount = 0;
                $positiveAssessmentsCount = 0;
                $isComplete = false;
                $isLolos = false;
                $requiredAssessments = 0; // Termasuk self-assessment
                $finalScore = null; // Untuk nilai akhir jika lolos

                if ($matkulAssessmentInstance) {
                    // Hitung asesor yang ditugaskan
                    if ($matkulAssessmentInstance->assessor1_id) $assignedAssessorsCount++;
                    if ($matkulAssessmentInstance->assessor2_id) $assignedAssessorsCount++;
                    if ($matkulAssessmentInstance->assessor3_id) $assignedAssessorsCount++;
                    
                    $requiredAssessments = $assignedAssessorsCount + 1; // +1 untuk self_assessment_value

                    // Hitung penilaian yang sudah selesai
                    if ($matkulAssessmentInstance->self_assessment_value) { // Dianggap selesai jika ada nilai
                        $completedAssessmentsCount++;
                        if (in_array($matkulAssessmentInstance->self_assessment_value, ['Baik', 'Sangat Baik'])) {
                            $positiveAssessmentsCount++;
                        }
                    }
                    if ($matkulAssessmentInstance->assessor1_id && $matkulAssessmentInstance->assessor1_assessment) {
                        $completedAssessmentsCount++;
                        if (in_array($matkulAssessmentInstance->assessor1_assessment, ['Baik', 'Sangat Baik'])) {
                            $positiveAssessmentsCount++;
                        }
                    }
                    if ($matkulAssessmentInstance->assessor2_id && $matkulAssessmentInstance->assessor2_assessment) {
                        $completedAssessmentsCount++;
                        if (in_array($matkulAssessmentInstance->assessor2_assessment, ['Baik', 'Sangat Baik'])) {
                            $positiveAssessmentsCount++;
                        }
                    }
                    if ($matkulAssessmentInstance->assessor3_id && $matkulAssessmentInstance->assessor3_assessment) {
                        $completedAssessmentsCount++;
                        if (in_array($matkulAssessmentInstance->assessor3_assessment, ['Baik', 'Sangat Baik'])) {
                            $positiveAssessmentsCount++;
                        }
                    }

                    $isComplete = ($completedAssessmentsCount >= $requiredAssessments) && ($requiredAssessments > 0) ;
                    
                    if ($isComplete) {
                        $percentage = ($requiredAssessments > 0) ? ($positiveAssessmentsCount / $requiredAssessments) * 100 : 0;
                        $isLolos = $percentage >= 50;

                        // Hitung nilai akhir jika lolos
                        if ($isLolos) {
                            // Ambil semua nilai dari tabel matkul_scores untuk matkul dan camaba ini
                            $scores = Matkul_score::where('matkul_id', $mk->id)
                                                ->where('calon_mahasiswa_id', $camaba->id)
                                                ->whereNotNull('nilai') // Hanya yang ada nilainya
                                                ->avg('nilai'); // Ambil rata-rata
                            $finalScore = $scores !== null ? round($scores, 2) : null;
                        }
                    }
                }
                
                // Tambahkan status ke objek matkul
                $mk->isComplete = $isComplete;
                $mk->isLolos = $isLolos;
                $mk->completedAssessmentsCount = $completedAssessmentsCount;
                $mk->requiredAssessments = $requiredAssessments;
                $mk->finalScore = $finalScore; // Ini akan digunakan untuk kolom Nilai di tabel konversi

                return $mk;
            });

            // Rekomendasi (kode Anda sebelumnya, pastikan $transkrip diambil)
            $transkrip = Transkrip::where('calon_mahasiswa_id', $id)->first();
            $rekomendasi = [];
            if ($transkrip && $transkrip->mata_kuliah_transkrip) {
                $matkulTranskrip = is_string($transkrip->mata_kuliah_transkrip) ?
                    json_decode($transkrip->mata_kuliah_transkrip, true) :
                    $transkrip->mata_kuliah_transkrip;

                if (is_array($matkulTranskrip)) { // Pastikan hasil decode adalah array
                    foreach ($allMatkulJurusan as $matkulTujuan) { // Gunakan $allMatkulJurusan
                        $maxSimilarity = 0;
                        $bestMatch = null;

                        $mappingTujuan = is_string($matkulTujuan->sinonim) ?
                            json_decode($matkulTujuan->sinonim, true) :
                            $matkulTujuan->sinonim ?? [];
                        $mappingTujuan = is_array($mappingTujuan) ? $mappingTujuan : [];


                        foreach ($matkulTranskrip as $mkAsal) {
                             if (!is_array($mkAsal)) continue; // Skip jika format tidak sesuai
                            $mappingAsal = $mkAsal['mapping'] ?? [];
                            $mappingAsal = is_array($mappingAsal) ? $mappingAsal : [];


                            if (!empty($mappingAsal) && !empty($mappingTujuan)) {
                                $mappingAsalLower = array_map('mb_strtolower', $mappingAsal);
                                $mappingTujuanLower = array_map('mb_strtolower', $mappingTujuan);

                                $jaccardScore = $this->hitungJaccardSimilarity($mappingAsalLower, $mappingTujuanLower);
                                $cosineScore = $this->hitungCosineSimilarity($mappingAsalLower, $mappingTujuanLower);
                                $combinedScore = min(1.0, ($jaccardScore + $cosineScore) / 2);

                                if ($combinedScore > $maxSimilarity) {
                                    $maxSimilarity = $combinedScore;
                                    $bestMatch = [
                                        'matkul_asal' => $mkAsal['nama'] ?? 'N/A',
                                        'nilai_asal' => $mkAsal['nilai'] ?? 'N/A',
                                        'similarity_score' => $combinedScore
                                    ];
                                }
                            }
                        }

                        if ($maxSimilarity >= 0.5 && $bestMatch) {
                            $rekomendasi[] = [
                                'matkul_tujuan' => $matkulTujuan->nama_matkul,
                                'matkul_asal' => $bestMatch['matkul_asal'],
                                'nilai_asal' => $bestMatch['nilai_asal'],
                                'similarity_score' => $bestMatch['similarity_score']
                            ];
                        }
                    }
                    usort($rekomendasi, function($a, $b) {
                        return $b['similarity_score'] <=> $a['similarity_score'];
                    });
                }
            }
            
            // Ambil Matkul_score spesifik untuk asesor yang login (untuk tab konversi)
            $matkulScoresForLoggedInAssessor = Matkul_score::where('calon_mahasiswa_id', $id)
                                        ->where('assessor_id', $loggedInAssessor->id)
                                        ->get()
                                        ->keyBy('matkul_id');

            return view('Assessor/detail-user', [
                'camaba' => $camaba,
                'matkul' => $matkulWithStatus, // Untuk tabel konversi dengan status Lolos/Gagal
                'matkul2' => $allMatkulJurusan, // Untuk dropdown modal tambah matkul
                'rekomendasi' => $rekomendasi,
                'matkulAssessments' => $matkulAssessmentsFromDB, // Ini yang PENTING untuk tab Self-Assessment
                'matkulScores' => $matkulScoresForLoggedInAssessor, // Nilai yang diinput asesor di tabel konversi
                'loggedInAssessor' => $loggedInAssessor // Kirim objek asesor yang login
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Calon mahasiswa tidak ditemukan di detail_user: ' . $e->getMessage(), ['calon_mahasiswa_id' => $id]);
            return redirect()->route('list-name-table')->with('error', 'Calon mahasiswa tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('Error in detail_user: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat detail: ' . $e->getMessage());
        }
    }
    
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
    
            // Find the MatkulAssessment for this course and student
            $matkulAssessment = \App\Models\MatkulAssessment::where([
                'matkul_id' => $request->matkul_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$matkulAssessment) {
                 // If no MatkulAssessment exists, we can't calculate status based on assessments
                 // We might want to handle this case, perhaps setting status to 'Belum Dinilai' or similar
                $status = 'Belum Dinilai';
                $percentage = 0;
            } else {
                // Calculate status based on self-assessment and assessor assessments with new weighting
                $assignedAssessorsCount = 0;
                $completedAssessmentsCount = 0;
                $positiveAssessmentsCount = 0;
                $requiredAssessments = 0;
                
                // Count assigned assessors
                if ($matkulAssessment->assessor1_id) $assignedAssessorsCount++;
                if ($matkulAssessment->assessor2_id) $assignedAssessorsCount++;
                if ($matkulAssessment->assessor3_id) $assignedAssessorsCount++;

                $requiredAssessments = $assignedAssessorsCount + 1; // +1 for self-assessment

                // Count completed and positive assessments
                if ($matkulAssessment->self_assessment_value && $matkulAssessment->self_assessment_value !== '') {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->self_assessment_value, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                if ($matkulAssessment->assessor1_id && $matkulAssessment->assessor1_assessment && $matkulAssessment->assessor1_assessment !== '') {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor1_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                if ($matkulAssessment->assessor2_id && $matkulAssessment->assessor2_assessment && $matkulAssessment->assessor2_assessment !== '') {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor2_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                if ($matkulAssessment->assessor3_id && $matkulAssessment->assessor3_assessment && $matkulAssessment->assessor3_assessment !== '') {
                    $completedAssessmentsCount++;
                    if (in_array($matkulAssessment->assessor3_assessment, ['Baik', 'Sangat Baik'])) {
                        $positiveAssessmentsCount++;
                    }
                }

                // Determine completion
                $isComplete = ($matkulAssessment->self_assessment_value !== null && $completedAssessmentsCount === $requiredAssessments);

                // Calculate percentage and Lolos/Gagal status only if complete
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

            // Check if the matkul has passed the assessment phase
            $matkulAssessment = \App\Models\MatkulAssessment::where([
                'matkul_id' => $request->matkul_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$matkulAssessment) {
                throw new \Exception('Assessment data not found for this course.');
            }

            // Get the current status from Matkul_score
            $currentScore = \App\Models\Matkul_score::where([
                'matkul_id' => $request->matkul_id,
                'assessor_id' => $request->assessor_id,
                'calon_mahasiswa_id' => $request->calon_mahasiswa_id
            ])->first();

            if (!$currentScore || $currentScore->status !== 'Lolos') {
                throw new \Exception('Cannot input numeric score for a course that has not passed the assessment phase.');
            }

            // Update or create the Matkul_score record
            $matkulScore = \App\Models\Matkul_score::updateOrCreate(
                [
                    'matkul_id' => $request->matkul_id,
                    'assessor_id' => $request->assessor_id,
                    'calon_mahasiswa_id' => $request->calon_mahasiswa_id,
                ],
                [
                    'nilai' => $request->nilai,
                    'status' => 'Lolos', // Keep the status as Lolos
                    'updated_at' => now()
                ]
            );

            // Redirect back to the detail page to show the updated nilai
            return redirect()->route('detail-user', ['id' => $request->calon_mahasiswa_id])
                ->with('success', 'Nilai berhasil disimpan.');
            
        } catch (\Exception $e) {
            Log::error('Error menyimpan nilai: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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

    
    public function profile_assessor_edit_view($id){
        $assessor = Assessor::findOrFail($id);
        return view('Assessor/profile-edit-assesor', compact('assessor'));
    }
    
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

    private function hitungJaccardSimilarity($set1, $set2) 
    {
        // Ensure both sets are arrays and remove duplicates
        $set1 = array_unique(array_filter($set1));
        $set2 = array_unique(array_filter($set2));

        if (empty($set1) || empty($set2)) {
            return 0;
        }

        // Convert all elements to lowercase for comparison
        $set1 = array_map('mb_strtolower', $set1);
        $set2 = array_map('mb_strtolower', $set2);

        $intersection = array_intersect($set1, $set2);
        $union = array_unique(array_merge($set1, $set2));

        return count($intersection) / count($union);
    }

    private function hitungCosineSimilarity($set1, $set2) 
    {
        // Ensure both sets are arrays and remove duplicates
        $set1 = array_unique(array_filter($set1));
        $set2 = array_unique(array_filter($set2));

        if (empty($set1) || empty($set2)) {
            return 0;
        }

        // Convert all elements to lowercase for comparison
        $set1 = array_map('mb_strtolower', $set1);
        $set2 = array_map('mb_strtolower', $set2);

        // Create vectors
        $allTerms = array_unique(array_merge($set1, $set2));
        $vector1 = array_fill_keys($allTerms, 0);
        $vector2 = array_fill_keys($allTerms, 0);

        // Calculate term frequencies
        foreach ($set1 as $term) {
            $vector1[$term]++;
        }
        foreach ($set2 as $term) {
            $vector2[$term]++;
        }

        // Calculate dot product and magnitudes
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

    public function view_bukti_alih_jenjang($filename)
    {
        // Cari bukti berdasarkan filename
        $bukti = Bukti_alih_jenjang::where('file', $filename)->firstOrFail();
    
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

    public function download_bukti_alih_jenjang($filename)
    {
        // Cari bukti berdasarkan filename
        $bukti = Bukti_alih_jenjang::where('file', $filename)->firstOrFail();
    
        $path = public_path('Data/Bukti_alih_jenjang/' . $filename);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->download($path, $filename);
    }

    public function saveMatkulAssessorAssessment(Request $request)
    {
        try {
            Log::info('Starting saveMatkulAssessorAssessment', [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $user = Auth::user();
            $assessor = Assessor::where('user_id', $user->id)->first();

            if (!$assessor) {
                Log::error('Assessor not found', ['user_id' => $user->id]);
                throw new \Exception('Assessor data not found.');
            }

            // Validate the incoming array of assessments
            $validated = $request->validate([
                'calon_mahasiswa_id' => 'required|exists:calon_mahasiswa,id',
                'assessments' => 'required|array',
                'assessments.*.matkul_assessment_id' => 'required|exists:matkul_assessments,id',
                'assessments.*.value' => 'nullable|string|in:Tidak Pernah,Baik,Sangat Baik',
            ]);

            $calonMahasiswaId = (int)$validated['calon_mahasiswa_id'];

            // Verify that the logged-in assessor is assigned to this student
            $isAssigned = \App\Models\Assessment::where('calon_mahasiswa_id', $calonMahasiswaId)
                                              ->where(function ($query) use ($assessor) {
                                                  $query->where('assessor_id_1', $assessor->id)
                                                        ->orWhere('assessor_id_2', $assessor->id)
                                                        ->orWhere('assessor_id_3', $assessor->id);
                                              })
                                              ->exists();

            if (!$isAssigned) {
                Log::error('Assessor not assigned to student', [
                    'assessor_id' => $assessor->id,
                    'calon_mahasiswa_id' => $calonMahasiswaId
                ]);
                throw new \Exception('Assessor is not assigned to evaluate this student.');
            }

            $matkulIdsToUpdate = [];
            $updatedAssessments = [];

            // Process each submitted assessment
            foreach ($validated['assessments'] as $assessmentData) {
                Log::info('Processing assessment data', ['assessment_data' => $assessmentData]);

                if (!isset($assessmentData['value'])) {
                    Log::info('Skipping assessment without value', ['assessment_data' => $assessmentData]);
                    continue;
                }

                $matkulAssessmentId = (int)$assessmentData['matkul_assessment_id'];
                $assessmentValue = $assessmentData['value'];

                $matkulAssessment = \App\Models\MatkulAssessment::findOrFail($matkulAssessmentId);

                // Double-check that this assessment belongs to the correct student
                if ((int)$matkulAssessment->calon_mahasiswa_id !== $calonMahasiswaId) {
                    Log::warning('Assessment belongs to different student', [
                        'matkul_assessment_id' => $matkulAssessmentId,
                        'expected_calon_mahasiswa_id' => $calonMahasiswaId,
                        'actual_calon_mahasiswa_id' => $matkulAssessment->calon_mahasiswa_id
                    ]);
                    continue;
                }

                // Update the assessment based on which slot the current assessor occupies
                $updated = false;
                if ($matkulAssessment->assessor1_id === $assessor->id) {
                    $matkulAssessment->assessor1_assessment = $assessmentValue;
                    $updated = true;
                } elseif ($matkulAssessment->assessor2_id === $assessor->id) {
                    $matkulAssessment->assessor2_assessment = $assessmentValue;
                    $updated = true;
                } elseif ($matkulAssessment->assessor3_id === $assessor->id) {
                    $matkulAssessment->assessor3_assessment = $assessmentValue;
                    $updated = true;
                }

                if ($updated) {
                    $matkulAssessment->save();
                    $matkulIdsToUpdate[] = $matkulAssessment->matkul_id;
                    $updatedAssessments[] = [
                        'matkul_id' => $matkulAssessment->matkul_id,
                        'value' => $assessmentValue
                    ];
                    Log::info('Successfully updated assessment', [
                        'matkul_id' => $matkulAssessment->matkul_id,
                        'value' => $assessmentValue
                    ]);
                } else {
                    Log::warning('Assessment not updated - assessor not assigned to this assessment', [
                        'matkul_assessment_id' => $matkulAssessmentId,
                        'assessor_id' => $assessor->id
                    ]);
                }
            }

            Log::info('Successfully updated assessments', [
                'updated_assessments' => $updatedAssessments,
                'matkul_ids_to_update' => $matkulIdsToUpdate
            ]);

            // After saving the assessor assessments, recalculate the overall status for the affected matkuls
            foreach(array_unique($matkulIdsToUpdate) as $matkulId) {
                $this->updateMatkulStatus($calonMahasiswaId, $matkulId);
            }

            // Clear any cached data
            \Cache::forget('matkul_assessments_' . $calonMahasiswaId);
            
            // Force reload the data
            $matkulAssessments = \App\Models\MatkulAssessment::where('calon_mahasiswa_id', $calonMahasiswaId)
                ->with('matkul')
                ->get();

            // Redirect with cache control headers
            return redirect()
                ->route('detail-user', ['id' => $calonMahasiswaId])
                ->with('success', 'Penilaian berhasil disimpan!')
                ->withHeaders([
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);

        } catch (\Exception $e) {
            Log::error('Error saving Matkul Assessor Assessments', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    private function updateMatkulStatus($calonMahasiswaId, $matkulId)
    {
        $matkulAssessment = \App\Models\MatkulAssessment::where([
            'matkul_id' => $matkulId,
            'calon_mahasiswa_id' => $calonMahasiswaId
        ])->first();

        if (!$matkulAssessment) {
            return;
        }
        
        $assessorId = auth()->user()->assessor->id ?? null;

        $assignedAssessorsCount = 0;
        $completedAssessmentsCount = 0;
        $positiveAssessmentsCount = 0;

        // Count assigned assessors
        if ($matkulAssessment->assessor1_id) $assignedAssessorsCount++;
        if ($matkulAssessment->assessor2_id) $assignedAssessorsCount++;
        if ($matkulAssessment->assessor3_id) $assignedAssessorsCount++;

        // Count completed and positive assessments
        if ($matkulAssessment->self_assessment_value) {
            $completedAssessmentsCount++;
            if (in_array($matkulAssessment->self_assessment_value, ['Baik', 'Sangat Baik'])) {
                $positiveAssessmentsCount++;
            }
        }

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

        // Determine if all required assessments are complete
        $requiredAssessments = $assignedAssessorsCount + 1; // +1 for self-assessment
        $isComplete = $completedAssessmentsCount === $requiredAssessments;

        // Calculate percentage and determine status
        $percentage = $isComplete && $requiredAssessments > 0 ? ($positiveAssessmentsCount / $requiredAssessments) * 100 : 0;
        $status = $isComplete ? ($percentage >= 50 ? 'Lolos' : 'Gagal') : 'Menunggu Penilaian';

        // Update Matkul_score records for all assessors
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
}
