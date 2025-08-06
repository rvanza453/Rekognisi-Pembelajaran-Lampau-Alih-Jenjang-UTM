@extends('layout.assessor')
@section('content')

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Form Mahasiswa</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Forms</li>
                <li class="breadcrumb-item active">Layouts</li>
            </ol>
        </nav>
    </div>
    
    <section class="section">
        <div class="row">
            <div class="col-lg-13">
                <div class="card">
                    <div class="card-body"> 
                    <div class="container mt-5">

                        <br>
                        <h5 class="card-title text-center">{{ $matkul->name }}</h5>
                        <button type="button" class="btn btn-warning mb-3 float-end" data-toggle="modal" data-target="#exampleModalLongEdit">Edit</button>
 

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
                                        <td>
                                            @if($self_assessment->nilai == 'Sangat Baik')
                                                ✓
                                            @endif
                                        </td>
                                        <td>
                                            @if($self_assessment->nilai == 'Baik')
                                                ✓
                                            @endif
                                        </td>
                                        <td>
                                            @if($self_assessment->nilai == 'Tidak Pernah')
                                                ✓
                                            @endif
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            @if($self_assessment->bukti && $self_assessment->bukti->file)
                                                <a href="{{ asset('public/' . $self_assessment->bukti->file) }}" target="_blank">Lihat File</a>
                                            @else
                                                <span>File tidak tersedia</span> 
                                            @endif
                                        </td>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalLongEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Setujui Form!</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label for="matkul" class="form-label" style="color: black;">Notes</label>
                                            <div class="description">
                                                <ul>
                                                    <li><strong>Valid/Sahih:</strong> ada hubungan yang jelas antara persyaratan bukti dari unit kompetensi/mata kuliah yang akan dinilai dengan bukti yang menjadi dasar penilaian.</li>
                                                    <li><strong>Autentik/Asli:</strong> dapat dibuktikan bahwa buktinya adalah karya calon sendiri.</li>
                                                    <li><strong>Terkini:</strong> bukti menunjukkan pengetahuan dan keterampilan kandidat saat ini.</li>
                                                    <li><strong>Memadai/Cukup:</strong> kriteria mengacu kepada kriteria unjuk kerja dan panduan bukti: mendemonstrasikan kompetensi selama periode waktu tertentu; mengacu kepada semua dimensi kompetensi; dan mendemonstrasikan kompetensi dalam konteks yang berbeda.</li>
                                                </ul>
                                            </div>

                                            <!-- Tabel Edit -->
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
                                                        </tr>
                                                        <tr>
                                                            <th>Sangat baik</th>
                                                            <th>Baik</th>
                                                            <th>Tidak pernah</th>
                                                            <th>V</th>
                                                            <th>A</th>
                                                            <th>T</th>
                                                            <th>M</th>
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
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-warning">Save</button>
                                                </div>
                                                @if (session('success'))
                                                    <div class="alert alert-success mt-3">
                                                        {{ session('success') }}
                                                    </div>
                                                @endif

                                                @if (session('error'))
                                                    <div class="alert alert-danger 
                                            mt-3">
                                                        {{ session('error') }}
                                                    </div>
                                                @endif
          
                                            </form>
                                            
                                        </div>
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
