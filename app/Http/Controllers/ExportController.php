<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use App\Models\Calon_mahasiswa;
use App\Models\MatkulAssessment;
use App\Models\Matkul_score;
use Illuminate\Support\Facades\Storage;
use ConvertApi\ConvertApi;

class ExportController extends Controller
{
    public function exportWordF02($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting export for ID: ' . $id);

            // Enable output escaping
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

            // Get student data
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip'])->findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Student data retrieved: ' . $camaba->nama);
            
            // Get assessment data
            $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $id)
                ->with('matkul')
                ->get();
            \Illuminate\Support\Facades\Log::info('Found ' . count($matkulAssessments) . ' assessments');

            // Load template
            $templatePath = storage_path('app/public/template/f02_template.docx');
            if (!file_exists($templatePath)) {
                throw new \Exception('Template file not found at: ' . $templatePath);
            }
            \Illuminate\Support\Facades\Log::info('Template found at: ' . $templatePath);

            $templateProcessor = new TemplateProcessor($templatePath);
            \Illuminate\Support\Facades\Log::info('Template processor created');

            // Replace placeholders with actual data
            $templateProcessor->setValue('nama', $camaba->nama ?? '-');
            $templateProcessor->setValue('prodi', $camaba->jurusan->nama_jurusan ?? '-');
            $templateProcessor->setValue('alamat', $camaba->alamat ?? '-');
            $templateProcessor->setValue('email', $camaba->user->email ?? '-');
            $templateProcessor->setValue('no_wa', $camaba->nomor_telepon ?? '-');
            $templateProcessor->setValue('jenjang_prodi', $camaba->jurusan->jenjang ?? '-');
            $templateProcessor->setValue('tempat_lahir', $camaba->tempat_lahir ?? '-');
            $templateProcessor->setValue('tanggal_lahir', $camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-');
            $templateProcessor->setValue('kelamin', $camaba->kelamin ?? '-');
            $templateProcessor->setValue('kode_pos', $camaba->kode_pos ?? '-');
            $templateProcessor->setValue('kebangsaan', $camaba->kebangsaan ?? '-');
            $templateProcessor->setValue('nomor_rumah', $camaba->nomor_rumah ?? '-');
            $templateProcessor->setValue('nomor_kantor', $camaba->nomor_kantor ?? '-');
            $templateProcessor->setValue('institusi', $camaba->ijazah->institusi_pendidikan ?? '-');
            $templateProcessor->setValue('jenjang', $camaba->ijazah->jenjang ?? '-');
            $templateProcessor->setValue('ipk', $camaba->ijazah->ipk_nilai ?? '-');
            $templateProcessor->setValue('tahun_lulus', $camaba->ijazah->tahun_lulus ?? '-');
            $templateProcessor->setValue('jurusan_ijazah', $camaba->ijazah->jurusan ?? '-');

            \Illuminate\Support\Facades\Log::info('Basic data replaced in template');

            // Add table title
            $templateProcessor->setValue('table_title', 'MATA KULIAH YANG DIAJUKAN');

            // Create table rows
            if (count($matkulAssessments) > 0) {
                try {
                    \Illuminate\Support\Facades\Log::info('Attempting to clone rows for table');
                    $templateProcessor->cloneRow('no', count($matkulAssessments));
                    \Illuminate\Support\Facades\Log::info('Rows cloned successfully');

                    $no = 1;
                    foreach ($matkulAssessments as $assessment) {
                        $templateProcessor->setValue('no#' . $no, $no);
                        $templateProcessor->setValue('kode_matkul#' . $no, $assessment->matkul->kode_matkul ?? '-');
                        $templateProcessor->setValue('nama_matkul#' . $no, $assessment->matkul->nama_matkul ?? '-');
                        $templateProcessor->setValue('sks#' . $no, $assessment->matkul->sks ?? '-');
                        
                        // Self Assessment - Ya/Tidak based on self_assessment_value
                        $selfAssessment = $assessment->self_assessment_value ?? '';
                        $templateProcessor->setValue('rpl#' . $no, ($selfAssessment === 'Mengajukan') ? 'Iya' : 'Tidak');
                        
                        // Keterangan - Transfer SKS/Perolehan SKS
                        $templateProcessor->setValue('keterangan#' . $no, 'Transfer SKS');
                        
                        $no++;
                    }
                    \Illuminate\Support\Facades\Log::info('Table data filled successfully');
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error while creating table: ' . $e->getMessage());
                    throw $e;
                }
            }

            // Save as new document
            $outputDir = storage_path('app/public/exports');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Change filename to Form_2_F02
            $outputFileName = 'Form_2_F02_' . str_replace(' ', '_', $camaba->nama) . '.docx';
            $outputPath = $outputDir . '/' . $outputFileName;

            \Illuminate\Support\Facades\Log::info('Attempting to save file to: ' . $outputPath);

            $templateProcessor->saveAs($outputPath);

            if (!file_exists($outputPath)) {
                throw new \Exception('Failed to create output file at: ' . $outputPath);
            }

            \Illuminate\Support\Facades\Log::info('File saved successfully, preparing download');

            // Return the file for download
            return response()->download($outputPath, $outputFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in exportWord: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error generating document: ' . $e->getMessage());
        }
    }

    public function exportPdf($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting PDF export for ID: ' . $id);

            // Get student data
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah'])->findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Student data retrieved for PDF: ' . $camaba->nama);
            
            // Get assessment data for the table
            $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $id)
                ->with('matkul')
                ->get();
            \Illuminate\Support\Facades\Log::info('Found ' . count($matkulAssessments) . ' matkul assessments for PDF');

            // Start building the HTML content
            $html = '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Formulir Aplikasi RPL Tipe A</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    margin: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header img {
                    width: 150px; /* Sesuaikan ukuran logo jika perlu */
                    margin-bottom: 10px;
                }
                .header h3 {
                    margin: 5px 0;
                }
                .main-title {
                    text-align: center;
                    font-weight: bold;
                    font-size: 1.2em;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 15px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    vertical-align: top;
                }
                th {
                    background-color: #f2f2f2;
                }
                .section-title {
                    font-weight: bold;
                    margin-top: 20px;
                    margin-bottom: 10px;
                }
                .form-group {
                    margin-bottom: 10px;
                }
                .form-group label {
                    display: inline-block;
                    width: 200px; /* Lebar label */
                }
                .form-group span, .form-group input[type="text"] { /* Tambahkan input jika ingin interaktif */
                    display: inline-block;
                }
                .note {
                    font-size: 0.9em;
                    font-style: italic;
                }
                .declaration ol {
                    padding-left: 20px;
                }
                .declaration li {
                    margin-bottom: 10px;
                }
                .signature-section {
                    margin-top: 30px;
                    overflow: auto; /* Clearfix */
                }
                .signature-block {
                    float: right;
                    width: 300px; /* Sesuaikan lebar blok tanda tangan */
                    text-align: center;
                }
                .signature-block .date-location {
                    margin-bottom: 60px; /* Jarak untuk tanda tangan */
                }
                .attachments ul {
                    list-style-type: none;
                    padding-left: 0;
                }
                .attachments li {
                    margin-bottom: 5px;
                }
                /* Styles for table in Bagian 2 */
                .course-table th, .course-table td {
                    text-align: center;
                }
                .course-table .mata-kuliah-col {
                    text-align: left;
                }
            </style>
        </head>
            <body>

                <div class="header">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Logo_Universitas_Trunojoyo.png/600px-Logo_Universitas_Trunojoyo.png" alt="Logo Universitas Trunojoyo Madura"> <h3>UNIVERSITAS TRUNOJOYO MADURA</h3>
                    Program Studi : ...............................................
                </div>

                <div class="main-title">
                    FORMULIR APLIKASI <br>
                    REKOGNISI PEMBELAJARAN LAMPAU (RPL) <br>
                    TAHUN 2024 <br>
                    FORMULIR APLIKASI RPL TIPE A (Form 2/F02)
                </div>

                <div class="form-group">
                    <label>Program Studi</label>: <span>' . ($camaba->jurusan->nama_jurusan ?? '-') . '</span>
                </div>
                <div class="form-group">
                    <label>Jenjang</label>: <span>' . ($camaba->jurusan->jenjang ?? '-') . '</span>
                </div>
                <div class="form-group">
                    <label>Nama Perguruan Tinggi</label>: <span>Universitas Trunojoyo Madura</span>
                </div>

                <div class="section-title">Bagian 1: Rincian Data Calon Mahasiswa</div>
                <p>Pada bagian ini, cantumkan data pribadi, data pendidikan formal serta data pekerjaan saudara pada saat ini.</p>

                <div class="subsection-title">a. Data Pribadi</div>
                <table>
                    <tr>
                        <td>Nama lengkap</td>
                        <td>:</td>
                        <td>' . ($camaba->nama ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Tempat / tgl. lahir</td>
                        <td>:</td>
                        <td>' . ($camaba->tempat_lahir ?? '-') . '/' . ($camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-') . '</td>
                    </tr>
                    <tr>
                        <td>Jenis kelamin</td>
                        <td>:</td>
                        <td>' . ($camaba->kelamin ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>Menikah/Lajang/Pernah menikah *)</td>
                    </tr>
                    <tr>
                        <td>Kebangsaan</td>
                        <td>:</td>
                        <td>' . ($camaba->kebangsaan ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td rowspan="2">Alamat rumah</td>
                        <td rowspan="2">:</td>
                        <td>' . ($camaba->alamat ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Kode pos : ' . ($camaba->kode_pos ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td rowspan="4">No. Telepon/E-mail</td>
                        <td rowspan="4">:</td>
                        <td>Rumah : ' . ($camaba->nomor_rumah ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Kantor : ' . ($camaba->nomor_kantor ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>HP : ' . ($camaba->nomor_telepon ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>e-mail : ' . ($camaba->user->email ?? '-') . '</td>
                    </tr>
                </table>
                <p class="note">*) Coret yang tidak perlu</p>

                <div class="subsection-title">b. Data Pendidikan</div>
                <table>
                    <tr>
                        <td>Pendidikan terakhir</td>
                        <td>:</td>
                        <td>' . ($camaba->ijazah->jenjang ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Nama Perguruan Tinggi/Sekolah</td>
                        <td>:</td>
                        <td>' . ($camaba->ijazah->institusi_pendidikan ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>' . ($camaba->ijazah->jurusan ?? '-') . '</td>
                    </tr>
                    <tr>
                        <td>Tahun lulus</td>
                        <td>:</td>
                        <td>' . ($camaba->ijazah->tahun_lulus ?? '-') . '</td>
                    </tr>
                </table>

                <div class="section-title">Bagian 2: Daftar Mata Kuliah</div>
                <p>Pada bagian 2 ini, cantumkan Daftar Mata Kuliah pada Program Studi yang saudara ajukan untuk memperoleh pengakuan berdasarkan kompetensi yang sudah saudara peroleh dari pendidikan formal sebelumnya (melalui Transfer sks ), dan dari pendidikan nonformal, informal atau pengalaman kerja (melalui asesmen untuk Perolehan sks ), dengan cara memberi tanda pada pilihan Ya atau Tidak.</p>

                <div class="form-group">
                <strong>Daftar Mata Kuliah Program Studi :</strong> ………………….
                <br>
                MATA KULIAH YANG DIAJUKAN
                </div>

                <table class="course-table">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Kode Mata Kuliah</th>
                            <th rowspan="2" class="mata-kuliah-col">Nama Mata Kuliah</th>
                            <th rowspan="2">sks</th>
                            <th colspan="2">Mengajukan RPL</th>
                            <th rowspan="2">Keterangan<br>(Isikan:Transfer sks/Perolehan sks)</th>
                        </tr>
                        <tr>
                            <th>Ya</th>
                            <th>Tidak</th>
                        </tr>
                    </thead>
                    <tbody>';

                        // Add table rows dynamically
                        $no = 1;
                        foreach ($matkulAssessments as $assessment) {
                            // Determine Ya/Tidak based on self_assessment_value
                            $selfAssessment = $assessment->self_assessment_value ?? '';
                            $mengajukanRplYa = ($selfAssessment === 'Baik' || $selfAssessment === 'Sangat Baik') ? 'X' : '';
                            $mengajukanRplTidak = ($selfAssessment !== 'Baik' && $selfAssessment !== 'Sangat Baik') ? 'X' : '';

                            $html .= '<tr>
                                <td>' . $no . '</td>
                                <td>' . ($assessment->matkul->kode_matkul ?? '-') . '</td>
                                <td class="mata-kuliah-col">' . ($assessment->matkul->nama_matkul ?? '-') . '</td>
                                <td>' . ($assessment->matkul->sks ?? '-') . '</td>
                                <td>' . $mengajukanRplYa . '</td>
                                <td>' . $mengajukanRplTidak . '</td>
                                <td>Transfer SKS</td>
                            </tr>';
                            $no++;
                        }

                        $html .= '</tbody>
                </table>
                <p class="note">Keterangan untuk kolom "Mengajukan RPL": Beri tanda pada pilihan Ya atau Tidak.</p>


                <div class="declaration">
                    <p>Bersama ini saya mengajukan permohonan untuk dapat mengikuti Rekognisi Pembelajaran Lampau (RPL) dan dengan ini saya menyatakan bahwa:</p>
                    <ol>
                        <li>semua informasi yang saya tuliskan adalah sepenuhnya benar dan saya bertanggung-jawab atas seluruh data dalam formulir ini, dan apabila dikemudian hari ternyata informasi yang saya sampaikan tersebut adalah tidak benar, maka saya bersedia menerima sangsi sesuai dengan ketentuan yang berlaku;</li>
                        <li>saya memberikan ijin kepada pihak pengelola program RPL, untuk melakukan pemeriksaan kebenaran informasi yang saya berikan dalam formulir aplikasi ini kepada seluruh pihak yang terkait dengan jenjang akademik sebelumnya dan kepada perusahaan tempat saya bekerja sebelumnyadan atau saat ini saya bekerja;</li>
                        <li>dan saya akan mengikuti proses asesmen sesuai dengan jadwal/waktu yang ditetapkan oleh Perguruan Tinggi.</li>
                    </ol>
                </div>

                <div class="signature-section">
                    <div class="signature-block">
                        <div class="date-location">Tempat/Tanggal: ............................</div>
                        <div>Tanda tangan Pemohon:</div>
                        <br><br><br><br>
                        <div>(........................................................)</div>
                    </div>
                </div>

                <div class="section-title">Lampiran yang disertakan:</div>
                <ul class="attachments">
                    <li>Formulir Evaluasi Diri sesuai dengan Daftar Mata Kuliah yang diajukan untuk RPL disertai dengan bukti pendukung pemenuhan Capaian Pembelajarannya.</li>
                    <li>Daftar Riwayat Hidup (lihat Form 7/F07)</li>
                    <li>Ijazah dan Transkrip Nilai</li>
                    <li>................... lainnya/sebutkan…………...</li>
                </ul>

            </body>
            </html>';

            // --- Convert HTML to PDF using Dompdf --- //
            $dompdf = new Dompdf();
            
            // Enable HTML5 parsing and remote files (if you have images/CSS links)
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);

            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Save the PDF to the export path
            $outputDir = storage_path('app/public/exports');
             // Ensure export directory exists
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Change filename to Form_2_F02
            $outputFileName = 'Form_2_F02_' . str_replace(' ', '_', $camaba->nama) . '.pdf';
            $outputPath = $outputDir . '/' . $outputFileName;

            file_put_contents($outputPath, $dompdf->output());

            \Illuminate\Support\Facades\Log::info('PDF file saved successfully, preparing download');

            return response()->download($outputPath, $outputFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error generating PDF (Dompdf): ' . $e->getMessage());
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage() . '. Check server logs.');
        }
    }

    public function exportWordF08($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting transcript export for ID: ' . $id);
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'transkrip'])->findOrFail($id);
            $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $id)->with('matkul')->get();
            
            $templatePath = storage_path('app/public/template/f08_template.docx');
            if (!file_exists($templatePath)) {
                throw new \Exception('Transcript template file not found at: ' . $templatePath);
            }

            $templateProcessor = new TemplateProcessor($templatePath);
            $templateProcessor->setValue('nama', $camaba->nama ?? '-');

            if (count($matkulAssessments) > 0) {
                $templateProcessor->cloneRow('no', count($matkulAssessments));
                $no = 1;

                foreach ($matkulAssessments as $assessment) {
                    // <<< PERUBAHAN LOGIKA DI SINI >>>
                    // Mengambil nilai_akhir dari Matkul_score
                    $score = Matkul_score::where('calon_mahasiswa_id', $id)
                        ->where('matkul_id', $assessment->matkul_id)
                        ->first();
                    
                    // Gunakan nilai_akhir jika ada, jika tidak, fallback ke nilai, jika tidak ada juga, fallback ke 30
                    $numericScore = 30; // Default fallback
                    if ($score) {
                        $numericScore = !is_null($score->nilai_akhir) ? $score->nilai_akhir : (!is_null($score->nilai) ? $score->nilai : 30);
                    }
                    
                    $letterGrade = $this->convertToLetterGrade($numericScore);

                    $templateProcessor->setValue('no#' . $no, $no);
                    $templateProcessor->setValue('kode_matkul#' . $no, $assessment->matkul->kode_matkul ?? '-');
                    $templateProcessor->setValue('nama_matkul#' . $no, $assessment->matkul->nama_matkul ?? '-');
                    $templateProcessor->setValue('sks#' . $no, $assessment->matkul->sks ?? '-');
                    $templateProcessor->setValue('nilai#' . $no, $numericScore);
                    $templateProcessor->setValue('konversi#' . $no, $letterGrade);
                    $templateProcessor->setValue('asal_cp#' . $no, 'RPL');
                    
                    $no++;
                }
            }

            $outputDir = storage_path('app/public/exports');
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $outputFileName = 'Form_8_F08_' . str_replace(' ', '_', $camaba->nama) . '.docx';
            $outputPath = $outputDir . '/' . $outputFileName;
            
            $templateProcessor->saveAs($outputPath);

            if (!file_exists($outputPath)) {
                throw new \Exception('Failed to create transcript output file at: ' . $outputPath);
            }

            return response()->download($outputPath, $outputFileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in exportTranscript: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error generating transcript: ' . $e->getMessage());
        }
    }

    private function convertToLetterGrade($score)
    {
        // Ensure score is treated as a number
        $score = (float) $score;

        if ($score >= 80) return 'A';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'D+';
        if ($score >= 40) return 'D';
        return 'E';
    }

    public function exportPdfFromWordF02($id)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting ConvertAPI PDF export from Word F02 for ID: ' . $id);

            \ConvertApi\ConvertApi::setApiCredentials('VAqaflcqFTMY3DJeUkfrOP50XjEyQV2M');

            // Mendapatkan data
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'ijazah', 'transkrip'])->findOrFail($id);
            \Illuminate\Support\Facades\Log::info('Student data retrieved for ConvertAPI PDF: ' . $camaba->nama);

            // Generate the F02 Word file temporarily
            $wordExportController = new ExportController(); // Create an instance to call exportWordF02
            // Temporarily save the generated .docx file
            $tempWordPath = tempnam(sys_get_temp_dir(), 'f02_export_') . '.docx';
            // We need to capture the response and save the file from it.
            // This requires modifying exportWordF02 slightly or replicating its logic here to save to a specific path.
            // Given the complexity of modifying exportWordF02 just for this,
            // let's replicate the necessary logic to generate and save the .docx temporarily.

            // Replicate exportWordF02 logic to save to a temporary file
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
            $templatePath = storage_path('app/public/template/f02_template.docx');
             if (!file_exists($templatePath)) {
                throw new \Exception('Template file not found at: ' . $templatePath);
            }
            $templateProcessor = new TemplateProcessor($templatePath);

            $templateProcessor->setValue('nama', $camaba->nama ?? '-');
            $templateProcessor->setValue('prodi', $camaba->jurusan->nama_jurusan ?? '-');
            $templateProcessor->setValue('alamat', $camaba->alamat ?? '-');
            $templateProcessor->setValue('email', $camaba->user->email ?? '-');
            $templateProcessor->setValue('no_wa', $camaba->nomor_telepon ?? '-');
            $templateProcessor->setValue('jenjang_prodi', $camaba->jurusan->jenjang ?? '-');
            $templateProcessor->setValue('tempat_lahir', $camaba->tempat_lahir ?? '-');
            $templateProcessor->setValue('tanggal_lahir', $camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-');
            $templateProcessor->setValue('kelamin', $camaba->kelamin ?? '-');
            $templateProcessor->setValue('kode_pos', $camaba->kode_pos ?? '-');
            $templateProcessor->setValue('kebangsaan', $camaba->kebangsaan ?? '-');
            $templateProcessor->setValue('nomor_rumah', $camaba->nomor_rumah ?? '-');
            $templateProcessor->setValue('nomor_kantor', $camaba->nomor_kantor ?? '-');
            $templateProcessor->setValue('institusi', $camaba->ijazah->institusi_pendidikan ?? '-');
            $templateProcessor->setValue('jenjang', $camaba->ijazah->jenjang ?? '-');
            $templateProcessor->setValue('ipk', $camaba->ijazah->ipk_nilai ?? '-');
            $templateProcessor->setValue('tahun_lulus', $camaba->ijazah->tahun_lulus ?? '-');
            $templateProcessor->setValue('jurusan_ijazah', $camaba->ijazah->jurusan ?? '-');

            $templateProcessor->setValue('table_title', 'MATA KULIAH YANG DIAJUKAN');

            $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $id)
                ->with('matkul')
                ->get();

             if (count($matkulAssessments) > 0) {
                 $templateProcessor->cloneRow('no', count($matkulAssessments));
                 $no = 1;
                 foreach ($matkulAssessments as $assessment) {
                     $templateProcessor->setValue('no#' . $no, $no);
                     $templateProcessor->setValue('kode_matkul#' . $no, $assessment->matkul->kode_matkul ?? '-');
                     $templateProcessor->setValue('nama_matkul#' . $no, $assessment->matkul->nama_matkul ?? '-');
                     $templateProcessor->setValue('sks#' . $no, $assessment->matkul->sks ?? '-');
                     $selfAssessment = $assessment->self_assessment_value ?? '';
                     $templateProcessor->setValue('rpl#' . $no, ($selfAssessment === 'Baik' || $selfAssessment === 'Sangat Baik') ? 'Iya' : 'Tidak');
                     $templateProcessor->setValue('keterangan#' . $no, 'Transfer SKS');
                     $no++;
                 }
             }

            $templateProcessor->saveAs($tempWordPath);

            if (!file_exists($tempWordPath)) {
                throw new \Exception('Failed to create temporary Word file at: ' . $tempWordPath);
            }
            \Illuminate\Support\Facades\Log::info('Temporary Word file created: ' . $tempWordPath);

            // Use ConvertAPI to convert the temporary Word file to PDF
            $result = \ConvertApi\ConvertApi::convert('pdf', [
                'File' => $tempWordPath,
            ], 'docx');

            // Get the PDF file content
            $pdfContent = $result->getFile()->getContents();

            // Define output filename
            $outputFileName = 'Form_2_F02_' . str_replace(' ', '_', $camaba->nama) . '.pdf';

            // Clean up temporary Word file
            unlink($tempWordPath);
            \Illuminate\Support\Facades\Log::info('Temporary Word file deleted: ' . $tempWordPath);

            // Return PDF for download
            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, $outputFileName, ['Content-Type' => 'application/pdf']);

        } catch (\ConvertApi\Errors\Api $e) {
             // Handle ConvertAPI specific errors
            \Illuminate\Support\Facades\Log::error('ConvertAPI Error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('ConvertAPI Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'ConvertAPI Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other general errors
            \Illuminate\Support\Facades\Log::error('General Error in ConvertAPI PDF export: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error generating PDF via ConvertAPI: ' . $e->getMessage() . '. Check server logs.');
        }
    }
    
    public function exportPdfFromWordF08($id)
    {
        try {
            // \Illuminate\Support\Facades\Log::info('Starting ConvertAPI PDF export from Word F08 for ID: ' . $id);

            \ConvertApi\ConvertApi::setApiCredentials('VAqaflcqFTMY3DJeUkfrOP50XjEyQV2M'); // Kredensial API-mu lagi

            // Ambil data mahasiswa
            $camaba = Calon_mahasiswa::with(['user', 'jurusan', 'transkrip'])->findOrFail($id);
            // \Illuminate\Support\Facades\Log::info('Student data retrieved for ConvertAPI PDF F08: ' . $camaba->nama);

            // --- Replikasi logika exportWordF08 untuk buat file DOCX sementara ---
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
            $templatePath = storage_path('app/public/template/f08_template.docx');
            if (!file_exists($templatePath)) {
                throw new Exception('Template transkrip F08 tidak ditemukan di: ' . $templatePath);
            }
            $templateProcessor = new TemplateProcessor($templatePath);

            // Isi data mahasiswa
            $templateProcessor->setValue('nama', $camaba->nama ?? '-');
            // Isi data lain jika ada placeholder di template F08
            // $templateProcessor->setValue('nim_f08', $camaba->nim_sementara ?? '-');
            // $templateProcessor->setValue('prodi_f08_detail', $camaba->jurusan->nama_jurusan ?? '-');

            // \Illuminate\Support\Facades\Log::info('Student data replaced in transcript template for PDF export');

            // Ambil dan proses tabel mata kuliah
            $matkulAssessments = MatkulAssessment::where('calon_mahasiswa_id', $id)
                ->with('matkul')
                ->get();

            if (count($matkulAssessments) > 0) {
                // \Illuminate\Support\Facades\Log::info('Attempting to clone rows for transcript table PDF export');
                $templateProcessor->cloneRow('no', count($matkulAssessments));
                // \Illuminate\Support\Facades\Log::info('Transcript rows cloned successfully for PDF export');

                $no = 1;
                foreach ($matkulAssessments as $assessment) {
                    $score = Matkul_score::where('calon_mahasiswa_id', $id)
                        ->where('matkul_id', $assessment->matkul_id)
                        ->first();

                    $numericScore = ($score && !is_null($score->nilai)) ? $score->nilai : 30;
                    $letterGrade = $this->convertToLetterGrade($numericScore);

                    // \Illuminate\Support\Facades\Log::info('ExportTranscript PDF F08 Row Data', [ /* ... data ... */ ]);

                    $templateProcessor->setValue('no#' . $no, $no);
                    $templateProcessor->setValue('kode_matkul#' . $no, $assessment->matkul->kode_matkul ?? '-');
                    $templateProcessor->setValue('nama_matkul#' . $no, $assessment->matkul->nama_matkul ?? '-');
                    $templateProcessor->setValue('sks#' . $no, $assessment->matkul->sks ?? '-');
                    $templateProcessor->setValue('nilai#' . $no, $numericScore);
                    $templateProcessor->setValue('konversi#' . $no, $letterGrade);
                    $templateProcessor->setValue('asal_cp#' . $no, 'RPL');

                    $no++;
                }
                // \Illuminate\Support\Facades\Log::info('Transcript table data filled successfully for PDF export');
            }
            // --- Akhir replikasi logika exportWordF08 ---

            // Simpan DOCX sementara
            $tempWordPath = tempnam(sys_get_temp_dir(), 'f08_export_') . '.docx';
            $templateProcessor->saveAs($tempWordPath);

            if (!file_exists($tempWordPath)) {
                throw new Exception('Gagal membuat file Word sementara untuk konversi PDF F08 di: ' . $tempWordPath);
            }
            // \Illuminate\Support\Facades\Log::info('Temporary Word file created for PDF conversion F08: ' . $tempWordPath);

            // Konversi ke PDF pake ConvertAPI
            $result = ConvertApi::convert('pdf', [
                'File' => $tempWordPath,
            ], 'docx');

            // Ambil hasilnya
            $pdfContent = $result->getFile()->getContents();

            // Nama file output
            $outputFileName = 'Form_8_F08_ConvertAPI_' . str_replace(' ', '_', $camaba->nama) . '.pdf';

            // Hapus file DOCX sementara
            unlink($tempWordPath);
            // \Illuminate\Support\Facades\Log::info('Temporary Word file deleted F08: ' . $tempWordPath);

            // Kirim ke user
            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, $outputFileName, ['Content-Type' => 'application/pdf']);

        } catch (\ConvertApi\Errors\Api $e) {
            Log::error('ConvertAPI Error F08: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
            return back()->with('error', 'ConvertAPI Error F08: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('General Error in ConvertAPI PDF F08 export: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
            return back()->with('error', 'Error membuat PDF (via ConvertAPI F08): Terjadi masalah. Silakan cek log.');
        }
    }
} 