@extends('layout.user')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Ijazah</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Ijazah</li>
        <li class="breadcrumb-item active">Edit Ijazah</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <div class="col-lg-12">


        <div class="card">
          <div class="card-body">
            <h5 class="card-title center" align="center">Ijazah Pendidikan Sebelumnya</h5>

            <!-- Vertical Form -->
            <form class="row g-3" method="POST" action="{{ route('ijazah_edit', ['id' => $ijazah->id]) }}" enctype="multipart/form-data">
                @csrf
                <div class="col-6">
                  <label for="institusi_pendidikan" class="form-label">Nama Perguruan Tinggi/Sekolah</label>
                  <input type="text" class="form-control" id="institusi_pendidikan" name="institusi_pendidikan" value="{{ old('institusi_pendidikan', $ijazah->institusi_pendidikan) }}" required>
                </div>
                <div class="col-6">
                  <label for="jenjang" class="form-label">Jenjang</label>
                  <select class="form-select" id="jenjang" name="jenjang" required>
                      <option value="SMA" {{ old('jenjang', $ijazah->jenjang) == 'SMA' ? 'selected' : '' }}>SMA</option>
                      <option value="S1" {{ old('jenjang', $ijazah->jenjang) == 'S1' ? 'selected' : '' }}>S1</option>
                  </select>
                </div>
                <div class="col-6">
                  <label for="kota" class="form-label">Kota</label>
                  <input type="text" class="form-control" id="kota" name="kota" value="{{ old('kota', $ijazah->kota) }}" required>
                </div>
                <div class="col-6">
                  <label for="provinsi" class="form-label">Provinsi</label>
                  <input type="text" class="form-control" id="provinsi" name="provinsi" value="{{ old('provinsi', $ijazah->provinsi) }}" required>
                </div>
                <div class="col-6">
                  <label for="negara" class="form-label">Negara</label>
                  <input type="text" class="form-control" id="negara" name="negara" value="{{ old('negara', $ijazah->negara) }}" required>
                </div>
                <div class="col-6">
                  <label for="fakultas" class="form-label">Fakultas</label>
                  <input type="text" class="form-control" id="fakultas" name="fakultas" value="{{ old('fakultas', $ijazah->fakultas) }}" >
                </div>
                <div class="col-6">
                  <label for="jurusan" class="form-label">Jurusan</label>
                  <input type="text" class="form-control" id="jurusan" name="jurusan" value="{{ old('jurusan', $ijazah->jurusan) }}" required>
                </div>
                <div class="col-6">
                  <label for="ipk_nilai" class="form-label">IPK/Nilai</label>
                  <input type="text" class="form-control" id="ipk_nilai" name="ipk_nilai" value="{{ old('ipk_nilai', $ijazah->ipk_nilai) }}" required>
                </div>
                <div class="col-6">
                  <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                  <input type="number" class="form-control" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $ijazah->tahun_lulus) }}" required>
                </div>
                <div class="col-6">
                  <label for="formFile" class="form-label">Upload Bukti</label>
                  <input class="form-control" type="file" id="formFile" name="file">
                                @if($ijazah->file)
                                    <small class="text-muted">File saat ini: {{ basename($ijazah->file) }}</small>
                                @endif
                </div>
                <div align="right">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form>

          </div>
        </div>


      </div>
    </div>
  </section>

</main><!-- End #main -->
@endsection