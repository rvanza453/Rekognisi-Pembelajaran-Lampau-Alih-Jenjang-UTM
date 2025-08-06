@extends('layout.assessor')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <!-- Deadline Statistics Cards -->
    @php
        $assessor = \App\Models\Assessor::where('user_id', auth()->id())->first();
        // Statistik penugasan AKTIF (self-assessment, penilaian assessor)
        $deadlineStats = $assessor ? \App\Models\Assessment::getAssessorDeadlineStatistics($assessor->id) : ['total' => 0, 'overdue' => 0, 'approaching' => 0];
        // Statistik SEMUA penugasan (termasuk selesai)
        $allAssignments = $assessor ? \App\Models\Assessment::whereNotNull('deadline')
            ->where(function($query) use ($assessor) {
                $query->where('assessor_id_1', $assessor->id)
                      ->orWhere('assessor_id_2', $assessor->id)
                      ->orWhere('assessor_id_3', $assessor->id);
            })->count() : 0;
        // Statistik penilaian selesai (status rpl_status selain self-assessment & penilaian assessor)
        $completedAssignments = $assessor ? \App\Models\Assessment::whereNotNull('deadline')
            ->whereNotIn('rpl_status', ['self-assessment', 'penilaian assessor'])
            ->where(function($query) use ($assessor) {
                $query->where('assessor_id_1', $assessor->id)
                      ->orWhere('assessor_id_2', $assessor->id)
                      ->orWhere('assessor_id_3', $assessor->id);
            })->count() : 0;
    @endphp

    <div class="row mb-4">
        <!-- Total Assessments Card -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Penilaian</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-clipboard-check"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $allAssignments }}</h6>
                  <span class="text-muted small pt-2">Semua penugasan (termasuk selesai & terlambat)</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Overdue Assessments Card -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Terlambat</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: #dc3545;">
                  <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $deadlineStats['overdue'] }}</h6>
                  <span class="text-muted small pt-2">Deadline telah lewat</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Approaching Deadlines Card -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Mendekati Deadline</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: #ffc107;">
                  <i class="bi bi-clock"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $deadlineStats['approaching'] }}</h6>
                  <span class="text-muted small pt-2">≤ 3 hari lagi</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Completed Assessments Card -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Penilaian Selesai</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: #198754;">
                  <i class="bi bi-check-circle"></i>
                </div>
                <div class="ps-3">
                  <h6>{{ $completedAssignments }}</h6>
                  <span class="text-muted small pt-2">Penugasan yang sudah selesai</span>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Aksi Cepat</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('list-name-table') }}" class="btn btn-primary">
                            <i class="bi bi-list-check"></i> Daftar Penilaian
                        </a>
                        <a href="{{ route('profile-view-assessor') }}" class="btn btn-info">
                            <i class="bi bi-person"></i> Profil Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

                <!-- Slides with captions -->
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <img src="{{ asset('assets/img/background-upn.jpg') }}" class="d-block w-100" alt="...">
                      <div class="carousel-caption d-none d-md-block">
                        <h5 class="text-white"><b>RPL & E-PORTOFOLIO</b></h5>
                        <p class="text-white">Sistem Informasi Manajemen Formulir Aplikasi RPL & E-Portofolio</p>
                      </div>
                    </div>
                  </div>

                  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>

                </div><!-- End Slides with captions -->

                <div class="card my-4">
                  <div class="card-body">
                    <h5 class="card-title">Fitur RPL & E-Portofolio</h5>
                    <!-- List group With Icons -->
                    <ul class="list-group">
                      <li class="list-group-item"><i class="bi bi-person-check"></i>  Biodata Lengkap Calon Mahasiswa RPL dan Pengalaman Kerja</li>
                      <li class="list-group-item"><i class="bi bi-book"></i> Konversi Smart Data Calon Mahasiswa RPL Ke Matakuliah</li>
                      <li class="list-group-item"><i class="bi bi-bookmark-check"></i> Luaran:E-Portofolio, Transkrip Konversi, Laporan Pelaksanaan</li>
                    </ul><!-- End List group With Icons -->

                  </div>
                </div>

      </div>
    </section>

  </main><!-- End #main -->

@endsection
