@extends('layout.admin')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Kelola CPMK</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
        <li class="breadcrumb-item">List Mata Kuliah</li>
        <li class="breadcrumb-item active">Kelola CPMK</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <div class="col-lg-12">


        <div class="card">
          <div class="card-body">
            <h5 class="card-title center" align="center">Kelola CPMK</h5>
            <button class="btn btn-primary mb-3 float-end" data-toggle="modal"
              data-target="#tambahModal">Tambah</button>

            <!-- Default Table -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th width="100">Kode CPMK</th>
                  <th width="500">CPMK</th>
                  <th width="50">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($cpmks as $index => $cpmk)
                <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $cpmk->kode_cpmk }}</td>
                <td>{{ $cpmk->penjelasan }}</td>
                <td>
                    <!-- Trigger the modal with a button -->
                    <a type="button" class="bi-trash fs-2" data-toggle="modal" data-target="#deleteModal{{ $cpmk->id }}">
                    </a>
                  

                    <!-- Modal -->
                    <div class="modal fade" id="deleteModal{{ $cpmk->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Peringatan!</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Apakah Kamu Yakin ingin menghapus CPMK ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <form action="{{ route('delete-cpmk', $cpmk->id) }}" method="POST" style="display: inline;">
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
                    <h5 class="modal-title" id="tambahModalLabel">Tambah CPMK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form id="tambahForm" method="POST" action={{ route("add-data-cpmk") }}>
                      @csrf
                      <input type="hidden" name="matkul_id" value="{{ $matkul->id }}">
                      <div class="col-12 mb-3">
                        <label for="kode_cpmk" class="form-label">Kode CPMK</label>
                        <input type="text" name="kode_cpmk" class="form-control" id="kode_cpmk" required>
                        <div class="invalid-feedback">Tolong masukkan kode CPMK!</div>
                      </div>
                      <div class="col-12 mb-3">
                        <label for="penjelasan" class="form-label">Penjelasan CPMK</label>
                        <input type="text" name="penjelasan" class="form-control" id="penjelasan" required>
                        <div class="invalid-feedback">Tolong masukkan penjelasan CPMK!</div>
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