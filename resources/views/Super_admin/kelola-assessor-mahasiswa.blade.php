@extends('layout.super_admin')
@section('content')

  <main id="main" class="main">
    <div class="pagetitle">
    <h1>Penugasan Assessor</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Atur Assessor</li>
        </ol>
    </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Atur Penugasan Assessor</h5>
            <!-- Filter jurusan khusus super admin -->
              <form class="row g-3" action="{{ route('super.kelola-assessor-mahasiswa') }}" method="GET">
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
              @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
              @endif
              @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
              @endif
              <div class="container mt-5">
              <table class="table table-bordered">
                  <thead>
                      <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Jurusan</th>
                          <th>Periode</th>
                          <th>Assessor 1</th>
                          <th>Assessor 2</th>
                          <th>Assessor 3</th>
                          <th>Deadline</th>
                          <th>Status RPL</th>
                          <th>Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                  @foreach($calon_mahasiswa as $index => $camaba)
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $camaba->nama ?? '-'  }}</td>
                    <td>{{ $camaba->jurusan->nama_jurusan ?? '-'  }}</td>
                    <td>{{ $camaba->periode->tahun_ajaran ?? '-'  }}</td>
                    <td>
                      {{ $camaba->assessment->assessor1->nama ?? 'belum' }}
                      @if($camaba->assessment && $camaba->assessment->assessor_1_submitted_at)
                                <span class="badge bg-primary">Selesai</span>
                            @endif
                        </td>
                        <td>
                      {{ $camaba->assessment->assessor2->nama ?? 'belum' }}
                      @if($camaba->assessment && $camaba->assessment->assessor_2_submitted_at)
                                <span class="badge bg-primary">Selesai</span>
                            @endif
                        </td>
                        <td>
                      {{ $camaba->assessment->assessor3->nama ?? 'belum' }}
                      @if($camaba->assessment && $camaba->assessment->assessor_3_submitted_at)
                                <span class="badge bg-primary">Selesai</span>
                            @endif
                        </td>
                        <td>
                      @if($camaba->assessment && $camaba->assessment->deadline)
                        {{ \Carbon\Carbon::parse($camaba->assessment->deadline)->format('d/m/Y H:i') }}
                                @php
                          $deadline = $camaba->assessment->deadline;
                                    $now = now();
                                @endphp
                        @if($camaba->assessment->rpl_status == 'selesai')
                                    <div><span class="badge bg-success mt-1">Selesai</span></div>
                                @else
                                    @if($deadline < $now)
                                        <span class="badge bg-danger">Terlambat</span>
                                    @elseif($deadline <= $now->addDays(3))
                                        <span class="badge bg-warning">Mendekati Deadline</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                @endif
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                        </td>
                        <td>
                      @if($camaba->assessment && $camaba->assessment->rpl_status)
                        <span class="badge bg-info">{{ ucfirst(str_replace('-', ' ', $camaba->assessment->rpl_status)) }}</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td>
                      <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          Aksi
                            </button>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item" href="#" onclick="editData({{ $camaba->id }})"><i class="bi bi-person-lines-fill me-2"></i>Atur Assessor</a>
                          </li>
                            @php
                                $can_publish = false;
                            if ($camaba->assessment) {
                              $assessment = $camaba->assessment;
                                    $assessor1_ok = !$assessment->assessor_id_1 || ($assessment->assessor_id_1 && $assessment->assessor_1_submitted_at);
                                    $assessor2_ok = !$assessment->assessor_id_2 || ($assessment->assessor_id_2 && $assessment->assessor_2_submitted_at);
                                    $assessor3_ok = !$assessment->assessor_id_3 || ($assessment->assessor_id_3 && $assessment->assessor_3_submitted_at);
                                    $has_assessors = $assessment->assessor_id_1 || $assessment->assessor_id_2 || $assessment->assessor_id_3;
                                    if ($has_assessors && $assessor1_ok && $assessor2_ok && $assessor3_ok) {
                                        $can_publish = true;
                                    }
                                }
                            @endphp
                            @if($can_publish)
                            @if($camaba->assessment->published_at)
                              <li><button class="dropdown-item" disabled><i class="bi bi-check2-circle me-2"></i>Telah Dipublish</button></li>
                                @else
                              <li><a class="dropdown-item" href="#" onclick="showRekapNilai({{ $camaba->id }})"><i class="bi bi-send-check me-2"></i>Publish Hasil</a></li>
                            @endif
                          @endif
                          @if($camaba->assessment)
                            <li><a class="dropdown-item" href="#" onclick="showUbahStatusRpl({{ $camaba->id }}, '{{ $camaba->assessment->rpl_status ?? '' }}')"><i class="bi bi-diagram-3 me-2"></i>Ubah Status RPL</a></li>
                          @endif
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="{{ route('export-word02', $camaba->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F02</a></li>
                          <li><a class="dropdown-item" href="{{ route('export-word08', $camaba->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F08</a></li>
                        </ul>
                      </div>
                        </td>
                        </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Modal Edit Penugasan Assessor -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Assessor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                      </div>
      <div class="modal-body">
        <form id="editForm" method="POST" action="{{ route('super.kelola-assessor-mahasiswa-add') }}">
                              @csrf
          <input type="hidden" name="calon_mahasiswa_id" id="calonMahasiswaId">
          <div class="form-group">
            <label for="assessor1_id">Assessor 1</label>
            <select class="form-select" id="assessor1_id" name="assessor1_id" required onchange="updateAssessorOptions()">
                                    <option value="">Pilih Assessor 1</option>
              @foreach($assessor as $assessor1)
                <option value="{{ $assessor1->id }}">{{ $assessor1->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
          <div class="form-group">
            <label for="assessor2_id">Assessor 2</label>
            <select class="form-select" id="assessor2_id" name="assessor2_id" onchange="updateAssessorOptions()">
                                    <option value="">Pilih Assessor 2</option>
              @foreach($assessor as $assessor2)
                <option value="{{ $assessor2->id }}">{{ $assessor2->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
          <div class="form-group">
            <label for="assessor3_id">Assessor 3</label>
            <select class="form-select" id="assessor3_id" name="assessor3_id">
                                    <option value="">Pilih Assessor 3</option>
              @foreach($assessor as $assessor3)
                <option value="{{ $assessor3->id }}">{{ $assessor3->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
          <div class="form-group">
            <label for="deadline">Deadline Penilaian</label>
            <input type="datetime-local" name="deadline" id="deadline" class="form-control">
                                  <small class="form-text text-muted">Pilih tanggal dan waktu deadline untuk penilaian assessor</small>
                                </div>
          <button type="submit" class="btn btn-primary mt-3">Save changes</button>
                          </form>
                      </div>
                  </div>
              </div>
</div>

<!-- Modal Rekap Nilai -->
<div class="modal fade" id="rekapNilaiModal" tabindex="-1" aria-labelledby="rekapNilaiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rekapNilaiModalLabel">Rekap Nilai Akhir Mahasiswa</h5>
      </div>
      <div class="modal-body">
        <div id="rekapNilaiContent">
          <!-- Data rekap nilai akan dimuat di sini -->
        </div>
      </div>
      <div class="modal-footer">
        <form id="publishForm" method="POST">
          @csrf
          <button type="submit" class="btn btn-primary">Konfirmasi & Publish</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
          </div>

<!-- Modal Ubah Status RPL (stepper modern) -->
<div class="modal fade" id="ubahStatusRplModal" tabindex="-1" aria-labelledby="ubahStatusRplModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="ubahStatusRplForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ubahStatusRplModalLabel">Ubah Status RPL</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="stepper-wrapper d-flex justify-content-between align-items-center mb-4" id="rpl-stepper">
            <div class="stepper-item" id="stepper-self-assessment">
              <div class="stepper-circle"><span class="stepper-icon" id="icon-self-assessment">1</span></div>
              <div class="stepper-label">Self-Assessment</div>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-item" id="stepper-penilaian-assessor">
              <div class="stepper-circle"><span class="stepper-icon" id="icon-penilaian-assessor">2</span></div>
              <div class="stepper-label">Penilaian Assessor</div>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-item" id="stepper-ditinjau-admin">
              <div class="stepper-circle"><span class="stepper-icon" id="icon-ditinjau-admin">3</span></div>
              <div class="stepper-label">Ditinjau Admin</div>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-item" id="stepper-selesai">
              <div class="stepper-circle"><span class="stepper-icon" id="icon-selesai">4</span></div>
              <div class="stepper-label">Selesai</div>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-item" id="stepper-banding">
              <div class="stepper-circle"><span class="stepper-icon" id="icon-banding">5</span></div>
              <div class="stepper-label">Banding</div>
            </div>
          </div>
          <div class="mt-4" id="stepper-actions">
            <!-- Tombol aksi akan diisi JS -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
.stepper-wrapper {
  width: 100%;
  position: relative;
}
.stepper-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  min-width: 80px;
  position: relative;
  z-index: 2;
}
.stepper-circle {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #dee2e6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 18px;
  margin-bottom: 6px;
  transition: background 0.3s, color 0.3s;
}
.stepper-item.active .stepper-circle {
  background: #0d6efd;
  color: #fff;
  box-shadow: 0 0 0 4px #cfe2ff;
}
.stepper-item.completed .stepper-circle {
  background: #198754;
  color: #fff;
}
.stepper-label {
  font-size: 13px;
  text-align: center;
  margin-top: 2px;
  color: #495057;
}
.stepper-item.completed .stepper-label {
  color: #198754;
  font-weight: 500;
}
.stepper-item.active .stepper-label {
  color: #0d6efd;
  font-weight: 500;
}
.stepper-line {
  flex: 0 0 16px;
  height: 3px;
  background: #dee2e6;
  margin-bottom: 24px;
  margin-left: -8px;
  margin-right: -8px;
  z-index: 1;
}
.stepper-item:last-child .stepper-line {
  display: none;
}
.stepper-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
</style>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function editData(mahasiswaId) {
    document.getElementById('calonMahasiswaId').value = mahasiswaId;
    const row = document.querySelector(`a[onclick="editData(${mahasiswaId})"]`).closest('tr');
    const assessor1Name = row.cells[4].textContent.trim();
    const assessor2Name = row.cells[5].textContent.trim();
    const assessor3Name = row.cells[6].textContent.trim();
    const allAssessors = [ @foreach($assessor as $a) { id: '{{ $a->id }}', nama: '{{ $a->nama }}' }, @endforeach ];
    const assessor1Id = allAssessors.find(a => a.nama === assessor1Name)?.id || '';
    const assessor2Id = allAssessors.find(a => a.nama === assessor2Name)?.id || '';
    const assessor3Id = allAssessors.find(a => a.nama === assessor3Name)?.id || '';
    document.getElementById('assessor1_id').value = assessor1Id;
    document.getElementById('assessor2_id').value = assessor2Id;
    document.getElementById('assessor3_id').value = assessor3Id;
    const deadlineCell = row.cells[7];
    const deadlineText = deadlineCell.textContent.trim();
    if (deadlineText && deadlineText !== 'Belum diatur') {
        const dateMatch = deadlineText.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
        if (dateMatch) {
            const [, day, month, year, hour, minute] = dateMatch;
            const deadlineValue = `${year}-${month}-${day}T${hour}:${minute}`;
            document.getElementById('deadline').value = deadlineValue;
        }
    } else {
        document.getElementById('deadline').value = '';
    }
    updateAssessorOptions();
    var myModal = new bootstrap.Modal(document.getElementById('editModal'));
    myModal.show();
}
function updateAssessorOptions() {
    const assessor1 = document.getElementById('assessor1_id');
    const assessor2 = document.getElementById('assessor2_id');
    const assessor3 = document.getElementById('assessor3_id');
    const allAssessors = [
        @foreach($assessor as $a)
            { id: '{{ $a->id }}', nama: '{{ $a->nama }}' },
        @endforeach
    ];
    const selectedValues = [
        assessor1.value,
        assessor2.value,
        assessor3.value
    ].filter(value => value !== '');
    [assessor1, assessor2, assessor3].forEach((select) => {
        const currentValue = select.value;
        select.innerHTML = '<option value="">Pilih Assessor ' + (select.id.slice(-1)) + '</option>';
        allAssessors.forEach(assessor => {
            if (!selectedValues.includes(assessor.id) || assessor.id === currentValue) {
                const option = new Option(assessor.nama, assessor.id);
                select.add(option);
            }
        });
        select.value = currentValue;
    });
}
document.addEventListener('DOMContentLoaded', updateAssessorOptions);
document.getElementById('assessor1_id').addEventListener('change', updateAssessorOptions);
document.getElementById('assessor2_id').addEventListener('change', updateAssessorOptions);
document.getElementById('assessor3_id').addEventListener('change', updateAssessorOptions);
function showRekapNilai(mahasiswaId) {
    document.getElementById('publishForm').action = '/super/publish-results/' + mahasiswaId;
    document.getElementById('rekapNilaiContent').innerHTML = 'Memuat...';
    fetch('/super/rekap-nilai/' + mahasiswaId)
        .then(response => response.json())
        .then(data => {
            let html = '<table class="table table-bordered"><thead><tr><th>No</th><th>Mata Kuliah</th><th>Status</th><th>Nilai Akhir</th></tr></thead><tbody>';
            data.final_results.forEach((item, idx) => {
                html += `<tr>
                    <td>${idx+1}</td>
                    <td>${item.matkul}</td>
                    <td><span class="badge ${item.status === 'Lolos' ? 'bg-success' : 'bg-danger'}">${item.status}</span></td>
                    <td>${item.nilai}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('rekapNilaiContent').innerHTML = html;
        });
    var modal = new bootstrap.Modal(document.getElementById('rekapNilaiModal'));
    modal.show();
}
function showUbahStatusRpl(mahasiswaId, currentStatus) {
    document.getElementById('ubahStatusRplForm').action = '/super/ubah-status-rpl/' + mahasiswaId;
    let steps = ['self-assessment', 'penilaian assessor', 'ditinjau admin', 'selesai', 'banding'];
    let stepLabels = {
        'self-assessment': 'Self-Assessment',
        'penilaian assessor': 'Penilaian Assessor',
        'ditinjau admin': 'Ditinjau Admin',
        'selesai': 'Selesai',
        'banding': 'Banding'
    };
    let currentIdx = steps.indexOf(currentStatus);
    if (currentIdx === -1) currentIdx = 0;
    steps.forEach((step, idx) => {
        let item = document.getElementById('stepper-' + step.replace(' ', '-'));
        let icon = document.getElementById('icon-' + step.replace(' ', '-'));
        if (item) {
            item.classList.remove('completed', 'active');
            if (idx < currentIdx) {
                item.classList.add('completed');
                if (icon) icon.innerHTML = '<i class="bi bi-check-lg"></i>';
            } else if (idx === currentIdx) {
                item.classList.add('active');
                if (icon) icon.innerHTML = (idx+1);
            } else {
                if (icon) icon.innerHTML = (idx+1);
            }
        }
    });
    let actions = '';
    if (currentIdx < steps.length - 1) {
        let nextStep = steps[currentIdx + 1];
        actions += `<button type="button" class="btn btn-primary" onclick="submitUbahStatusRpl('${nextStep}')">Lanjut ke ${stepLabels[nextStep]}</button>`;
    }
    if (currentIdx > 0) {
        let prevStep = steps[currentIdx - 1];
        actions += ` <button type="button" class="btn btn-warning" onclick="submitUbahStatusRpl('${prevStep}')">Kembali ke ${stepLabels[prevStep]}</button>`;
    }
    document.getElementById('stepper-actions').innerHTML = actions;
    var modal = new bootstrap.Modal(document.getElementById('ubahStatusRplModal'));
    modal.show();
}
function submitUbahStatusRpl(status) {
    let form = document.getElementById('ubahStatusRplForm');
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'rpl_status';
    input.value = status;
    form.appendChild(input);
    form.submit();
}
</script>
@endsection
