@extends('layout.user')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Profil</li>
          <li class="breadcrumb-item active">Edit Profil</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row justify-content-center">

        <div class="col-xl-11" >

          <div class="card">
            <div class="card-body pt-3">
              
              <h5 class="card-title center" align="center">Ubah Data</h5>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST" action="{{ route('profile_edit_camaba', $calon_mahasiswa->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                        <div class="col-md-8 col-lg-9">
                            @if ($calon_mahasiswa->foto)
                                <div style="width: 200px; height: 200px; overflow: hidden; position: relative;">
                                    <img src="{{ asset('Data/profile_pict_camaba/' . $calon_mahasiswa->foto) }}" alt="Profile" 
                                         style="width: 100%; height: 100%; object-fit: cover; position: absolute;">
                                </div>
                            @else
                                <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile" style="width: 200px; height: 200px; object-fit: cover;">
                            @endif
                            <div class="pt-2">
                                <input type="file" class="form-control-file" id="foto" name="foto">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nama" class="col-md-4 col-lg-3 col-form-label">Nama Calon</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $calon_mahasiswa->nama }}" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="tempat_lahir" class="col-md-4 col-lg-3 col-form-label">Tempat Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tempat_lahir" type="text" class="form-control" id="tempat_lahir" value="{{ $calon_mahasiswa->tempat_lahir }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="tanggal_lahir" class="col-md-4 col-lg-3 col-form-label">Tanggal Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tanggal_lahir" type="date" class="form-control" id="tanggal_lahir" value="{{ $calon_mahasiswa->tanggal_lahir }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nomor_telepon" class="col-md-4 col-lg-3 col-form-label">Nomor WhatsApp</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="nomor_telepon" type="tel" class="form-control" id="nomor_telepon" 
                               value="{{ $calon_mahasiswa->nomor_telepon }}"
                               placeholder="Masukkan nomor WhatsApp">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nomor_rumah" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon Rumah</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="nomor_rumah" type="tel" class="form-control" id="nomor_rumah" 
                               value="{{ $calon_mahasiswa->nomor_rumah }}"
                               placeholder="Masukkan nomor telepon rumah">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nomor_kantor" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon Kantor</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="nomor_kantor" type="tel" class="form-control" id="nomor_kantor" 
                               value="{{ $calon_mahasiswa->nomor_kantor }}"
                               placeholder="Masukkan nomor telepon kantor">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="kebangsaan" class="col-md-4 col-lg-3 col-form-label">Kebangsaan</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="kebangsaan" type="text" class="form-control" id="kebangsaan" value="{{ $calon_mahasiswa->kebangsaan }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="kelamin" class="col-md-4 col-lg-3 col-form-label">Jenis Kelamin</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="kelamin" name="kelamin">
                          <option value="laki-laki" {{ $calon_mahasiswa->kelamin == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                          <option value="perempuan" {{ $calon_mahasiswa->kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="alamat" class="col-md-4 col-lg-3 col-form-label">Alamat</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $calon_mahasiswa->alamat }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="kota" class="col-md-4 col-lg-3 col-form-label">Kota</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="kota" name="kota" value="{{ $calon_mahasiswa->kota }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputProvinsi" class="col-md-4 col-lg-3 col-form-label">Provinsi</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="inputProvinsi" name="provinsi" value="{{ $calon_mahasiswa->provinsi }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputKodePos" class="col-md-4 col-lg-3 col-form-label">Kode Pos</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="inputKodePos" name="kode_pos" value="{{ $calon_mahasiswa->kode_pos }}">
                      </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLongTitle">Peringatan!</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <div class="modal-body">
                              Apakah Kamu Yakin ingin mengirim?
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Simpan</button>
                          </div>
                          </div>
                      </div>
                    </div>

                    <div class="text-end">
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">Simpan</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

@endsection
