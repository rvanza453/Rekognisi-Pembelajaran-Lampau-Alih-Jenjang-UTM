@extends('layout.admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Akun Assessor</h1>
      <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item active">Akun Assessor</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Akun Assesor</h5>
              <a href="/account-assessor-add"><button type="button" class="btn btn-primary mb-3 float-end" >Tambah</button></a>

              <!-- Dropdown for selecting Jurusan -->
              <form class="row g-3" action="{{ route('account-assessor-table') }}" method="GET">
                <div class="col-6 my-3">
                    
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
                      <!-- <th scope="col">Tempat Lahir</th>
                      <th scope="col">Tanggal Lahir</th>
                      <th scope="col">No.HP</th> -->
                      <!-- <th scope="col">Kelamin</th>
                      <th scope="col">Alamat</th> -->
                      <th scope="col">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users_assessor as $index => $user)
                        <tr data-jurusan-id="{{ $user->assessor->jurusan_id ?? '' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($user->assessor)
                                    {{ $user->assessor->nama }}
                                @else
                                    Data tidak tersedia
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ 'rahasia' }}</td>
                            <td>
                                <!-- Trigger the modal with a button (trash icon) -->
                                <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteAssessorModal{{ $user->id }}"></i>

                                <!-- Modal -->
                                <div class="modal fade" id="deleteAssessorModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteAssessorModalTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteAssessorModalTitle">Peringatan!</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah kamu yakin ingin menghapus akun <strong>{{ $user->assessor ? $user->assessor->nama : 'Pengguna' }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <form action="{{ route('delete-user', $user->id) }}" method="POST" style="display: inline;">
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