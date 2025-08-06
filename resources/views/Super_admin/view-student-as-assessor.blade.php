@extends('layout.super_admin')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Detail Mahasiswa - Melihat sebagai Assessor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/super/dashboard">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('super.data-assessor-table') }}">Data Assessor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('super.view-assessor-students', $assessor->id) }}">Mahasiswa Assessor</a></li>
        <li class="breadcrumb-item active">Detail Mahasiswa</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Detail Mahasiswa: {{ $camaba->nama }}</h5>
            <p class="text-muted">Melihat sebagai Assessor: {{ $assessor->nama }}</p>

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab" aria-controls="profil" aria-selected="true">Profil</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="ijazah-tab" data-bs-toggle="tab" data-bs-target="#ijazah" type="button" role="tab" aria-controls="ijazah" aria-selected="false">Ijazah</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="transkrip-tab" data-bs-toggle="tab" data-bs-target="#transkrip" type="button" role="tab" aria-controls="transkrip" aria-selected="false">Transkrip</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="bukti-tab" data-bs-toggle="tab" data-bs-target="#bukti" type="button" role="tab" aria-controls="bukti" aria-selected="false">Bukti Alih Jenjang</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="self-assessment-tab" data-bs-toggle="tab" data-bs-target="#self-assessment" type="button" role="tab" aria-controls="self-assessment" aria-selected="false">Self Assessment</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="konversi-tab" data-bs-toggle="tab" data-bs-target="#konversi" type="button" role="tab" aria-controls="konversi" aria-selected="false">Konversi Nilai</button>
              </li>
            </ul>

            <div class="tab-content pt-2" id="borderedTabContent">

              <!-- PROFIL -->
              <div class="tab-pane fade show active" id="profil" role="tabpanel" aria-labelledby="profil-tab">
                <h2>Data Diri Mahasiswa</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Nama</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->nama ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Prodi</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->jurusan->nama_jurusan ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Alamat</div>
                      <div class="col-lg-9 col-md-8">{{   $camaba->alamat ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Email</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->user->email ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">No Wa</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->nomor_telepon ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Tempat Lahir</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->tempat_lahir ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Tanggal Lahir</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Jenis Kelamin</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->kelamin ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Kota</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->kota ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Provinsi</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->provinsi ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Kode Pos</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->kode_pos ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Kebangsaan</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->kebangsaan ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Nomor Rumah</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->nomor_rumah ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Nomor Kantor</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->nomor_kantor ?? '-'  }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- IJAZAH -->
              <div class="tab-pane fade" id="ijazah" role="tabpanel" aria-labelledby="ijazah-tab">
                <h2>Data Ijazah</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Institusi Pendidikan</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->institusi_pendidikan ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Jenjang</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->jenjang ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Provinsi</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->provinsi ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Kota</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->kota ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Negara</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->negara ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Fakultas</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->fakultas ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Jurusan</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->jurusan ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Nilai/IPK</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->ipk_nilai ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">Tahun Lulus</div>
                      <div class="col-lg-9 col-md-8">{{  $camaba->ijazah->tahun_lulus ?? '-'  }}</div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 label">File Ijazah</div>
                      <div class="col-lg-9 col-md-8">
                        @if($camaba->ijazah && ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah))
                          <a href="{{ asset('Data/Ijazah/' . ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah)) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-download"></i> Download Ijazah
                          </a>
                        @else
                          <span class="text-muted">File tidak tersedia</span>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TRANSKRIP -->
              <div class="tab-pane fade" id="transkrip" role="tabpanel" aria-labelledby="transkrip-tab">
                <h2>Data Transkrip</h2>
                <div class="card">
                  <div class="card-body">
                    @if($camaba->transkrip && ($camaba->transkrip instanceof \Illuminate\Support\Collection ? $camaba->transkrip->count() > 0 : true))
                      <div class="mt-3">
                        <strong>File Transkrip:</strong>
                        @php
                          $fileTranskrip = $camaba->transkrip instanceof \Illuminate\Support\Collection ? ($camaba->transkrip->first()->file_transkrip ?? $camaba->transkrip->first()->file ?? null) : ($camaba->transkrip->file_transkrip ?? $camaba->transkrip->file ?? null);
                        @endphp
                        @if($fileTranskrip)
                          <a href="{{ asset('Data/Transkrip/' . $fileTranskrip) }}" target="_blank" class="btn btn-primary btn-sm ms-2">
                            <i class="bi bi-download"></i> Download Transkrip
                          </a>
                        @else
                          <span class="text-muted ms-2">File tidak tersedia</span>
                        @endif
                      </div>
                    @else
                      <div class="alert alert-info" role="alert">
                        Data transkrip tidak tersedia.
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <!-- BUKTI ALIH JENJANG -->
              <div class="tab-pane fade" id="bukti" role="tabpanel" aria-labelledby="bukti-tab">
                <h2>Bukti Alih Jenjang</h2>
                <div class="card">
                  <div class="card-body">
                    @if($camaba->bukti_alih_jenjang && ($camaba->bukti_alih_jenjang instanceof \Illuminate\Support\Collection ? $camaba->bukti_alih_jenjang->count() > 0 : true))
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Jenis Bukti</th>
                              <th>File</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if($camaba->bukti_alih_jenjang instanceof \Illuminate\Support\Collection)
                              @foreach($camaba->bukti_alih_jenjang as $index => $bukti)
                                <tr>
                                  <td>{{ (int)$index + 1 }}</td>
                                  <td>{{ $bukti->jenis_bukti ?? $bukti->jenis_dokumen ?? '-' }}</td>
                                  <td>
                                    @if($bukti->file_bukti ?? $bukti->file)
                                      <a href="{{ asset('Data/Bukti_alih_jenjang/' . ($bukti->file_bukti ?? $bukti->file)) }}" target="_blank" class="btn btn-primary btn-sm">
                                        <i class="bi bi-download"></i> Download
                                      </a>
                                    @else
                                      <span class="text-muted">File tidak tersedia</span>
                                    @endif
                                  </td>
                                </tr>
                              @endforeach
                            @else
                              <tr>
                                <td>1</td>
                                <td>{{ $camaba->bukti_alih_jenjang->jenis_bukti ?? $camaba->bukti_alih_jenjang->jenis_dokumen ?? '-' }}</td>
                                <td>{{ $camaba->bukti_alih_jenjang->deskripsi ?? '-' }}</td>
                                <td>
                                  @if($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file)
                                    <a href="{{ asset('Data/Bukti_alih_jenjang/' . ($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file)) }}" target="_blank" class="btn btn-primary btn-sm">
                                      <i class="bi bi-download"></i> Download
                                    </a>
                                  @else
                                    <span class="text-muted">File tidak tersedia</span>
                                  @endif
                                </td>
                              </tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                    @else
                      <div class="alert alert-info" role="alert">
                        Data bukti alih jenjang tidak tersedia.
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              <!-- SELF ASSESSMENT -->
              <div class="tab-pane fade" id="self-assessment" role="tabpanel" aria-labelledby="self-assessment-tab">
                  <h5 class="card-title">Detail Self Assessment & Penilaian Assessor per CPMK</h5>

                  @if ($cpmkAssessments->isEmpty())
                      <div class="alert alert-info" role="alert">
                          Mahasiswa belum melakukan self-assessment mata kuliah.
                      </div>
                  @else
                      @php
                          $assessorSlotName = '';
                          if ($camaba->assessment) {
                              if ($camaba->assessment->assessor_id_1 == $assessor->id) $assessorSlotName = 'nilai_assessor1';
                              elseif ($camaba->assessment->assessor_id_2 == $assessor->id) $assessorSlotName = 'nilai_assessor2';
                              elseif ($camaba->assessment->assessor_id_3 == $assessor->id) $assessorSlotName = 'nilai_assessor3';
                          }
                      @endphp

                      <div class="accordion" id="selfAssessmentAccordion">
                          @foreach($cpmkAssessments as $nama_matkul => $assessments)
                              <div class="accordion-item">
                                  <h2 class="accordion-header" id="heading-sa-{{ \Str::slug($nama_matkul) }}">
                                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-sa-{{ \Str::slug($nama_matkul) }}">
                                          {{ $nama_matkul }}
                                      </button>
                                  </h2>
                                  <div id="collapse-sa-{{ \Str::slug($nama_matkul) }}" class="accordion-collapse collapse" data-bs-parent="#selfAssessmentAccordion">
                                      <div class="accordion-body">
                                          <div class="table-responsive">
                                              <table class="table table-striped table-bordered">
                                                  <thead class="table-light">
                                                      <tr>
                                                          <th>CPMK</th>
                                                          <th>Matkul Dasar (dari Mahasiswa)</th>
                                                          <th>Nilai Dasar (dari Mahasiswa)</th>
                                                          <th>Nilai dari {{ $assessor->nama }}</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>
                                                      @foreach($assessments as $item)
                                                          <tr>
                                                              <td>{{ $item->cpmk->penjelasan ?? 'CPMK tidak ditemukan' }}</td>
                                                              <td>{{ $item->matkul_dasar ?? '-' }}</td>
                                                              <td>{{ $item->nilai_matkul_dasar ?? '-' }}</td>
                                                              <td>
                                                                  @if($assessorSlotName && $item->$assessorSlotName !== null)
                                                                      <span class="badge bg-success">{{ $item->$assessorSlotName }}</span>
                                                                  @else
                                                                      <span class="badge bg-secondary">Belum dinilai</span>
                                                                  @endif
                                                              </td>
                                                          </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          @endforeach
                      </div>
                  @endif
              </div>

              <!-- KONVERSI NILAI -->
              <div class="tab-pane fade" id="konversi" role="tabpanel" aria-labelledby="konversi-tab">
                  <h5 class="card-title">Hasil Akhir Konversi Nilai Mata Kuliah</h5>
                  <div class="card">
                      <div class="card-body table-responsive mt-4">
                          <div class="table-responsive">
                              <table class="table table-striped table-bordered">
                                  <thead>
                                      <tr>
                                          <th>No</th>
                                          <th>Mata Kuliah</th>
                                          <th>Status</th>
                                          <th>Nilai per Assessor</th>
                                          <th>Nilai Akhir</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @forelse($matkulWithStatus as $mk)
                                          <tr>
                                              <td>{{ $loop->iteration }}</td>
                                              <td>{{ $mk->nama_matkul }}</td>
                                              <td>
                                                  @if($mk->isComplete)
                                                      <span class="badge {{ $mk->isLolos ? 'bg-success' : 'bg-danger' }}">
                                                          {{ $mk->isLolos ? 'Lolos' : 'Gagal' }}
                                                      </span>
                                                  @else
                                                      <span class="badge bg-warning">
                                                          Menunggu Penilaian ({{ $mk->completedAssessmentsCount }}/{{ $mk->requiredAssessments }})
                                                      </span>
                                                  @endif
                                              </td>
                                              <td>
                                                  @if(isset($allAssessorScores[$mk->id]))
                                                      @foreach($allAssessorScores[$mk->id] as $score)
                                                          @if($score->assessor && $score->nilai !== null)
                                                              <small><strong>{{ $score->assessor->nama }}:</strong> {{ number_format($score->nilai, 2) }}</small><br>
                                                          @endif
                                                      @endforeach
                                                  @else
                                                      <span class="text-muted">-</span>
                                                  @endif
                                              </td>
                                              <td>
                                                  @if($mk->isComplete && $mk->finalScore !== null)
                                                      <strong class="{{ $mk->isLolos ? 'text-success' : 'text-danger' }}">
                                                          {{ number_format($mk->finalScore, 2) }}
                                                      </strong>
                                                  @else
                                                      -
                                                  @endif
                                              </td>
                                          </tr>
                                      @empty
                                          <tr>
                                              <td colspan="5" class="text-center">Tidak ada mata kuliah yang tersedia.</td>
                                          </tr>
                                      @endforelse
                                  </tbody>
                              </table>
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

{{-- Pastikan HTML Modal seperti ini --}}
<div class="modal fade" id="cpmkModal" tabindex="-1" aria-labelledby="cpmkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cpmkModalLabel">CPMK Mata Kuliah: <span id="matkulNameInModal"></span></h5>
      </div>
      <div class="modal-body" id="cpmkModalBody">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

{{-- Ganti keseluruhan blok @push('scripts') dengan ini --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Menambahkan logika untuk TAB yang sebelumnya tidak ada di file ini
    const tabButtons = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Hapus kelas 'active' dari semua tombol tab
            tabButtons.forEach(b => b.classList.remove('active'));
            // Tambahkan 'active' ke tombol yang diklik
            this.classList.add('active');
            
            // Logika untuk menampilkan konten tab yang sesuai
            document.querySelectorAll('.tab-content').forEach(tab => {
                if(tab.id === this.getAttribute('data-bs-target').substring(1)) {
                    tab.classList.add('show', 'active');
                } else {
                    tab.classList.remove('show', 'active');
                }
            });
        });
    });

    // Logika untuk MODAL, disamakan dengan file detail-banding yang sudah berfungsi
    var cpmkModal = new bootstrap.Modal(document.getElementById('cpmkModal'));
    var matkulNameInModal = document.getElementById('matkulNameInModal');
    var cpmkModalBody = document.getElementById('cpmkModalBody');

    document.querySelectorAll('.view-cpmk-btn').forEach(button => {
        button.addEventListener('click', function () {
            const matkulId = this.getAttribute('data-matkul-id');
            const matkulName = this.getAttribute('data-matkul-name');

            matkulNameInModal.textContent = matkulName;
            cpmkModalBody.innerHTML = '<div class="text-center">Loading CPMK...</div>';

            fetch(`/super/matkul/${matkulId}/cpmk`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    let html = '';
                    let cpmkList = [];

                    if (Array.isArray(data)) {
                        cpmkList = data;
                    } else if (data.success && Array.isArray(data.cpmks)) {
                        cpmkList = data.cpmks;
                    }

                    if (cpmkList.length > 0) {
                        html += '<div class="table-responsive"><table class="table table-striped">';
                        html += '<thead><tr><th>No</th><th>Kode CPMK</th><th>Penjelasan</th></tr></thead><tbody>';
                        cpmkList.forEach((cpmk, idx) => {
                            html += `<tr><td>${idx + 1}</td><td>${cpmk.kode_cpmk ?? '-'}</td><td>${cpmk.penjelasan ?? '-'}</td></tr>`;
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html = '<div class="alert alert-info">Tidak ada CPMK yang tersedia untuk mata kuliah ini.</div>';
                    }
                    cpmkModalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching CPMK:', error);
                    cpmkModalBody.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data CPMK.</div>';
                });

            cpmkModal.show();
        });
    });
});
</script>
@endpush