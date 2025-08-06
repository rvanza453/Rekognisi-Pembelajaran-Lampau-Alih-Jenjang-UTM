@extends('layout.assessor')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row justify-content-center">
  
          <div class="col-xl-11" >
  
            <div class="card">
              <div class="card-body pt-3">

                @if($assessor->id)
                  <a type="button" href="{{ route('profile-assessor-edit-view', $assessor->id) }}" class="btn btn-warning align-right float-end me-2 mt-3">Ubah Data</a>
                @endif
                    
                <h5 class="card-title center mt-5" align="center">Data Diri</h5>
                <div class="tab-content pt-2">
  
                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                 
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
                    <h5 class="card-title mt-3">Profile Details</h5>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Nama Lengkap</div>
                      <div class="col-lg-9 col-md-8">{{ $assessor->nama ?? '-' }}</div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Jurusan</div>
                      <div class="col-lg-9 col-md-8">{{ $assessor->jurusan->nama_jurusan ?? '-' }}</div>
                    </div>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Email</div>
                      <div class="col-lg-9 col-md-8">{{ $user->email ?? '-' }}</div>
                    </div>
  
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">No. Hp</div>
                      <div class="col-lg-9 col-md-8">{{ $assessor->no_hp ?? '-' }}</div>
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