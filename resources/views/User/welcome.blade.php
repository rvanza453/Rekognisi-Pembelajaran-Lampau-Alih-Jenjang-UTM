@extends('layout.user')
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

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

                <!-- Slides with captions -->
                <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <div class="carousel-item active">
                      <img src="{{ asset('assets/img/background-utm.png') }}" class="d-block w-100" alt="...">
                      <div class="carousel-caption d-none d-md-block">
                        <h5 class="text-dark"><b>RPL Lintas Jenjang</b></h5>
                        <p class="text-dark">Sistem Informasi Manajemen Formulir Aplikasi RPL & E-Portofolio</p>
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
