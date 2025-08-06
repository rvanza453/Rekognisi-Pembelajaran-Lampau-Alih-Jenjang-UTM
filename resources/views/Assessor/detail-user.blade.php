<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Detail User</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        input[type="radio"] {
            width: 1.5em;
            height: 1.5em;
            accent-color: #0d6efd; /* Bootstrap primary */
            cursor: pointer;
        }
    </style>
</head>
<body>
@extends('layout.assessor')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Data Calon Mahasiswa</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item active"><a href="/list-name-table">Ajuan Form</a></li>
        <li class="breadcrumb-item active"><a href="/detail-user">Data Calon Mahasiswa</a></li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <!-- Deadline Warning Alert -->
  @if($camaba->assessment && $camaba->assessment->deadline)
    @if($camaba->assessment->deadline->isPast())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Peringatan!</strong> Deadline penilaian untuk {{ $camaba->nama }} telah terlambat sejak {{ $camaba->assessment->deadline->format('d/m/Y H:i') }}.
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
      </div>
    @elseif($camaba->assessment->deadline->diffInDays(now()) <= 3)
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-clock me-2"></i>
        <strong>Peringatan!</strong> Deadline penilaian untuk {{ $camaba->nama }} akan berakhir pada {{ $camaba->assessment->deadline->format('d/m/Y H:i') }} 
        ({{ $camaba->assessment->deadline->diffForHumans() }}).
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
      </div>
    @else
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Info:</strong> Deadline penilaian untuk {{ $camaba->nama }} adalah {{ $camaba->assessment->deadline->format('d/m/Y H:i') }}.
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
  @endif

  <!-- Export Buttons -->
  <!-- Tombol Export Utama -->
  <!--<div class="d-flex justify-content-end">-->
  <!--  <div class="btn-group">-->
  <!--      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">-->
  <!--          <i class="bi bi-file-word me-1"></i> Export-->
  <!--      </button>-->
  <!--      <ul class="dropdown-menu dropdown-menu-end">-->
  <!--          <li><a class="dropdown-item" href="{{ route('export-word02', $camaba->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F02</a></li>-->
  <!--          <li><a class="dropdown-item" href="{{ route('exportPdfFromWordF02', $camaba->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF V2</a></li>-->
  <!--          <li><a class="dropdown-item" href="{{ route('export-pdfF02', $camaba->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF</a></li>-->
  <!--          <li><a class="dropdown-item" href="{{ route('export-word08', $camaba->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F08</a></li>-->
  <!--          <li><a class="dropdown-item" href="{{ route('exportPdfFromWordF08', $camaba->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF F08</a></li>-->
  <!--      </ul>-->
  <!--  </div>-->
  <!--</div>-->

  <div class="container-fluid mt-2">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills justify-content-center gap-2 mb-4 flex-wrap">
                <li class="nav-item">
                    <button class="nav-link active btn btn-primary btn-sm" onclick="showTab('profil')">
                        <i class="fas fa-user-circle me-1"></i> Profil
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link btn btn-outline-primary btn-sm" onclick="showTab('ijazah')">
                        <i class="fas fa-graduation-cap me-1"></i> Ijazah
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link btn btn-outline-primary btn-sm" onclick="showTab('bukti')">
                        <i class="fas fa-receipt me-1"></i> Bukti
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link btn btn-outline-primary btn-sm" onclick="showTab('transkrip')">
                        <i class="fas fa-book me-1"></i> Penilaian Konversi
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link btn btn-outline-primary btn-sm" onclick="showTab('penilaian-matkul')">
                        <i class="fas fa-star me-1"></i> Hasil Penilaian
                    </button>
                </li>
            </ul>
        </div>
    </div>
  </div>

  <!-- PROFIL -->
  <div class="tab-content active" id="profil">
    <h2>Data Diri Mahasiswa</h2>
    <div class="card">
      <div class="ijazah-overview-assessor mx-4 my-3">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Nama</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->nama ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Prodi</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->jurusan->nama_jurusan ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Alamat</div>
          <div class="col-lg-9 col-md-8">{{   $camaba->alamat ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Email</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->user->email ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">No Wa</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->nomor_telepon ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Tempat Lahir</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->tempat_lahir ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->kelamin ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Kota</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->kota ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Provinsi</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->provinsi ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Kode Pos</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->kode_pos ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Kebangsaan</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->kebangsaan ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Nomor Rumah</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->nomor_rumah ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Nomor Kantor</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->nomor_kantor ?? '-'  }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- IJAZAH -->
  <div class="tab-content" id="ijazah">
    <h2>Data Ijazah</h2>
    <div class="card">
      <div class="ijazah-overview-assessor mx-4 my-3">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Institusi Pendidikan</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->institusi_pendidikan ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Jenjang</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->jenjang ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Provinsi</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->provinsi ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Kota</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->kota ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Negara</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->negara ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Fakultas</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->fakultas ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Jurusan</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->jurusan ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Nilai/IPK</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->ipk_nilai ?? '-'  }}</div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Tahun Lulus</div>
          <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->tahun_lulus ?? '-'  }}</div>
        </div>
        <div class="row mb-2">
          <div class="col-lg-3 col-md-4 label">Bukti File</div>
          <div class="col-lg-9 col-md-8">
            @if($camaba->ijazah && $camaba->ijazah->file)
              <a href="{{ route('assessor.download-ijazah', $camaba->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-download"></i> Download Ijazah
              </a>
            @else
              <span class="text-muted">File tidak tersedia</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- BUKTI -->
  <div class="tab-content" id="bukti">
    <h2>Bukti Alih Jenjang</h2>
    <div class="card">
      <div class="card-body mt-4">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>Jenis Dokumen</th>
                <th>Nama File</th>
                <th>Tanggal Upload</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($camaba->bukti_alih_jenjang as $index => $bukti)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $bukti->jenis_dokumen }}</td>
                  <td>{{ $bukti->file }}</td>
                  <td>{{ $bukti->created_at->format('d/m/Y H:i') }}</td>
                  <td>
                    <a href="{{ route('assessor.view-bukti-alih-jenjang', ['filename' => $bukti->file]) }}" target="_blank" class="btn btn-sm btn-info">
                      <i class="bi bi-eye"></i> Lihat
                    </a>
                    <a href="{{ route('assessor.download-bukti-alih-jenjang', ['filename' => $bukti->file]) }}" class="btn btn-sm btn-primary">
                      <i class="bi bi-download"></i> Unduh
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center">Belum ada bukti yang diunggah</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- TRANSKRIP -->
  <div class="tab-content" id="transkrip">
      <h5 class="card-title mt-3">Formulir Penilaian Berdasarkan CPMK</h5>
      <p>Klik pada setiap mata kuliah untuk membuka formulir dan memberikan penilaian numerik (0-100) untuk setiap CPMK.</p>

      @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      @endif
      @if (session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      @endif

      {{-- PESAN JIKA SUDAH SUBMIT --}}
      @if($isSubmitted)
        <div class="alert alert-info" role="alert">
            <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Penilaian Terkunci</h4>
            <p>Anda telah melakukan submit penilaian akhir untuk mahasiswa ini. Semua form penilaian di bawah ini telah dinonaktifkan dan tidak dapat diubah lagi.</p>
        </div>
      @endif

      <div class="card">
        <div class="card-body mt-4">
            <!-- PDF Viewer -->
            <div class="pdf-container mt-3 mb-3">
                <embed 
                    src="{{ route('assessor.view-transkrip', $existing_transkrip->file) }}"
                    type="application/pdf"
                    width="100%"
                    height="600px"
                />
            </div>
        </div>
      </div>

      <div class="accordion" id="accordionPenilaianCPMK">
        @forelse($matkul as $mk)
            @php
                $currentAssessorScore = optional($allAssessorScores->get($mk->id))->firstWhere('assessor_id', $assessor->id);
                $matkulCpmkAssessments = $cpmkAssessments->get($mk->id);
            @endphp
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $mk->id }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $mk->id }}" aria-expanded="false" aria-controls="collapse-{{ $mk->id }}">
                        <div class="w-100 d-flex justify-content-between align-items-center pe-2">
                            <span>{{ $loop->iteration }}. {{ $mk->nama_matkul }}</span>
                            @if($currentAssessorScore && $currentAssessorScore->nilai !== null)
                                <span class="badge bg-primary">
                                    Nilai Anda: {{ number_format($currentAssessorScore->nilai, 2) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum Anda Nilai</span>
                            @endif
                        </div>
                    </button>
                </h2>
                <div id="collapse-{{ $mk->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $mk->id }}" data-bs-parent="#accordionPenilaianCPMK">
                    <div class="accordion-body">
                        @if($mk->cpmk->isEmpty())
                            <div class="alert alert-warning text-center">Mata kuliah ini belum memiliki data CPMK.</div>
                        @elseif(!$matkulCpmkAssessments)
                            <div class="alert alert-info text-center">Mahasiswa belum melakukan self-assessment untuk mata kuliah ini.</div>
                        @else
                            <form action="{{ route('assessor.store-cpmk-scores') }}" method="POST">
                                @csrf
                                <input type="hidden" name="calon_mahasiswa_id" value="{{ $camaba->id }}">
                                <input type="hidden" name="matkul_id" value="{{ $mk->id }}">
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        {{-- ... (kode thead tabel) ... --}}
                                        <tbody>
                                            @foreach($mk->cpmk as $cpmk)
                                                @php
                                                    $cpmkData = $matkulCpmkAssessments->firstWhere('cpmk_id', $cpmk->id);
                                                    
                                                    $assessorSlotName = '';
                                                    if ($camaba->assessment) {
                                                        if ($camaba->assessment->assessor_id_1 == $assessor->id) $assessorSlotName = 'nilai_assessor1';
                                                        elseif ($camaba->assessment->assessor_id_2 == $assessor->id) $assessorSlotName = 'nilai_assessor2';
                                                        elseif ($camaba->assessment->assessor_id_3 == $assessor->id) $assessorSlotName = 'nilai_assessor3';
                                                    }
                                                    $nilaiSudahAda = $cpmkData[$assessorSlotName] ?? '';
                                                @endphp
                                                <tr>
                                                    <td>{{ $cpmk->penjelasan }}</td>
                                                    <td>{{ optional($cpmkData)->matkul_dasar ?? '-' }}</td>
                                                    <td>{{ optional($cpmkData)->nilai_matkul_dasar ?? '-' }}</td>
                                                    <td>
                                                        <input type="number" name="scores[{{ $cpmk->id }}]" class="form-control" min="0" max="100" value="{{ $nilaiSudahAda }}" required placeholder="0-100" {{ $isSubmitted ? 'disabled' : '' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn btn-primary" {{ $isSubmitted ? 'disabled' : '' }}>Simpan Nilai {{ $mk->nama_matkul }}</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-secondary text-center">Tidak ada mata kuliah yang tersedia untuk jurusan ini.</div>
        @endforelse
      </div>
  </div>

  <!-- PENILAIAN MATKUL -->
  <div class="tab-content" id="penilaian-matkul">
    <h4 class="mt-4">Hasil Penilaian Mata Kuliah</h4>
    <div class="card">
      <div class="card-body mt-4">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Mata Kuliah</th>
                <th>Bobot SKS</th>
                <th>Status</th>
                <th>Nilai</th>
                <th>Detail Penilaian</th>
                <!--<th>Aksi</th>-->
              </tr>
            </thead>
            <tbody>
              @forelse ($matkul as $index => $mk)
                @php
                   // Fetch the current assessor's score for this matkul
                   $matkulScore = $matkulScores[$mk->id] ?? null;

                   // Fetch the MatkulAssessment to display details
                   $matkulAssessment = $matkulAssessments->firstWhere('matkul_id', $mk->id);
                @endphp
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $mk->nama_matkul ?? '-' }}</td>
                  <td>{{ $mk->sks ?? '-' }}</td>
                  <td>
                    @if($mk->isComplete)
                      <span class="badge {{ $mk->isLolos ? 'bg-success' : 'bg-danger' }}">
                        {{ $mk->isLolos ? 'Lolos' : 'Gagal' }}
                      </span>
                    @else
                      <span class="badge bg-warning">
                        Menunggu Penilaian ({{ $mk->completedAssessmentsCount }}/{{ $mk->requiredAssessments }})
                      </span>
                    @endif
                  </td>
                  <td>
                    @if($allAssessorScores->has($mk->id))
                      @foreach($allAssessorScores[$mk->id] as $score)
                        @if($score->assessor && $score->nilai !== null)
                          <small>
                            <strong>{{ $score->assessor->nama }}:</strong> {{ $score->nilai }}<br>
                          </small>
                        @endif
                      @endforeach
                      @if($allAssessorScores[$mk->id]->where('nilai', '!=', null)->isEmpty())
                        <span class="text-muted">Belum ada nilai</span>
                      @elseif($mk->isLolos && $mk->finalScore)
                        <hr class="my-1">
                        <small class="text-success">
                          <strong>Nilai Akhir:</strong> {{ number_format($mk->finalScore, 2) }}
                        </small>
                      @endif
                    @else
                      <span class="text-muted">Belum ada nilai</span>
                    @endif
                  </td>
                  <td>
                    @if($matkulAssessment)
                      <small>
                        <strong>Self Assessment:</strong> {{ $matkulAssessment->self_assessment_value ?? '-' }}<br>
                        @if($matkulAssessment->assessor1_id && $assignedAssessors->has($matkulAssessment->assessor1_id))
                          <strong>{{ $assignedAssessors[$matkulAssessment->assessor1_id]->nama }}:</strong> {{ $matkulAssessment->assessor1_assessment ?? '-' }}<br>
                        @endif
                        @if($matkulAssessment->assessor2_id && $assignedAssessors->has($matkulAssessment->assessor2_id))
                          <strong>{{ $assignedAssessors[$matkulAssessment->assessor2_id]->nama }}:</strong> {{ $matkulAssessment->assessor2_assessment ?? '-' }}<br>
                        @endif
                        @if($matkulAssessment->assessor3_id && $assignedAssessors->has($matkulAssessment->assessor3_id))
                          <strong>{{ $assignedAssessors[$matkulAssessment->assessor3_id]->nama }}:</strong> {{ $matkulAssessment->assessor3_assessment ?? '-' }}
                        @endif
                      </small>
                    @else
                      <span class="text-muted">Belum ada penilaian</span>
                    @endif
                  </td>
                  <!--<td>-->
                  <!--  @if($mk->isComplete && $mk->isLolos)-->
                  <!--    <form action="{{ route('nilai-matkul-input') }}" method="POST" class="d-inline">-->
                  <!--      @csrf-->
                  <!--      <input type="hidden" name="matkul_id" value="{{ $mk->id }}">-->
                  <!--      <input type="hidden" name="calon_mahasiswa_id" value="{{ $camaba->id }}">-->
                  <!--      <input type="hidden" name="assessor_id" value="{{ auth()->user()->assessor->id ?? '' }}">-->
                  <!--      <div class="input-group">-->
                  <!--        <input type="number" name="nilai" class="form-control form-control-sm" style="width: 80px" min="0" max="100" placeholder="Nilai">-->
                  <!--        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>-->
                  <!--      </div>-->
                  <!--    </form>-->
                  <!--  @elseif(!$mk->isComplete)-->
                  <!--    <span class="text-muted">Menunggu penilaian lengkap</span>-->
                  <!--  @else-->
                  <!--    <span class="text-muted">Tidak dapat memberikan nilai</span>-->
                  <!--  @endif-->
                  <!--</td>-->
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">Tidak ada mata kuliah yang tersedia</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Mata Kuliah</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="row g-3" action="{{ route('handle_matkul_input') }}" method="POST">
              @csrf
              <div class="col-6 my-3">
                <label for="matkul" class="form-label">Pilih Mata Kuliah</label>
                <select name="matkul_id" id="matkul2" class="form-select" required>
                  <option value="" disabled selected>Pilih Mata Kuliah</option>
                  @foreach($matkul2 as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_matkul }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="nilai">Masukkan Nilai</label>
                <input type="number" id="nilai" name="nilai" class="form-control" required>
              </div>
              <input type="hidden" name="calon_mahasiswa_id" value="{{ $camaba->id }}">
              <input type="hidden" name="assessor_id" value="{{ auth()->user()->id }}">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="card">
      <div class="card-body text-center my-3">
          <h5 class="card-title">Submit Penilaian Akhir</h5>
          <p>Klik tombol di bawah ini jika Anda telah selesai melakukan seluruh penilaian untuk mahasiswa ini. Tindakan ini tidak dapat diurungkan.</p>
          
          @php
              $assessorId = auth()->user()->assessor->id;
              $assessment = $camaba->assessment;
              $isSubmitted = false;
              if ($assessment) {
                  if ($assessment->assessor_id_1 == $assessorId && $assessment->assessor_1_submitted_at) {
                      $isSubmitted = true;
                  } elseif ($assessment->assessor_id_2 == $assessorId && $assessment->assessor_2_submitted_at) {
                      $isSubmitted = true;
                  } elseif ($assessment->assessor_id_3 == $assessorId && $assessment->assessor_3_submitted_at) {
                      $isSubmitted = true;
                  }
              }
          @endphp

          {{-- Status Assessor Submission --}}
          @if($assessment)
              <div class="row justify-content-center mb-3">
                  <div class="col-md-8">
                      <h6>Status Submit Assessor:</h6>
                      <div class="d-flex justify-content-center gap-3">
                          @if($assessment->assessor_id_1)
                              <div class="text-center">
                                  <div class="badge {{ $assessment->assessor_1_submitted_at ? 'bg-success' : 'bg-warning' }} mb-1">
                                      {{ $assessment->assessor_1_submitted_at ? '✓' : '⏳' }}
                                  </div>
                                  <div class="small">
                                      <strong>{{ $assignedAssessors->get($assessment->assessor_id_1)->nama ?? 'Assessor 1' }}</strong><br>
                                      @if($assessment->assessor_1_submitted_at)
                                          <small class="text-muted">{{ \Carbon\Carbon::parse($assessment->assessor_1_submitted_at)->format('d/m/Y H:i') }}</small>
                                      @else
                                          <small class="text-muted">Belum submit</small>
                                      @endif
                                  </div>
                              </div>
                          @endif
                          
                          @if($assessment->assessor_id_2)
                              <div class="text-center">
                                  <div class="badge {{ $assessment->assessor_2_submitted_at ? 'bg-success' : 'bg-warning' }} mb-1">
                                      {{ $assessment->assessor_2_submitted_at ? '✓' : '⏳' }}
                                  </div>
                                  <div class="small">
                                      <strong>{{ $assignedAssessors->get($assessment->assessor_id_2)->nama ?? 'Assessor 2' }}</strong><br>
                                      @if($assessment->assessor_2_submitted_at)
                                          <small class="text-muted">{{ \Carbon\Carbon::parse($assessment->assessor_2_submitted_at)->format('d/m/Y H:i') }}</small>
                                      @else
                                          <small class="text-muted">Belum submit</small>
                                      @endif
                                  </div>
                              </div>
                          @endif
                          
                          @if($assessment->assessor_id_3)
                              <div class="text-center">
                                  <div class="badge {{ $assessment->assessor_3_submitted_at ? 'bg-success' : 'bg-warning' }} mb-1">
                                      {{ $assessment->assessor_3_submitted_at ? '✓' : '⏳' }}
                                  </div>
                                  <div class="small">
                                      <strong>{{ $assignedAssessors->get($assessment->assessor_id_3)->nama ?? 'Assessor 3' }}</strong><br>
                                      @if($assessment->assessor_3_submitted_at)
                                          <small class="text-muted">{{ \Carbon\Carbon::parse($assessment->assessor_3_submitted_at)->format('d/m/Y H:i') }}</small>
                                      @else
                                          <small class="text-muted">Belum submit</small>
                                      @endif
                                  </div>
                              </div>
                          @endif
                      </div>
                  </div>
              </div>
          @endif
  
          @if($isSubmitted)
              <button class="btn btn-success" disabled><i class="bi bi-check-circle"></i> Anda Sudah Submit</button>
              <p class="text-muted mt-2">Disubmit pada: 
                  @if($assessment->assessor_id_1 == $assessorId)
                      {{ \Carbon\Carbon::parse($assessment->assessor_1_submitted_at)->format('d M Y, H:i') }}
                  @elseif($assessment->assessor_id_2 == $assessorId)
                      {{ \Carbon\Carbon::parse($assessment->assessor_2_submitted_at)->format('d M Y, H:i') }}
                  @elseif($assessment->assessor_id_3 == $assessorId)
                      {{ \Carbon\Carbon::parse($assessment->assessor_3_submitted_at)->format('d M Y, H:i') }}
                  @endif
              </p>
          @else
              <form action="{{ route('assessor.submit-final', ['camaba_id' => $camaba->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin submit penilaian akhir?');">
                  @csrf
                  <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Submit Penilaian Akhir</button>
              </form>
          @endif
      </div>
    </div>

</main>

{{-- CPMK Modal --}}
<div class="modal fade" id="cpmkModal" tabindex="-1" aria-labelledby="cpmkModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cpmkModalLabel">CPMK Mata Kuliah: <span id="matkulNameInModal"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>CPMK</th>
              </tr>
            </thead>
            <tbody id="cpmkTableBody">
              <!-- CPMK data will be dynamically added here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<style>
  .nav-pills .nav-link.active,
  .nav-pills .show > .nav-link {
    background-color: #0d6efd;
    color: white !important;
    font-weight: bold;
    transition: all 0.3s ease;
  }

  .nav-pills .nav-link:hover {
    background-color: #1987fb;
    color: white !important;
  }

  .tab-content {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
  }

  .tab-content.active {
    display: block;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
<script>
  function showTab(tabName) {
    // Logika untuk menampilkan/menyembunyikan konten tab
    document.querySelectorAll('.tab-content').forEach(content => {
      content.classList.remove('active');
    });

    document.getElementById(tabName).classList.add('active');

    // Update kelas aktif pada tombol tab
    document.querySelectorAll('.nav-link').forEach(btn => {
      btn.classList.remove('active');
    });

    event.currentTarget.classList.add('active');
  }

  // Script untuk CPMK Modal
  document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi modal Bootstrap
    var cpmkModal = new bootstrap.Modal(document.getElementById('cpmkModal'));
    var matkulNameInModal = document.getElementById('matkulNameInModal');
    var cpmkTableBody = document.getElementById('cpmkTableBody');

    // Event listener untuk tombol View CPMK
    document.querySelectorAll('.view-cpmk-btn').forEach(button => {
      button.addEventListener('click', function() {
        const matkulId = this.getAttribute('data-matkul-id');
        const matkulName = this.getAttribute('data-matkul-name');

        matkulNameInModal.textContent = matkulName;
        cpmkTableBody.innerHTML = ''; // Clear previous table rows

        // Show loading message
        const loadingRow = document.createElement('tr');
        const loadingCell = document.createElement('td');
        loadingCell.setAttribute('colspan', '2');
        loadingCell.textContent = 'Loading CPMK...';
        loadingCell.style.textAlign = 'center';
        loadingRow.appendChild(loadingCell);
        cpmkTableBody.appendChild(loadingRow);

        // Fetch CPMK data
        fetch(`/assessor/matkul/${matkulId}/cpmk`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            cpmkTableBody.innerHTML = ''; // Clear loading message
            if (data.length > 0) {
              data.forEach((cpmk, index) => {
                const row = document.createElement('tr');
                const noCell = document.createElement('td');
                const cpmkCell = document.createElement('td');

                noCell.textContent = index + 1;
                cpmkCell.textContent = cpmk.penjelasan;

                row.appendChild(noCell);
                row.appendChild(cpmkCell);
                cpmkTableBody.appendChild(row);
              });
            } else {
              const noCpmkRow = document.createElement('tr');
              const noCpmkCell = document.createElement('td');
              noCpmkCell.setAttribute('colspan', '2');
              noCpmkCell.textContent = 'Tidak ada CPMK untuk mata kuliah ini.';
              noCpmkCell.style.textAlign = 'center';
              noCpmkRow.appendChild(noCpmkCell);
              cpmkTableBody.appendChild(noCpmkRow);
            }
          })
          .catch(error => {
            console.error('Error fetching CPMK:', error);
            cpmkTableBody.innerHTML = ''; // Clear loading message
            const errorRow = document.createElement('tr');
            const errorCell = document.createElement('td');
            errorCell.setAttribute('colspan', '2');
            errorCell.textContent = 'Error loading CPMK.';
            errorCell.style.textAlign = 'center';
            errorRow.appendChild(errorCell);
            cpmkTableBody.appendChild(errorRow);
          });

        cpmkModal.show();
      });
    });
  });

  function validateForm() {
      const assessments = document.querySelectorAll('input[type="radio"]:checked');
      if (assessments.length === 0) {
          alert('Silakan pilih minimal satu penilaian untuk setiap mata kuliah yang akan dinilai.');
          return false;
      }
      return true;
  }
</script>

<div class="modal fade" id="unassessedWarningModal" tabindex="-1" aria-labelledby="unassessedWarningModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="unassessedWarningModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Peringatan</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Anda belum memberikan penilaian untuk semua mata kuliah. Masih ada mata kuliah yang perlu diisi:</p>
        <ul id="unassessed-list">
          @if(session('unassessed_matkuls'))
            @foreach(session('unassessed_matkuls') as $matkul)
              <li><strong>{{ $matkul }}</strong></li>
            @endforeach
          @endif
        </ul>
        <p class="mt-3">Apakah Anda yakin ingin tetap melakukan submit?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal, saya akan melengkapi</button>
        <form id="forceSubmitForm" action="{{ route('assessor.submit-final', ['camaba_id' => $camaba->id]) }}" method="POST">
            @csrf
            {{-- Input tersembunyi untuk menandakan submit paksa --}}
            <input type="hidden" name="force_submit" value="1"> 
            <button type="submit" class="btn btn-warning">Ya, Tetap Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cek jika session 'show_warning_modal' ada
    @if(session('show_warning_modal'))
        var unassessedModal = new bootstrap.Modal(document.getElementById('unassessedWarningModal'));
        unassessedModal.show();
    @endif

    // Menangani form submit utama untuk memunculkan konfirmasi standar
    const mainSubmitForm = document.querySelector('form[action="{{ route('assessor.submit-final', ['camaba_id' => $camaba->id]) }}"]:not(#forceSubmitForm)');
    if (mainSubmitForm) {
        mainSubmitForm.addEventListener('submit', function(event) {
            // Hentikan submit default untuk menampilkan konfirmasi
            event.preventDefault();
            
            if (confirm('Apakah Anda yakin ingin submit penilaian akhir? Proses ini akan memeriksa kelengkapan data.')) {
                // Jika user setuju, lanjutkan submit form
                this.submit();
            }
        });
    }
});
</script>
@endpush

</body>
</html>