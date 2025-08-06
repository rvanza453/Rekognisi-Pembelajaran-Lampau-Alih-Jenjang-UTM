@extends('layout.admin')
@section('content')
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Daftar Mahasiswa Mengajukan Banding</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Mahasiswa Banding</h5>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
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
                      <td>{{ $camaba->nim ?? '-' }}</td>
                      <td>
                        <a href="{{ route('admin.detail-banding-mahasiswa', $camaba->id) }}" class="btn btn-primary btn-sm">Lihat Detail Banding</a>
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
@endsection