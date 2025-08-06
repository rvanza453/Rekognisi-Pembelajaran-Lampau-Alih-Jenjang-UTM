@extends('layout.super_admin')
@section('content')
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Daftar Mahasiswa Mengajukan Banding (Semua Jurusan)</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Mahasiswa Banding</h5>
            
            <form method="GET" action="{{ route('super.daftar-banding') }}" id="filterForm">
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
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jurusan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($mahasiswa_banding as $camaba_id => $banding_group)
                    @php $camaba = $banding_group->first()->calon_mahasiswa; @endphp
                    
                    {{-- <<< PERBAIKAN DI SINI: Tambahkan @if untuk memastikan $camaba tidak null --}}
                    @if($camaba)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $camaba->nama ?? '-' }}</td>
                      <td>{{ $camaba->jurusan->nama_jurusan ?? '-' }}</td>
                      <td>
                        {{-- Pastikan route name benar untuk Super Admin --}}
                        <a href="{{ route('super.detail-banding-mahasiswa', $camaba->id) }}" class="btn btn-primary btn-sm">Lihat Detail Banding</a>
                      </td>
                    </tr>
                    @endif
                  @empty
                  <tr>
                    <td colspan="4" class="text-center">Tidak ada mahasiswa yang mengajukan banding.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form ketika filter jurusan berubah
    document.getElementById('jurusan_filter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection