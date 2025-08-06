@extends('layout.super_admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Akun Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item active">Akun Admin</li>
        </ol>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Akun Admin</h5>
              <a href="{{ route('super.account-admin-add') }}" class="btn btn-primary mb-3 float-end">Tambah</a>

              <!-- Dropdown for selecting Jurusan -->
              <form class="row g-3" action="{{ route('super.account-admin-table') }}" method="GET">
                <div class="col-6 my-3">
                    <label for="jurusan" class="form-label">Pilih Jurusan</label>
                    <div>
                        <select name="jurusan_id" id="jurusan" class="form-select" required onchange="this.form.submit()">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusans as $jurusan)
                                <option value="{{ $jurusan->id }}" {{ $jurusan_id == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
              </form>

              <!-- Default Table -->
              <div class="table-container">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Nama</th>
                      <th scope="col">Email</th>
                      <th scope="col">Username</th>
                      <th scope="col">Password</th>
                      <!-- <th scope="col">No.HP</th> -->
                      <th scope="col">Jurusan</th>
                      <!-- <th scope="col">Kelamin</th> -->
                      <th scope="col">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users_admin as $index => $user)
                        <tr data-jurusan-id="{{ $user->admin->jurusan_id ?? '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($user->admin)
                                    {{ $user->admin->nama }}
                                @else
                                    Data tidak tersedia
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ 'rahasia' }}</td>
                            <td>
                                @if($user->admin && $user->admin->jurusan)
                                    {{ $user->admin->jurusan->nama_jurusan }}
                                @else
                                    Data tidak tersedia
                                @endif
                            </td>
                            <td>
                                <!-- Trigger the modal with a button (trash icon) -->
                                <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteAdminModal{{ $user->id }}"></i>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteAdminModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteAdminModalTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteAdminModalTitle">Peringatan!</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah kamu yakin ingin menghapus akun <strong>{{ $user->admin ? $user->admin->nama : 'Pengguna' }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <form action="{{ route('super.delete-user', $user->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                  </tbody>
                </table>
              </div>
              <!-- End Default Table Example -->

            </div>
          </div>


        </div>
      </div>
    </section>


  </main><!-- End #main -->
@endsection