@extends('layout.admin')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Mahasiswa Assessor: {{ $assessor->nama }}</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('data-assessor-table') }}">Data Assessor</a></li>
          <li class="breadcrumb-item active">Mahasiswa Assessor</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Mahasiswa yang Ditugaskan ke {{ $assessor->nama }}</h5>
              
              @if($assignedStudents->isEmpty())
                <div class="alert alert-info" role="alert">
                  Assessor ini belum ditugaskan untuk menilai mahasiswa manapun.
                </div>
              @else
                <!-- Table with stripped rows -->
                <div class="table-container">
                  <table class="table table-striped table-hover">
                    <thead>
                      <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Mahasiswa</th>
                        <th scope="col">Email</th>
                        <th scope="col">Jurusan</th>
                        <th scope="col">Periode</th>
                        <th scope="col">Status Assessment</th>
                        <th scope="col">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($assignedStudents as $index => $student)
                      @php
                          $assessment = $student->assessment;
                          $assessorRole = '';
                          if ($assessment) {
                              if ($assessment->assessor_id_1 == $assessor->id) {
                                  $assessorRole = 'Assessor 1';
                              } elseif ($assessment->assessor_id_2 == $assessor->id) {
                                  $assessorRole = 'Assessor 2';
                              } elseif ($assessment->assessor_id_3 == $assessor->id) {
                                  $assessorRole = 'Assessor 3';
                              }
                          }
                      @endphp
                      <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $student->nama ?? '-' }}</td>
                        <td>{{ $student->user->email ?? '-' }}</td>
                        <td>{{ $student->jurusan->nama_jurusan ?? '-' }}</td>
                        <td>{{ $student->periode->tahun_ajaran ?? '-' }}</td>
                        <td>
                            @if($student->assessment && $student->assessment->rpl_status)
                                <span class="badge bg-info">{{ ucfirst(str_replace('-', ' ', $student->assessment->rpl_status)) }}</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <a href="{{ route('admin.view-student-as-assessor', [$assessor->id, $student->id]) }}" class="btn btn-primary btn-sm">
                              <i class="bi bi-eye"></i> Lihat sebagai Assessor
                            </a>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- End Table with stripped rows -->
              @endif

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

@endsection 