@extends('layout.admin')
@section('content')
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Detail Banding Mahasiswa</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Detail Mahasiswa: {{ $camaba->nama }}</h5>
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
            </ul>
            <div class="tab-content pt-2" id="borderedTabContent">
              <!-- PROFIL -->
              <div class="tab-pane fade show active" id="profil" role="tabpanel" aria-labelledby="profil-tab">
                <h2>Data Diri Mahasiswa</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nama</div><div class="col-lg-9 col-md-8">{{ $camaba->nama ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Prodi</div><div class="col-lg-9 col-md-8">{{ $camaba->jurusan->nama_jurusan ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Alamat</div><div class="col-lg-9 col-md-8">{{ $camaba->alamat ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Email</div><div class="col-lg-9 col-md-8">{{ $camaba->user->email ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">No Wa</div><div class="col-lg-9 col-md-8">{{ $camaba->nomor_telepon ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tempat Lahir</div><div class="col-lg-9 col-md-8">{{ $camaba->tempat_lahir ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tanggal Lahir</div><div class="col-lg-9 col-md-8">{{ $camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jenis Kelamin</div><div class="col-lg-9 col-md-8">{{ $camaba->kelamin ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kota</div><div class="col-lg-9 col-md-8">{{ $camaba->kota ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Provinsi</div><div class="col-lg-9 col-md-8">{{ $camaba->provinsi ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kode Pos</div><div class="col-lg-9 col-md-8">{{ $camaba->kode_pos ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kebangsaan</div><div class="col-lg-9 col-md-8">{{ $camaba->kebangsaan ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nomor Rumah</div><div class="col-lg-9 col-md-8">{{ $camaba->nomor_rumah ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nomor Kantor</div><div class="col-lg-9 col-md-8">{{ $camaba->nomor_kantor ?? '-' }}</div></div>
                  </div>
                </div>
              </div>
              <!-- IJAZAH -->
              <div class="tab-pane fade" id="ijazah" role="tabpanel" aria-labelledby="ijazah-tab">
                <h2>Data Ijazah</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Institusi Pendidikan</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->institusi_pendidikan ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jenjang</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->jenjang ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Provinsi</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->provinsi ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kota</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->kota ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Negara</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->negara ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Fakultas</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->fakultas ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jurusan</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->jurusan ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nilai/IPK</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->ipk_nilai ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tahun Lulus</div><div class="col-lg-9 col-md-8">{{ $camaba->ijazah->tahun_lulus ?? '-' }}</div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">File Ijazah</div><div class="col-lg-9 col-md-8">
                      @if($camaba->ijazah && ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah))
                        <a href="{{ asset('Data/Ijazah/' . ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah)) }}" target="_blank" class="btn btn-primary btn-sm">
                          <i class="bi bi-download"></i> Download Ijazah
                        </a>
                      @else
                        <span class="text-muted">File tidak tersedia</span>
                      @endif
                    </div></div>
                  </div>
                </div>
              </div>
              <!-- TRANSKRIP -->
              <div class="tab-pane fade" id="transkrip" role="tabpanel" aria-labelledby="transkrip-tab">
                <h2>Data Transkrip</h2>
                <div class="card">
                  <div class="card-body">
                    <!-- PDF Viewer -->
                    <div class="pdf-container mt-3 mb-3">
                        <embed 
                            src="{{ route('view-transkrip', $existing_transkrip->file) }}"
                            type="application/pdf"
                            width="100%"
                            height="600px"
                        />
                    </div>
                    @if($camaba->transkrip && ($camaba->transkrip instanceof \Illuminate\Support\Collection ? $camaba->transkrip->count() > 0 : true))
                      <!--<div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr><th>No</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th></tr>
                          </thead>
                          <tbody>
                            @if($camaba->transkrip instanceof \Illuminate\Support\Collection)
                              @foreach($camaba->transkrip as $index => $transkrip)
                                <tr><td>{{ (int)$index + 1 }}</td><td>{{ $transkrip->nama_matkul ?? '-' }}</td><td>{{ $transkrip->sks ?? '-' }}</td><td>{{ $transkrip->nilai ?? '-' }}</td></tr>
                              @endforeach
                            @else
                              <tr><td>1</td><td>{{ $camaba->transkrip->nama_matkul ?? '-' }}</td><td>{{ $camaba->transkrip->sks ?? '-' }}</td><td>{{ $camaba->transkrip->nilai ?? '-' }}</td></tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
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
                      </div>-->
                    @else
                      <div class="alert alert-info" role="alert">Data transkrip tidak tersedia.</div>
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
                          <thead><tr><th>No</th><th>Jenis Bukti</th><th>Deskripsi</th><th>File</th></tr></thead>
                          <tbody>
                            @if($camaba->bukti_alih_jenjang instanceof \Illuminate\Support\Collection)
                              @foreach($camaba->bukti_alih_jenjang as $index => $bukti)
                                <tr><td>{{ (int)$index + 1 }}</td><td>{{ $bukti->jenis_bukti ?? $bukti->jenis_dokumen ?? '-' }}</td><td>{{ $bukti->deskripsi ?? '-' }}</td><td>@if($bukti->file_bukti ?? $bukti->file)<a href="{{ asset('Data/Bukti_alih_jenjang/' . ($bukti->file_bukti ?? $bukti->file)) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Download</a>@else<span class="text-muted">File tidak tersedia</span>@endif</td></tr>
                              @endforeach
                            @else
                              <tr><td>1</td><td>{{ $camaba->bukti_alih_jenjang->jenis_bukti ?? $camaba->bukti_alih_jenjang->jenis_dokumen ?? '-' }}</td><td>{{ $camaba->bukti_alih_jenjang->deskripsi ?? '-' }}</td><td>@if($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file)<a href="{{ asset('Data/Bukti_alih_jenjang/' . ($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file)) }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Download</a>@else<span class="text-muted">File tidak tersedia</span>@endif</td></tr>
                            @endif
                          </tbody>
                        </table>
                      </div>
                    @else
                      <div class="alert alert-info" role="alert">Data bukti alih jenjang tidak tersedia.</div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <!-- Tabel Banding -->
            <div class="card mt-4">
              <div class="card-body">
                <h5 class="card-title">Mata Kuliah yang Diajukan Banding</h5>
                <form action="{{ route('admin.proses-banding', $camaba->id) }}" method="POST">
                  @csrf
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Mata Kuliah</th>
                          <th>Keterangan Banding</th>
                          <th>Nilai Akhir</th>
                          <th>Status Banding</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                          // Group by matkul_id agar hanya satu baris per matkul
                          $banding_grouped = $banding_matkul->groupBy('matkul_id')->map(function($items) { return $items->first(); });
                        @endphp
                        @forelse($banding_grouped as $matkul_id => $banding)
                        <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>
                            {{ $banding->matkul->nama_matkul ?? '-' }}
                            @if($banding->matkul)
                              <button type="button" class="btn btn-info btn-sm view-cpmk-btn ms-2"
                                data-matkul-id="{{ $banding->matkul->id }}"
                                data-matkul-name="{{ $banding->matkul->nama_matkul }}">
                                View CPMK
                              </button>
                            @endif
                          </td>
                          <td>{{ $banding->banding_keterangan }}</td>
                          <td>
                            <input type="number" name="nilai_akhir[{{ $matkul_id }}]" class="form-control" min="0" max="100" value="{{ $banding->nilai_akhir ?? '' }}" >
                          </td>
                          <td>
                            <select name="banding_status[{{ $matkul_id }}]" class="form-select" required>
                              <option value="pending" {{ $banding->banding_status=='pending' ? 'selected' : '' }}>Pending</option>
                              <option value="diterima" {{ $banding->banding_status=='diterima' ? 'selected' : '' }}>Diterima</option>
                              <option value="ditolak" {{ $banding->banding_status=='ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center">Tidak ada matkul yang diajukan banding.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  @if(count($banding_grouped))
                  <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Simpan Semua Banding</button>
                  </div>
                  @endif
                </form>
                @php
                  $ada_banding_selesai = $banding_grouped->whereIn('banding_status', ['diterima','ditolak'])->count() > 0;
                @endphp
                @if($ada_banding_selesai)
                  <form action="{{ route('admin.publish-results-banding', $camaba->id) }}" method="POST" class="mt-3" id="publishBandingFormAdmin">
                    @csrf
                    <button type="submit" class="btn btn-primary">Publikasikan Ulang Hasil Setelah Banding</button>
                  </form>
                  <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    var form = document.getElementById('publishBandingFormAdmin');
                    if(form) {
                      form.addEventListener('submit', function(e) {
                        if(!confirm('Apakah Anda yakin ingin mempublish hasil banding?')) {
                          e.preventDefault();
                        }
                      });
                    }
                  });
                  </script>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- Pastikan HTML Modal memiliki tombol close (X) di header --}}
<div class="modal fade" id="cpmkModal" tabindex="-1" aria-labelledby="cpmkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cpmkModalLabel">CPMK Mata Kuliah: <span id="matkulNameInModal"></span></h5>
        {{-- Tombol ini penting dan harus ada --}}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="cpmkModalBody">
        {{-- Konten dinamis akan dimuat di sini oleh JavaScript --}}
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
    // Logika untuk TAB
    const tabButtons = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Sembunyikan semua tab-content lalu tampilkan yang target saja
            document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('show', 'active'));
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if(target) {
                target.classList.add('show', 'active');
            }
        });
    });

    // Logika untuk MODAL
    var cpmkModalEl = document.getElementById('cpmkModal');
    var cpmkModal = new bootstrap.Modal(cpmkModalEl);
    
    var matkulNameInModal = document.getElementById('matkulNameInModal');
    var cpmkModalBody = document.getElementById('cpmkModalBody');

    // Event listener untuk MEMBUKA modal
    document.querySelectorAll('.view-cpmk-btn').forEach(button => {
        button.addEventListener('click', function () {
            const matkulId = this.getAttribute('data-matkul-id');
            const matkulName = this.getAttribute('data-matkul-name');

            matkulNameInModal.textContent = matkulName;
            cpmkModalBody.innerHTML = '<div class="text-center">Loading CPMK...</div>';

            // Ganti URL fetch ke endpoint yang sesuai untuk admin, jika berbeda.
            // Jika sama, biarkan /super/
            fetch(`/admin/matkul/${matkulId}/cpmk`) // Asumsi URL untuk admin adalah /admin/...
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    let html = '';
                    let cpmkList = [];

                    if (Array.isArray(data)) { cpmkList = data; } 
                    else if (data.success && Array.isArray(data.cpmks)) { cpmkList = data.cpmks; }

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

    // Event listener manual untuk MENUTUP modal
    const closeButtons = cpmkModalEl.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            cpmkModal.hide();
        });
    });
});
</script>
@endpush