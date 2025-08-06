@extends('layout.user')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Ijazah</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item active">Ijazah</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row card">

      <h5 class="card-title center" align="center">Ijazah Pendidikan Terakhir</h5>
      <a href="{{ route('ijazah-edit-view', $ijazah->id) }}"><button type="button" class="btn btn-warning my-3 mr-3 float-end">Edit</button></a>

      <div class="ijazah-overview">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Institusi Pendidikan</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->institusi_pendidikan }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Jenjang</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->jenjang }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Provinsi</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->provinsi }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Kota</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->kota }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Negara</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->negara }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Fakultas</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->fakultas }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Jurusan</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->jurusan }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Nilai/IPK</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->ipk_nilai }}</div>
        </div>

        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 label">Tahun Lulus</div>
          <div class="col-lg-9 col-md-8">{{ $ijazah->tahun_lulus }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-3 col-md-4 label">Bukti File</div>
            <div class="col-lg-9 col-md-8">
                @if(Auth::user()->calon_mahasiswa && Auth::user()->calon_mahasiswa->ijazah)
                   <a href="{{ route('download-ijazah', basename($ijazah->file)) }}" class="btn btn-primary btn-sm">
                       <i class="bi bi-download"></i> Download Ijazah
                   </a>
                @else
                    <span class="text-muted">File tidak tersedia</span> 
                @endif
            </div>
        </div>
      </div>

    </div>
  </section>

</main><!-- End #main -->
@endsection