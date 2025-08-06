@extends('layout.assessor')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Form Layouts</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Profil</li>
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

                    @if($assessor->id)
                      <a type="button" href="{{ route('profile-assessor-edit-view', $assessor->id) }}" class="btn btn-primary align-right float-end me-4">Ubah Data</a>
                    @endif
  
                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                    <h5 class="card-title">Foto Profil</h5>
                    <div align="center">
                        @if ($assessor->foto) 
                          <div style="width: 200px; height: 200px; overflow: hidden; position: relative; border-radius: 50%;">
                              <img src="{{ asset('Data/profile_pict_assesor/' . $assessor->foto) }}" alt="Profile" 
                                   style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                          </div>
                        @else
                          <div style="width: 200px; height: 200px; overflow: hidden; position: relative; border-radius: 50%;">
                              <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile"
                                   style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                          </div>
                        @endif
                    </div>
                    <h5 class="card-title">Profile Details</h5>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Nama Lengkap</div>
                      <div class="col-lg-9 col-md-8">{{ $assessor->nama ?? '-' }}</div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Jurusan</div>
                      <div class="col-lg-9 col-md-8"></div>
                    </div>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Email</div>
                      <div class="col-lg-9 col-md-8">{{ $user->email ?? '-' }}</div>
                    </div>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">No. Hp</div>
                      <div class="col-lg-9 col-md-8">{{ $assessor->no_telp ?? '-' }}</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 label">Alamat</div>
                        <div class="col-lg-9 col-md-8">{{ $assessor->alamat ?? '-' }}</div>
                    </div>

                  </div>
  
                  
  
                </div><!-- End Bordered Tabs -->
  
              </div>
            </div>
  
          </div>
        </div>
      </section>


  </main><!-- End #main -->
@endsection