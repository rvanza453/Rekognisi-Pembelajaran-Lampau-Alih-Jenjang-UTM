@extends('layout.super_admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Assessor</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/super/dashboard">Home</a></li>
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

              <!-- Form filter untuk memilih jurusan -->
              <form method="GET" action="{{ route('super.data-assessor-table') }}" id="filterForm">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="jurusan_filter" class="form-label">Filter Jurusan</label>
                    <select name="jurusan_id" id="jurusan_filter" class="form-select">
                      <option value="">Semua Jurusan</option>
                      @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                          {{ $jurusan->nama_jurusan }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </form>

              <!-- Tabel dengan baris yang di-strip -->
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
                        $assessorData = $assessor->firstWhere('user_id', $user->id);
                    @endphp
                    <tr>
                      <th scope="row">{{ $index + 1 }}</th>
                      <td>{{ $assessorData->nama ?? '-' }}</td>
                      <td>{{ $user->email ?? '-' }}</td>
                      <td>{{ $assessorData && $assessorData->jurusan ? $assessorData->jurusan->nama_jurusan : '-' }}</td>
                      <td>{{ $assessorData->no_hp ?? '-' }}</td>
                      <td class="alamat">{{ $assessorData->alamat ?? '-' }}</td>
                      <td>
                        <div class="btn-group" role="group">
                          @if($assessorData)
                          <a href="{{ route('super.view-assessor-students', $assessorData->id) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Lihat Mahasiswa
                          </a>
                          @else
                          <span class="text-muted">-</span>
                          @endif
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- Akhir tabel dengan baris yang di-strip -->

            </div>
          </div>

          
        </div>
      </div>
    </section>


  </main><!-- End #main -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form ketika filter jurusan berubah
    document.getElementById('jurusan_filter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection
