@extends('layout.assessor')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
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
                  <form method="POST" action="{{ route('profile-edit-assessor', $assessor->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                          @if ($assessor->foto) 
                              <img src="{{ asset('Data/profile_pict_assesor/' . $assessor->foto) }}" alt="Profile" class="img-fluid"> 
                          @else
                              <img src="{{ asset('assets/img/profile-img.jpg') }}" alt="Profile">
                          @endif
                          <div class="pt-2">
                              <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*"> 
                          </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="nama" class="col-md-4 col-lg-3 col-form-label">Nama Assessor</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="nama" name="nama" value="{{ $assessor->nama }}" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="no_hp" class="col-md-4 col-lg-3 col-form-label">Nomor Telepon</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="no_hp" type="text" class="form-control" id="no_hp" value="{{ $assessor->no_hp }}">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="alamat" class="col-md-4 col-lg-3 col-form-label">Alamat</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $assessor->alamat }}">
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
