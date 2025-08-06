@extends('layout.user')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Form Layouts</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active">Layouts</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row justify-content-center">

          <div class="col-xl-11" >

            <div class="card">
              <div class="card-body pt-3">

                <h5 class="card-title center" align="center">Overview</h5>
                <div class="tab-content pt-2">

                    <a type="button" href="/profile-edit-camaba" class="btn btn-primary align-right float-end me-4">Ubah Data</a>

                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                    <h5 class="card-title">Foto Profil</h5>
                  <img src="assets/img/profile-img.jpg" alt="Profile">
                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Nama Lengkap</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->nama ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Tempat Lahir</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->tempat_lahir ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->tanggal_lahir ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8">{{ Auth::user()->email ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">No. Hp</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->no_hp ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">No. Whatsapp</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->no_wa ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kelamin ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Alamat</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->alamat ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Kota</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kota ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Provinsi</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->provinsi ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Kode Pos</div>
                    <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kode_pos ?? 'Mohon masukkan data anda!!' }}</div>
                  </div>

                  </div>

                  <div class="tab-pane fade show active profile-edit pt-3" id="profile-edit">

                  </div>

                </div><!-- End Bordered Tabs -->

              </div>
            </div>

          </div>
        </div>
      </section>


  </main><!-- End #main -->
@endsection