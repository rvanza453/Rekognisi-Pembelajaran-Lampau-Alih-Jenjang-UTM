@extends('layout.super_admin')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Bukti</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
        <li class="breadcrumb-item active">Bukti</li>
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

            <!-- Dropdown for selecting Jurusan -->
            <form class="row g-3" action="{{ route('kelola-matkul-table') }}" method="GET">
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
            
            <!-- Default Table -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th width="600">Mata Kuliah</th>
                  <th width="100">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($matkuls as $index => $matkul)
                    <tr data-jurusan-id="{{ $matkul->jurusan_id ?? '' }}">
                      <td>{{ $index + 1 }}</td>
                      <td>
                        @if($matkul->nama_matkul)
                            {{ $matkul->nama_matkul }}
                        @else
                            Data tidak tersedia
                        @endif
                      </td>
                      <td>
                            <!-- Trigger the modal with a button (trash icon) -->
                            <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteMatkulModal{{ $matkul->id }}"></i>

                            <!-- Link to manage CPMK -->
                            <a type="button" href="{{ route('kelola-cpmk-table', $matkul->id) }}" class="bi-box-arrow-in-right fs-2"></a>

                            <!-- Modal -->
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
                                            <form action="{{ route('delete-matkul', $matkul->id) }}" method="POST" style="display: inline;">
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
            <!-- End Default Table Example -->

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
                    <form id="tambahForm" action="{{ 'kelola-matkul-add-data' }}" method="POST">
                      @csrf
                      <div class="col-12 mb-3">
                        <label for="nama_matkul" class="form-label">Nama Matkul</label>
                        <input type="text" name="nama_matkul" class="form-control" id="nama_matkul" required>
                        <div class="invalid-feedback">Tolong masukkan matkul yang ingin anda tambahkan!</div>
                      </div>
                      <div class="col-12 mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <select name="jurusan_id" id="jurusan" class="form-select" required>
                          @foreach($jurusans as $jurusan)
                              <option value="{{ $jurusan->id }}">{{ $jurusan->nama_jurusan }}</option>
                          @endforeach
                        </select>
                        <div class="invalid-feedback">Tolong masukkan matkul yang ingin anda tambahkan!</div>
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