@extends('layout.admin')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Assessor</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Data Assessor</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Data Assessor</h5>

              <!-- Table with stripped rows -->
              <div class="table-container">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Nama</th>
                      <th scope="col">Email</th>
                      <th scope="col">Jurusan</th>
                      <th scope="col">No. HP</th>
                      <th scope="col">Alamat</th>
                      <th scope="col">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users_assessor as $index => $user)
                    @php
                        $assessor = $assessor->firstWhere('user_id', $user->id);
                    @endphp
                    <tr>
                      <th scope="row">{{ $index + 1 }}</th>
                      <td>{{ $assessor->nama ?? '-' }}</td>
                      <td>{{ $user->email ?? '-' }}</td>
                      <td>{{ $assessor->jurusan->nama_jurusan ?? '-' }}</td>
                      <td>{{ $assessor->no_hp ?? '-' }}</td>
                      <td class="alamat">{{ $assessor->alamat ?? '-' }}</td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="{{ route('admin.view-assessor-students', $assessor->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Lihat Mahasiswa
                          </a>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

@endsection
