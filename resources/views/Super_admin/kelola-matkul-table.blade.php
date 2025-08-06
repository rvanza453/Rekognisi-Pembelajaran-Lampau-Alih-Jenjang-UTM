@extends('layout.super_admin')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Kelola Mata Kuliah</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
        <li class="breadcrumb-item active">Kelola Mata Kuliah</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <div class="col-lg-12">


        <div class="card">
          <div class="card-body">
            <h5 class="card-title center" align="center">List Mata Kuliah</h5>
            <button class="btn btn-primary mb-3 float-end" data-toggle="modal"
              data-target="#tambahModal">Tambah</button>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Dropdown untuk memilih jurusan -->
            <form class="row g-3" action="{{ route('super.kelola-matkul-table') }}" method="GET">
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
            
            <!-- Tabel default -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th width="150">Kode Matkul</th>
                  <th width="300">Mata Kuliah</th>
                  <th width="100">SKS</th>
                  <th width="100">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($matkuls as $index => $matkul)
                    <tr data-jurusan-id="{{ $matkul->jurusan_id ?? '' }}">
                      <td>{{ $index + 1 }}</td>
                      <td>
                        @if($matkul->kode_matkul)
                            {{ $matkul->kode_matkul }}
                        @else
                            -
                        @endif
                      </td>
                      <td>
                        @if($matkul->nama_matkul)
                            {{ $matkul->nama_matkul }}
                        @else
                            Data tidak tersedia
                        @endif
                      </td>
                      <td>
                        @if($matkul->sks)
                            {{ $matkul->sks }}
                        @else
                            -
                        @endif
                      </td>
                      <td>
                            <!-- Tombol edit -->
                            <i type="button" class="bi-pencil-square fs-3 me-2" data-toggle="modal" data-target="#editMatkulModal{{ $matkul->id }}"></i>
                            
                            <!-- Trigger modal dengan tombol (icon trash) -->
                            <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteMatkulModal{{ $matkul->id }}"></i>

                            <!-- Link untuk mengelola CPMK -->
                            <a type="button" href="{{ route('super.kelola-cpmk-table', $matkul->id) }}" class="bi-box-arrow-in-right fs-2"></a>

                            <!-- Modal edit -->
                            <div class="modal fade" id="editMatkulModal{{ $matkul->id }}" tabindex="-1" role="dialog" aria-labelledby="editMatkulModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editMatkulModalTitle">Edit Mata Kuliah</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('super.edit-matkul', $matkul->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group mb-3">
                                                    <label for="nama_matkul">Nama Matkul</label>
                                                    <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" value="{{ $matkul->nama_matkul }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kode_matkul">Kode Matkul</label>
                                                    <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" value="{{ $matkul->kode_matkul }}">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="sks">SKS</label>
                                                    <input type="number" class="form-control" id="sks" name="sks" value="{{ $matkul->sks }}" min="1" max="6">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal hapus -->
                            <div class="modal fade" id="deleteMatkulModal{{ $matkul->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteMatkulModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteMatkulModalTitle">Peringatan!</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah kamu yakin ingin menghapus mata kuliah <strong>{{ $matkul->nama_matkul }}</strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <form action="{{ route('super.delete-matkul', $matkul->id) }}" method="POST" style="display: inline;">
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
            <!-- Akhir tabel default -->

            <!-- Modal -->
            <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form id="tambahForm" action="{{ route('super.kelola-matkul-add-data') }}" method="POST">
                      @csrf
                      @if($errors->any())
                        <div class="alert alert-danger">
                          <ul class="mb-0">
                            @foreach($errors->all() as $error)
                              <li>{{ $error }}</li>
                            @endforeach
                          </ul>
                        </div>
                      @endif
                      <div class="col-12 mb-3">
                        <label for="nama_matkul" class="form-label">Nama Matkul</label>
                        <input type="text" name="nama_matkul" class="form-control @error('nama_matkul') is-invalid @enderror" id="nama_matkul" value="{{ old('nama_matkul') }}" required>
                        @error('nama_matkul')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-12 mb-3">
                        <label for="kode_matkul" class="form-label">Kode Matkul</label>
                        <input type="text" name="kode_matkul" class="form-control @error('kode_matkul') is-invalid @enderror" id="kode_matkul" value="{{ old('kode_matkul') }}">
                        @error('kode_matkul')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-12 mb-3">
                        <label for="sks" class="form-label">SKS</label>
                        <input type="number" name="sks" class="form-control @error('sks') is-invalid @enderror" id="sks" value="{{ old('sks') }}" min="1" max="6">
                        @error('sks')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <div class="col-12 mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select name="jurusan_id" id="jurusan" class="form-select @error('jurusan_id') is-invalid @enderror" required>
                          @foreach($jurusans as $jurusan)
                              <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                          @endforeach
                        </select>
                        @error('jurusan_id')
                          <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                      </div>
                      <button type="submit" class="btn btn-primary float-end me-3">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>
    </div>
  </section>


</main><!-- End #main -->
@endsection