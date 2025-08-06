@extends('layout.assessor')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List Ajuan Form</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">List Ajuan Form</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">List Nama Mahasiswa</h5>

              <!-- Default Table -->
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Program Studi</th>
                        <th scope="col">Deadline Penilaian</th>
                        <th scope="col">Status RPL</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($camaba as $index => $mahasiswa)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $mahasiswa->nama }}</td>
                            <td>{{ $mahasiswa->jurusan->nama_jurusan }}</td>
                            <td>
                              @if($mahasiswa->assessment && $mahasiswa->assessment->deadline)
                                {{ \Carbon\Carbon::parse($mahasiswa->assessment->deadline)->format('d/m/Y H:i') }}
                                @php
                                    $deadline = $mahasiswa->assessment->deadline;
                                    $now = now();
                                @endphp
                                @if($mahasiswa->assessment->rpl_status == 'selesai')
                                    <div><span class="badge bg-success mt-1">Selesai</span></div>
                                @else
                                    @if($deadline < $now)
                                        <span class="badge bg-danger">Terlambat</span>
                                    @elseif($deadline <= $now->addDays(3))
                                        <span class="badge bg-warning">Mendekati Deadline</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                @endif
                              @else
                                  <span class="text-muted">Belum diatur</span>
                              @endif
                            </td>
                            <td>
                              @if($mahasiswa->assessment && $mahasiswa->assessment->rpl_status)
                                  <span class="badge bg-info">{{ ucfirst(str_replace('-', ' ', $mahasiswa->assessment->rpl_status)) }}</span>
                              @else
                                  <span class="badge bg-secondary">-</span>
                              @endif
                            </td>
                            <td>
                                @if($mahasiswa->id)
                                    <a type="button" 
                                       href="{{ route('detail-user', ['id' => $mahasiswa->id]) }}" 
                                       class="bi-box-arrow-right fs-2"
                                       onclick="console.log('Clicked ID: {{ $mahasiswa->id }}')">
                                    </a>
                                @else
                                    <span class="text-danger">ID tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
              <!-- End Default Table Example -->

            </div>
          </div>

          
        </div>
      </div>
    </section>


  </main><!-- End #main -->
@endsection