@extends('layout.user')
@section('content')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Self Assessment</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Mata Kuliah</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Mata Kuliah yang akan di Konversi</h5>
              <a href="/self-assessment"><button type="button" class="btn btn-primary mb-3 float-end">Tambah</button></a>

              <!-- Form untuk Memilih Mata Kuliah -->
              <form class="row g-3" action="{{ route('self-assessment-table') }}" method="GET">
                <div class="col-6 my-3">
                  <label for="matkul" class="form-label">Pilih Mata Kuliah</label>
                  <div>
                    <select name="matkul_id" id="matkul" class="form-select" required onchange="this.form.submit()">
                      @foreach($matkuls as $matkul)
                        <option value="{{ $matkul->id }}" {{ $matkul_id == $matkul->id ? 'selected' : '' }}>{{ $matkul->nama_matkul }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </form>

              <!-- Default Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">CPMK</th>
                    <th scope="col">Status</th>
                    <th scope="col">Nilai</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($assessments as $index => $assessment)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $assessment->cpmk->penjelasan }}</td>
                    <td>
                      @if($assessment->nilai)
                        Persyaratan Terpenuhi
                      @else
                        Belum Memenuhi Penilaian
                      @endif
                    </td>
                    <td>{{ $assessment->nilai ?? '-' }}</td>
                    <td><i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteAssessmentModal{{ $assessment->id }}"></i>
                        <a href="{{ route('self-assessment', [
                            'matkul_id' => $matkul_id,
                            'cpmk_id' => $assessment->cpmk_id,
                            'nilai' => $assessment->nilai,
                            'bukti_id' => $assessment->bukti_id
                        ]) }}" class="btn btn-outline-primary ml-2">Edit
                        </a>
                  </tr>
                  <!-- Modal -->
                  <div class="modal fade" id="deleteAssessmentModal{{ $assessment->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteAssessmentModalTitle{{ $assessment->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="deleteAssessmentModalTitle{{ $assessment->id }}">Peringatan!</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  Apakah kamu yakin ingin menghapus penilaian <strong>{{ $assessment->cpmk->penjelasan  }}</strong>?
                              </div>
                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                  <form action="{{ route('delete-self-assessment', $assessment->id) }}" method="POST" style="display: inline;">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="btn btn-danger">Hapus</button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
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
