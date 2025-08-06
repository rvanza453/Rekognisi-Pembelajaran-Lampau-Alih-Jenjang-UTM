@extends('layout.super_admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
    <h1>Tambah Akun Assessor</h1>
      <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item">Akun Assessor</li>
            <li class="breadcrumb-item active">Form Tambah Akun Assessor</li>
        </ol>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center"><b>Form Tambah Akun Assessor</b></h5>

              <!-- Vertical Form -->
              <form class="row g-3" action="{{ route('super.account-assessor-add-data') }}" method="POST">
                @csrf
                    <div class="col-6">
                      <label for="yourName" class="form-label">Nama</label>
                      <input type="text" name="nama" class="form-control" id="yourName" required>
                      <div class="invalid-feedback">Mohon, Masukkan Nama Anda!</div>
                    </div>

                    <div class="col-6">
                      <label for="yourEmail" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" required>
                      <div class="invalid-feedback">Mohon, Masukkan Nama Anda!</div>
                    </div>

                    <div class="col-6">
                      <label for="yourUsername" class="form-label">Username</label>
                      <div class="input-group has-validation">
                        <input type="text" name="username" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Tolong masukkan Username Anda!</div>
                      </div>
                    </div>

                    <div class="col-6">
                      <label for="yourPassword" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Tolong masukkan Password Anda!</div>
                    </div>

                    

                    <div class="col-6">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select name="jurusan_id" id="jurusan" class="form-select" required>
                            @foreach($jurusan as $jurusan)
                               <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Mohon, Masukkan Jurusan Anda!</div>
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
                
                <div align="Right">
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form><!-- Vertical Form -->

            </div>
          </div>


        </div>
      </div>
    </section>

  </main><!-- End #main -->
@endsection
