@extends('layout.assessor')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Form Mahasiswa</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item active"><a href="/list-name-table">Ajuan Form</a></li>
                <li class="breadcrumb-item active"><a href="/detail-user">Data Calon Mahasiswa</a></li>
                <li class="breadcrumb-item active"><a href="/form-user">Form Mahasiswa</a></li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    
    <section class="section">
        <div class="row">
            <div class="col-lg-13">
                <div class="card">
                    <div class="card-body"> 
                    <div class="container mt-5">

                        <br>
                        <h5 class="card-title text-center">{{ $matkul->nama_matkul }}</h5>
                        <!--<button type="button" class="btn btn-warning mb-3 float-end" data-toggle="modal" data-target="#exampleModalLongEdit">Edit</button>-->
                        
                        <div class="mt-3">
                            <div class="ijazah-overview-assessor mx-4 my-3">
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Score VATM</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $matkulScore ? $matkulScore->score : '-' }}
                                    </div>
                                </div>
                        
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Status</div>
                                    <div class="col-lg-9 col-md-8">
                                        {{ $matkulScore ? $matkulScore->status : '-' }}
                                    </div>
                                </div>
                        
                                <div class="row mb-3">
                                    <div class="col-lg-3 col-md-4 label">Nilai</div>
                                    @if($matkulScore && $matkulScore->status == 'Lolos')
                                        <button class="btn btn-warning col-lg-2 col-md-8" data-toggle="modal" data-target="#beriNilaiModal">Beri Nilai</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('input_calculate') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <input type="hidden" name="matkul_id" value="{{ $matkul->id }}">
                            <input type="hidden" name="assessor_id" value="{{ $assessor_id }}">
                            <input type="hidden" name="calon_mahasiswa_id" value="{{ $calon_mahasiswa_id }}">

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th rowspan="2">Kemampuan Akhir Yang Diharapkan/ Capaian Pembelajaran Mata Kuliah</th>
                                        <th colspan="3">Profisiensi pengetahuan dan keterampilan saat ini*</th>
                                        <th colspan="4">Hasil evaluasi Asesor (diisi oleh Asesor)</th>
                                        <th colspan="2">Bukti yang disampaikan*</th>
                                    </tr>
                                    <tr>
                                        <th>Sangat baik</th>
                                        <th>Baik</th>
                                        <th>Tidak pernah</th>
                                        <th>V</th>
                                        <th>A</th>
                                        <th>T</th>
                                        <th>M</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Jenis dokumen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($self_assessment_camaba as $index => $self_assessment)
                                        <tr>
                                            <td>{{ $self_assessment->cpmk->penjelasan ?? '-' }}</td>
                                            <td>@if($self_assessment->nilai == 'Sangat Baik') ✓ @endif</td>
                                            <td>@if($self_assessment->nilai == 'Baik') ✓ @endif</td>
                                            <td>@if($self_assessment->nilai == 'Tidak Pernah') ✓ @endif</td>
                                            <td><input type="checkbox" name="nilai[{{ $index }}][V]" value="1"></td>
                                            <td><input type="checkbox" name="nilai[{{ $index }}][A]" value="1"></td>
                                            <td><input type="checkbox" name="nilai[{{ $index }}][T]" value="1"></td>
                                            <td><input type="checkbox" name="nilai[{{ $index }}][M]" value="1"></td>
                                            <td></td>
                                            <td>
                                                @if($self_assessment->bukti && $self_assessment->bukti->file)
                                                    <a href="{{ asset('public/' . $self_assessment->bukti->file) }}" target="_blank">Lihat File</a>
                                                @else
                                                    <span>File tidak tersedia</span> 
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger mt-2" data-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-warning mt-2">Save</button>
                            </div>
                            @if (session('success'))
                                <div class="alert alert-success mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger mt-3">
                                    {{ session('error') }}
                                </div>
                            @endif

                        </form>
                        
                        <!-- Modal -->
                        <div class="modal fade" id="beriNilaiModal" tabindex="-1" role="dialog" aria-labelledby="beriNilaiModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="beriNilaiModalLabel">Input Nilai</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{ route('nilai-matkul-input') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                  <div class="form-group">
                                    <label for="nilai">Masukkan Nilai (0-100)</label>
                                    <input type="number" name="nilai" class="form-control" min="0" max="100" placeholder="Masukkan nilai antara 0-100" required>
                                    <input type="hidden" name="matkul_id" value="{{ $matkul->id }}">
                                    <input type="hidden" name="assessor_id" value="{{ $assessor_id }}">
                                    <input type="hidden" name="calon_mahasiswa_id" value="{{ $calon_mahasiswa_id }}">
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                  <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                </div>
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
</main>

@endsection
