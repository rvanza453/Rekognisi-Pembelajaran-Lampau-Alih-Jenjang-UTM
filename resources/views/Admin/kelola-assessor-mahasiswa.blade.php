Tentu, ini adalah kode lengkap yang telah diperbaiki, yang menggabungkan peningkatan visual modern dengan semua elemen fungsional (modal, JavaScript, dll.) dari kode asli yang Anda berikan.

Anda bisa menyalin dan menempelkan seluruh blok kode ini untuk menggantikan file Blade Anda yang ada.

```php
@extends('layout.admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
    <h1>Atur Assessor Mahasiswa</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Atur Assessor Mahasiswa</li>
        </ol>
        </nav>
    </div><section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
                        <h5 class="card-title text-center">Atur Assessor Mahasiswa</h5>

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

                        <div class="table-responsive">
              <table class="table table-bordered mb-4">
                  <thead>
                      <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Program Studi</th>
                          <th>Assessor 1</th>
                          <th>Assessor 2</th>
                          <th>Assessor 3</th>
                          <th>Deadline</th>
                          <th>Status RPL</th>
                                        <th class="text-center">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach($calon_mahasiswa as $index => $camaba)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $camaba->nama ?? '-' }}</td>
                                        <td>{{ $camaba->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>
                                {{ $camaba->assessment->assessor1->nama ?? 'belum' }}
                                @if($camaba->assessment && $camaba->assessment->assessor_1_submitted_at)
                                                <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai dinilai"></i>
                                @endif
                            </td>
                            <td>
                                {{ $camaba->assessment->assessor2->nama ?? 'belum' }}
                                @if($camaba->assessment && $camaba->assessment->assessor_2_submitted_at)
                                                <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai dinilai"></i>
                                @endif
                            </td>
                            <td>
                                {{ $camaba->assessment->assessor3->nama ?? 'belum' }}
                                @if($camaba->assessment && $camaba->assessment->assessor_3_submitted_at)
                                                <i class="bi bi-check-circle-fill text-success ms-1" title="Selesai dinilai"></i>
                                @endif
                            </td>
                            <td>
                            @if($camaba->assessment && $camaba->assessment->deadline)
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-clock me-2"></i>
                                                    <div>
                                {{ \Carbon\Carbon::parse($camaba->assessment->deadline)->format('d/m/Y H:i') }}
                                @php
                                                            $deadline = \Carbon\Carbon::parse($camaba->assessment->deadline);
                                    $now = now();
                                                            $deadlineStatusClass = 'success';
                                                            $deadlineStatusText = 'Aktif';

                                                            if ($camaba->assessment->rpl_status == 'selesai') {
                                                                $deadlineStatusClass = 'success';
                                                                $deadlineStatusText = 'Selesai';
                                                            } elseif ($deadline < $now) {
                                                                $deadlineStatusClass = 'danger';
                                                                $deadlineStatusText = 'Terlambat';
                                                            } elseif ($deadline->isBetween($now, $now->addDays(3))) {
                                                                $deadlineStatusClass = 'warning';
                                                                $deadlineStatusText = 'Mendekati Deadline';
                                                            }
                                @endphp
                                                        <div class="status-badge mt-1">
                                                            <span class="status-dot {{ $deadlineStatusClass }}"></span> {{ $deadlineStatusText }}
                                                        </div>
                                                    </div>
                                                </div>
                            @else
                                <span class="text-muted">Belum diatur</span>
                            @endif
                            </td>
                            <td>
                                            @php
                                                $rplStatus = $camaba->assessment->rpl_status ?? null;
                                                $rplClass = 'secondary';
                                                $rplText = '-';
                                                if ($rplStatus) {
                                                    $rplText = ucfirst(str_replace('-', ' ', $rplStatus));
                                                    switch ($rplStatus) {
                                                        case 'selesai': $rplClass = 'success'; break;
                                                        case 'penilaian assessor': $rplClass = 'primary'; break;
                                                        case 'ditinjau admin': $rplClass = 'warning'; break;
                                                        case 'self assessment': $rplClass = 'info'; break;
                                                        case 'banding': $rplClass = 'danger'; break;
                                                    }
                                                }
                                            @endphp
                                            <div class="status-badge">
                                                <span class="status-dot {{ $rplClass }}"></span> {{ $rplText }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                    Aksi
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editData({{ $camaba->id }}, '{{ $camaba->assessment->assessor_id_1 ?? '' }}', '{{ $camaba->assessment->assessor_id_2 ?? '' }}', '{{ $camaba->assessment->assessor_id_3 ?? '' }}', '{{ $camaba->assessment && $camaba->assessment->deadline ? $camaba->assessment->deadline->format('Y-m-d\TH:i') : '' }}')"><i class="bi bi-pencil-square me-2"></i>Edit Assessor</a></li>
                                                    @if($camaba->assessment)
                                                    <li><a class="dropdown-item" href="#" onclick="showUbahStatusRpl({{ $camaba->id }}, '{{ $camaba->assessment->rpl_status ?? '' }}')"><i class="bi bi-diagram-3 me-2"></i>Ubah Status RPL</a></li>
                                @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="{{ route('export-word02', $camaba->id) }}"><i class="bi bi-file-earmark-word me-2"></i> Export Word F02</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('export-word08', $camaba->id) }}"><i class="bi bi-file-earmark-word me-2"></i> Export Word F08</a></li>
                                                </ul>
                                            </div>
                                @php
                                    $can_publish = false;
                                    if ($camaba->assessment) {
                                        $assessment = $camaba->assessment;
                                                    $assessor1_ok = !$assessment->assessor_id_1 || $assessment->assessor_1_submitted_at;
                                                    $assessor2_ok = !$assessment->assessor_id_2 || $assessment->assessor_2_submitted_at;
                                                    $assessor3_ok = !$assessment->assessor_id_3 || $assessment->assessor_3_submitted_at;
                                        $has_assessors = $assessment->assessor_id_1 || $assessment->assessor_id_2 || $assessment->assessor_id_3;
                                        if ($has_assessors && $assessor1_ok && $assessor2_ok && $assessor3_ok) {
                                            $can_publish = true;
                                        }
                                    }
                                @endphp
                            
                                @if($can_publish)
                                    @if($camaba->assessment->published_at)
                                                    <button class="btn btn-success btn-sm mt-1" disabled><i class="bi bi-check-circle me-2"></i>Telah Dipublish</button>
                                    @else
                                                    <button type="button" class="btn btn-primary btn-sm mt-1" onclick="showRekapNilai({{ $camaba->id }})"><i class="bi bi-rocket-takeoff me-2"></i>Publish Hasil</button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach  
                  </tbody>
              </table>
          </div>

          <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="editModalLabel">Edit Assessor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <form id="editForm" method="POST" action="{{ route("kelola-assessor-mahasiswa-add") }}">
                            @csrf
                            <input type="hidden" name="calon_mahasiswa_id" id="calonMahasiswaId">
                            
                                            <div class="form-group mb-3">
                                                <label for="assessor1_id" class="form-label">Assessor 1</label>
                                <select class="form-select" id="assessor1_id" name="assessor1_id" required onchange="updateAssessorOptions()">
                                    <option value="">Pilih Assessor 1</option>
                                    @foreach($assessor as $assessor1)
                                        <option value="{{ $assessor1->id }}">{{ $assessor1->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                                            <div class="form-group mb-3">
                                                <label for="assessor2_id" class="form-label">Assessor 2</label>
                                                <select class="form-select" id="assessor2_id" name="assessor2_id" onchange="updateAssessorOptions()">
                                    <option value="">Pilih Assessor 2</option>
                                    @foreach($assessor as $assessor2)
                                        <option value="{{ $assessor2->id }}">{{ $assessor2->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                                            <div class="form-group mb-3">
                                                <label for="assessor3_id" class="form-label">Assessor 3</label>
                                                <select class="form-select" id="assessor3_id" name="assessor3_id" onchange="updateAssessorOptions()">
                                    <option value="">Pilih Assessor 3</option>
                                    @foreach($assessor as $assessor3)
                                        <option value="{{ $assessor3->id }}">{{ $assessor3->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                                <label for="deadline" class="form-label">Deadline Penilaian</label>
                                <input type="datetime-local" name="deadline" id="deadline" class="form-control">
                                <small class="form-text text-muted">Pilih tanggal dan waktu deadline untuk penilaian assessor</small>
                            </div>
                            
                                            <div class="modal-footer px-0 pb-0 mt-3">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>

                        <div class="modal fade" id="rekapNilaiModal" tabindex="-1" aria-labelledby="rekapNilaiModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rekapNilaiModalLabel">Rekap Nilai Akhir Mahasiswa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="rekapNilaiContent">
                                            Memuat data rekapitulasi nilai...
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form id="publishForm" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Konfirmasi & Publish</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="ubahStatusRplModal" tabindex="-1" aria-labelledby="ubahStatusRplModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" id="ubahStatusRplForm">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ubahStatusRplModalLabel">Ubah Status RPL</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="stepper-wrapper d-flex justify-content-between align-items-center mb-4" id="rpl-stepper">
                                                <div class="stepper-item" id="stepper-self-assessment"><div class="stepper-circle"><span class="stepper-icon" id="icon-self-assessment">1</span></div><div class="stepper-label">Self-Assessment</div></div>
                                                <div class="stepper-line"></div>
                                                <div class="stepper-item" id="stepper-penilaian-assessor"><div class="stepper-circle"><span class="stepper-icon" id="icon-penilaian-assessor">2</span></div><div class="stepper-label">Penilaian Assessor</div></div>
                                                <div class="stepper-line"></div>
                                                <div class="stepper-item" id="stepper-ditinjau-admin"><div class="stepper-circle"><span class="stepper-icon" id="icon-ditinjau-admin">3</span></div><div class="stepper-label">Ditinjau Admin</div></div>
                                                <div class="stepper-line"></div>
                                                <div class="stepper-item" id="stepper-selesai"><div class="stepper-circle"><span class="stepper-icon" id="icon-selesai">4</span></div><div class="stepper-label">Selesai</div></div>
                                                <div class="stepper-line"></div>
                                                <div class="stepper-item" id="stepper-banding"><div class="stepper-circle"><span class="stepper-icon" id="icon-banding">5</span></div><div class="stepper-label">Banding</div></div>
                                            </div>
                                            <div class="mt-4 text-center" id="stepper-actions">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main><script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Note: Bootstrap's JS is likely already included in your main layout (layout.admin), so you might not need the script tags below if they cause double loading. --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> --}}

<script>
    // All original JavaScript functions are retained for functionality.
    function editData(mahasiswaId, id1, id2, id3, deadline) {
        document.getElementById('calonMahasiswaId').value = mahasiswaId;
        document.getElementById('assessor1_id').value = id1 || '';
        document.getElementById('assessor2_id').value = id2 || '';
        document.getElementById('assessor3_id').value = id3 || '';
        document.getElementById('deadline').value = deadline || '';
                // Update options based on the pre-filled values
                updateAssessorOptions();
                // Tampilkan modal
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
            }

            function updateAssessorOptions() {
        const selects = {
            assessor1: document.getElementById('assessor1_id'),
            assessor2: document.getElementById('assessor2_id'),
            assessor3: document.getElementById('assessor3_id')
        };
        
        const allAssessors = [ @foreach($assessor as $a) { id: '{{ $a->id }}', nama: '{{ $a->nama }}' }, @endforeach ];
        
        const selectedValues = Object.values(selects).map(s => s.value).filter(v => v !== '');
        
        Object.values(selects).forEach(select => {
            const currentValue = select.value;
            // Clear existing options except the placeholder
            Array.from(select.options).forEach(option => {
                if(option.value !== '') option.remove();
            });

                    allAssessors.forEach(assessor => {
                // Add option if it's not selected in another dropdown, OR if it's the current value of this dropdown
                        if (!selectedValues.includes(assessor.id) || assessor.id === currentValue) {
                            const option = new Option(assessor.nama, assessor.id);
                            select.add(option);
                        }
                    });
            select.value = currentValue; // Restore the selection
        });
    }

    function showRekapNilai(mahasiswaId) {
        document.getElementById('publishForm').action = '/admin/publish-results/' + mahasiswaId;
        document.getElementById('rekapNilaiContent').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        fetch('/admin/rekap-nilai/' + mahasiswaId)
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
            })
            .catch(error => {
                document.getElementById('rekapNilaiContent').innerHTML = '<p class="text-danger">Gagal memuat data. Silakan coba lagi.</p>';
            });
            
        var rekapModal = new bootstrap.Modal(document.getElementById('rekapNilaiModal'));
        rekapModal.show();
    }

    function showUbahStatusRpl(mahasiswaId, currentStatus) {
        document.getElementById('ubahStatusRplForm').action = '/admin/ubah-status-rpl/' + mahasiswaId;
        let steps = ['self-assessment', 'penilaian-assessor', 'ditinjau-admin', 'selesai', 'banding'];
        let stepLabels = {
            'self-assessment': 'Self-Assessment',
            'penilaian-assessor': 'Penilaian Assessor',
            'ditinjau-admin': 'Ditinjau Admin',
            'selesai': 'Selesai',
            'banding': 'Banding'
        };

        // Normalize currentStatus from view (e.g., 'penilaian assessor' to 'penilaian-assessor')
        let normalizedStatus = currentStatus.replace(' ', '-');
        let currentIdx = steps.indexOf(normalizedStatus);
        if (currentIdx === -1) currentIdx = 0;

        steps.forEach((step, idx) => {
            let item = document.getElementById('stepper-' + step);
            let icon = document.getElementById('icon-' + step);
            if (item && icon) {
                item.classList.remove('completed', 'active');
                if (idx < currentIdx) {
                    item.classList.add('completed');
                    icon.innerHTML = '<i class="bi bi-check-lg"></i>';
                } else if (idx === currentIdx) {
                    item.classList.add('active');
                    icon.innerText = (idx+1);
                } else {
                    icon.innerText = (idx+1);
                }
            }
        });

        let actions = '';
        if (currentIdx > 0) {
            let prevStep = steps[currentIdx - 1].replace('-', ' ');
            actions += `<button type="button" class="btn btn-warning" onclick="submitUbahStatusRpl('${prevStep}')">Kembali ke ${stepLabels[steps[currentIdx-1]]}</button>`;
        }
        if (currentIdx < steps.length - 1) {
            let nextStep = steps[currentIdx + 1].replace('-', ' ');
            actions += `<button type="button" class="btn btn-primary ms-2" onclick="submitUbahStatusRpl('${nextStep}')">Lanjut ke ${stepLabels[steps[currentIdx+1]]}</button>`;
        }
        document.getElementById('stepper-actions').innerHTML = actions;
        
        var statusModal = new bootstrap.Modal(document.getElementById('ubahStatusRplModal'));
        statusModal.show();
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

    // Initial call to setup dropdowns on page load
            document.addEventListener('DOMContentLoaded', updateAssessorOptions);
          </script>
          
@endsection

{{-- 🎨 New and Improved Styles --}}
<style>
/* Modern Table Style */
.table {
    border-collapse: collapse;
}
.table thead th {
    border-bottom: 2px solid #e9ecef;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}
.table td, .table th {
    vertical-align: middle;
    padding: 1rem 0.75rem;
    border: none;
    border-bottom: 1px solid #e9ecef;
}
.table tbody tr:hover {
    background-color: #f8f9fa;
}
.table-responsive {
    overflow-x: auto;
}

/* Elegant Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25em 0.6em;
    font-size: 0.85em;
    font-weight: 500;
    line-height: 1;
    color: #495057;
    background-color: #f1f3f5;
    border-radius: 1rem;
}
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}
.status-dot.primary { background-color: var(--bs-primary); }
.status-dot.success { background-color: var(--bs-success); }
.status-dot.danger  { background-color: var(--bs-danger); }
.status-dot.warning { background-color: var(--bs-warning); }
.status-dot.info    { background-color: var(--bs-info); }
.status-dot.secondary { background-color: var(--bs-secondary); }

/* Action Buttons */
.btn-group .btn {
    transition: all 0.2s ease-in-out;
}
.dropdown-item i {
    color: #6c757d;
}

/* Stepper from your original code (It's already great!) */
.stepper-wrapper { width: 100%; position: relative; }
.stepper-item { display: flex; flex-direction: column; align-items: center; flex: 1; min-width: 80px; position: relative; z-index: 2; }
.stepper-circle { width: 36px; height: 36px; border-radius: 50%; background: #dee2e6; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; margin-bottom: 6px; transition: background 0.3s, color 0.3s; }
.stepper-item.active .stepper-circle { background: #0d6efd; color: #fff; box-shadow: 0 0 0 4px #cfe2ff; }
.stepper-item.completed .stepper-circle { background: #198754; color: #fff; }
.stepper-label { font-size: 13px; text-align: center; margin-top: 2px; color: #495057; }
.stepper-item.completed .stepper-label { color: #198754; font-weight: 500; }
.stepper-item.active .stepper-label { color: #0d6efd; font-weight: 500; }
.stepper-line { flex: 1; height: 3px; background: #dee2e6; margin-top: -20px; position: relative; top: 18px; z-index: 1; }
.stepper-item:first-child .stepper-line:first-of-type, .stepper-item:last-child .stepper-line:last-of-type { display: none; }
.stepper-icon { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
</style>
