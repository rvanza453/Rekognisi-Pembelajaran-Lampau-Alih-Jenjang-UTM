@extends('layout.user')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profil</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Profil</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row justify-content-center">
  
          <div class="col-xl-13" >
  
            <div class="card">
              <div class="card-body pt-3">

                <a type="button" href="{{ route('profile_edit_camaba_view', $calon_mahasiswa->id) }}" class="btn btn-warning align-right float-end me-2 mt-3">Ubah Data</a>
  

                <h5 class="card-title center mt-5" align="center">Data Diri</h5>
                <div class="tab-content pt-2">

                    <div class="tab-pane fade show active profile-overview" id="profile-overview">
                      
                        <div align="center" class="mt-2">
                            @if ($calon_mahasiswa->foto) 
                              <div style="width: 200px; height: 200px; overflow: hidden; position: relative; border-radius: 50%;">
                                  <img src="{{ asset('Data/profile_pict_camaba/' . $calon_mahasiswa->foto) }}" alt="Profile" 
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
                        <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->nama }}</div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Tempat Lahir</div>
                        <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->tempat_lahir }}</div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
                        <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->tanggal_lahir }}</div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Nomor WhatsApp</div>
                        <div class="col-lg-9 col-md-8">
                          @if($calon_mahasiswa->nomor_telepon)
                            +62{{ preg_replace('/^0/', '', $calon_mahasiswa->nomor_telepon) }}
                          @else
                            -
                          @endif
                        </div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Email</div>
                        <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Nomor Telepon Rumah</div>
                        <div class="col-lg-9 col-md-8">
                          @if($calon_mahasiswa->nomor_rumah)
                            +62{{ preg_replace('/^0/', '', $calon_mahasiswa->nomor_rumah) }}
                          @else
                            -
                          @endif
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Nomor Telepon Kantor</div>
                        <div class="col-lg-9 col-md-8">
                          @if($calon_mahasiswa->nomor_kantor)
                            +62{{ preg_replace('/^0/', '', $calon_mahasiswa->nomor_kantor) }}
                          @else
                            -
                          @endif
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Kebangsaan</div>
                        <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kebangsaan }}</div>
                      </div>
  
                      <div class="row">
                        <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
                        <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kelamin }}</div>
                      </div>

                      <div class="row">
                          <div class="col-lg-3 col-md-4 label">Alamat</div>
                          <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->alamat }}</div>
                      </div>
  
                      <div class="row">
                          <div class="col-lg-3 col-md-4 label">Kota</div>
                          <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kota }}</div>
                      </div>
  
                      <div class="row">
                          <div class="col-lg-3 col-md-4 label">Provinsi</div>
                          <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->provinsi }}</div>
                      </div>
  
                      <div class="row">
                          <div class="col-lg-3 col-md-4 label">Kode Pos</div>
                          <div class="col-lg-9 col-md-8">{{ $calon_mahasiswa->kode_pos }}</div>
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
