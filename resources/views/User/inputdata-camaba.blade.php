@extends('layout.user')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row justify-content-center">

        <div class="col-xl-11">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

              </ul>

              
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form method="POST" action="{{ route('data_camaba') }}">
                    @csrf
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="assets/img/profile-img.jpg" alt="Profile">
                        <div class="pt-2">
                          <a href="#" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a>
                          <a href="#" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nama" class="col-md-4 col-lg-3 col-form-label">Nama Calon</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="inputNama" name="nama" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="jurusan" class="col-md-4 col-lg-3 col-form-label">Jurusan</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="jurusan" type="text" class="form-control" id="jurusan" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="tempat_lahir" class="col-md-4 col-lg-3 col-form-label">Tempat Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tempat_lahir" type="text" class="form-control" id="tempat_lahir"  required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="tanggal_lahir" class="col-md-4 col-lg-3 col-form-label">Tanggal Lahir</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="tanggal_lahir" type="date" class="form-control" id="tanggal_lahir"  required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="no_hp" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="no_hp" type="text" class="form-control" id="no_hp" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="no_wa" class="col-md-4 col-lg-3 col-form-label">Nomor Whatsapp</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="no_wa" type="text" class="form-control" id="no_wa" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="kelamin" class="col-md-4 col-lg-3 col-form-label">Jenis Kelamin</label>
                      <div class="col-md-8 col-lg-9">
                        <select class="form-select" id="kelamin" name="kelamin" required>
                          <option value="laki-laki">Laki-laki</option>
                          <option value="perempuan">Perempuan</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="alamat" class="col-md-4 col-lg-3 col-form-label">Alamat</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="alamat" name="alamat" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="kota" class="col-md-4 col-lg-3 col-form-label">Kota</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="kota" name="kota" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputProvinsi" class="col-md-4 col-lg-3 col-form-label">Provinsi</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="inputProvinsi" name="provinsi" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputKodePos" class="col-md-4 col-lg-3 col-form-label">Kode Pos</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="inputKodePos" name="kode_pos" required>
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
                              <button type="submit" class="btn btn-primary">Submit</button>
                          </div>
                          </div>
                      </div>
                    </div>

                    <div class="text-end">
                      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">Submit</button>
                      <button type="reset" class="btn btn-secondary">Reset</button>
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
