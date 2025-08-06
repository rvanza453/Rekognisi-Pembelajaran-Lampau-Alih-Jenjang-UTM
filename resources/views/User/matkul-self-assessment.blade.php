@extends('layout.user')
@section('content')

@php
    // Variabel untuk mengunci form jika sudah pernah submit
    $isLocked = $assessment && $assessment->self_assessment_submitted_at;
@endphp

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Self Assessment Mata Kuliah</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/user/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Self Assessment</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @endif

                {{-- <<< PENAMBAHAN PESAN JIKA FORM TERKUNCI >>> --}}
                @if($isLocked)
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="bi bi-lock-fill me-2"></i>
                        <div>
                            Anda telah mengirimkan hasil self-assessment pada tanggal <strong>{{ $assessment->self_assessment_submitted_at->format('d F Y, H:i') }}</strong>. Data tidak dapat diubah lagi.
                        </div>
                    </div>
                @endif
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Mata Kuliah</h5>
                        <!-- PDF Viewer -->
                        <div class="pdf-container mt-3 mb-3">
                            <embed 
                                src="{{ route('view-transkrip', $existing_transkrip->file) }}"
                                type="application/pdf"
                                width="100%"
                                height="600px"
                            />
                        </div>
                        <p>Klik pada salah satu mata kuliah untuk membuka form penilaian.</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mata Kuliah</th>
                                        <th>Status Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody id="matkul-table-body">
                                    @foreach($matkuls as $matkul)
                                        @php
                                            $currentAssessment = $matkulAssessments->get($matkul->id);
                                            $choice = $currentAssessment->self_assessment_value ?? '';
                                        @endphp
                                        <tr class="matkul-header-row" data-matkul-id="{{ $matkul->id }}" style="{{ $isLocked ? 'cursor: not-allowed;' : 'cursor: pointer;' }}">
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $matkul->nama_matkul }}</td>
                                            <td>
                                                @if($choice == 'Mengajukan')
                                                    <span class="badge bg-success">Mengajukan</span>
                                                @elseif($choice == 'Tidak Mengajukan')
                                                    <span class="badge bg-danger">Tidak Mengajukan</span>
                                                @else
                                                    <span class="badge bg-secondary">Belum Dipilih</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr class="assessment-content-row" id="content-{{$matkul->id}}" style="display: none;">
                                            <td colspan="3" class="p-3">
                                                {{-- Form akan di-generate oleh JavaScript di sini --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center p-4">
                        <h5 class="card-title">Kirim Hasil Self-Assessment Final</h5>
                        @if ($isLocked)
                            <p class="text-muted">Status RPL saat ini adalah <strong>{{ ucwords(str_replace('_', ' ', $assessment->rpl_status)) }}</strong>.</p>
                            <button class="btn btn-success" disabled><i class="bi bi-check-circle"></i> Sudah Dikirim</button>
                        @else
                            <p>Pastikan semua penilaian telah terisi dengan benar. Setelah dikirim, data tidak dapat diubah kembali.</p>
                            <form action="{{ route('submit-matkul-self-assessment') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim semua hasil self-assessment? Tindakan ini tidak dapat diurungkan.');">
                                @csrf
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send-check"></i> Kirim Hasil Self-Assessment</button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isLocked = {{ $isLocked ? 'true' : 'false' }};
    const matkulsData = @json($matkulsForJs);
    const allCpmkAssessmentsData = @json($allExistingCpmkAssessments);
    const matkulAssessmentsData = @json($matkulAssessments);
    const tableBody = document.getElementById('matkul-table-body');

    tableBody.addEventListener('click', function(event) {
        if (isLocked) return; // Jika terkunci, jangan lakukan apa-apa

        const headerRow = event.target.closest('.matkul-header-row');
        if (!headerRow) return;

        const matkulId = headerRow.dataset.matkulId;
        const contentRow = document.getElementById(`content-${matkulId}`);
        const isAlreadyOpen = contentRow.style.display === 'table-row';

        document.querySelectorAll('.assessment-content-row').forEach(row => {
            if(row.id !== `content-${matkulId}`) {
                row.style.display = 'none';
                row.querySelector('td').innerHTML = '';
            }
        });
        
        document.querySelectorAll('.matkul-header-row').forEach(row => {
            row.classList.remove('table-primary');
        });

        if (!isAlreadyOpen) {
            headerRow.classList.add('table-primary');
            contentRow.style.display = 'table-row';
            generateFormContent(contentRow.querySelector('td'), matkulId);
        } else {
            contentRow.style.display = 'none';
            contentRow.querySelector('td').innerHTML = '';
        }
    });

    function generateFormContent(cell, matkulId) {
        const matkul = matkulsData[matkulId];
        const cpmkAssessments = allCpmkAssessmentsData[matkulId] || [];
        const matkulAssessment = matkulAssessmentsData[matkulId] || {};
        const choice = matkulAssessment.self_assessment_value || '';
        const disabledAttr = isLocked ? 'disabled' : ''; // Tambahkan atribut disabled jika terkunci

        let formHtml = `
            <form action="{{ route('store-matkul-self-assessment') }}" method="POST" class="p-3 bg-light border rounded">
                @csrf
                <input type="hidden" name="matkul_id" value="${matkulId}">
                <h5>Formulir Penilaian: <strong>${matkul.nama_matkul}</strong></h5>
                <div class="my-3">
                    <label class="form-label fw-bold">Apakah Anda ingin mengajukan RPL untuk mata kuliah ini?</label>
                    <div class="form-check">
                        <input class="form-check-input choice-radio" type="radio" name="choice" id="ajukan-form-${matkulId}" value="Mengajukan" ${choice === 'Mengajukan' ? 'checked' : ''} ${disabledAttr}>
                        <label class="form-check-label" for="ajukan-form-${matkulId}">Ya, saya ingin mengajukan.</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input choice-radio" type="radio" name="choice" id="tidak-ajukan-form-${matkulId}" value="Tidak Mengajukan" ${choice === 'Tidak Mengajukan' ? 'checked' : ''} ${disabledAttr}>
                        <label class="form-check-label" for="tidak-ajukan-form-${matkulId}">Tidak, saya tidak mengajukan.</label>
                    </div>
                </div>
                <div class="cpmk-section" style="${choice === 'Mengajukan' ? '' : 'display: none;'}">
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>CPMK</th>
                                    <th>Mata Kuliah Dasar</th>
                                    <th>Nilai Dasar</th>
                                    <th class="text-center">Self Assessment</th>
                                </tr>
                            </thead>
                            <tbody>`;
        
        if (matkul.cpmk && matkul.cpmk.length > 0) {
            matkul.cpmk.forEach(cpmk => {
                const existing = cpmkAssessments.find(a => a.cpmk_id == cpmk.id) || {};
                const matkulDasar = existing.matkul_dasar || '';
                const nilaiMatkulDasar = existing.nilai_matkul_dasar || '';
                const selfValue = existing.self_assessment_value || '';
                formHtml += `
                    <tr>
                        <td>${cpmk.penjelasan}</td>
                        <td><input type="text" name="assessments[${cpmk.id}][matkul_dasar]" class="form-control" value="${matkulDasar}" ${disabledAttr}></td>
                        <td><input type="text" name="assessments[${cpmk.id}][nilai_matkul_dasar]" class="form-control" value="${nilaiMatkulDasar}" ${disabledAttr}></td>
                        <td>
                            <select name="assessments[${cpmk.id}][self_assessment_value]" class="form-select" ${disabledAttr}>
                                <option value="" ${selfValue === '' ? 'selected' : ''}>Pilih...</option>
                                <option value="Sangat Baik" ${selfValue === 'Sangat Baik' ? 'selected' : ''}>Sangat Baik</option>
                                <option value="Baik" ${selfValue === 'Baik' ? 'selected' : ''}>Baik</option>
                                <option value="Tidak Pernah" ${selfValue === 'Tidak Pernah' ? 'selected' : ''}>Tidak Pernah</option>
                            </select>
                        </td>
                    </tr>`;
            });
        } else {
            formHtml += `<tr><td colspan="4" class="text-center">Tidak ada CPMK untuk mata kuliah ini.</td></tr>`;
        }
        formHtml += `</tbody></table></div></div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success" ${disabledAttr}>Simpan Penilaian</button>
                </div>
            </form>`;
        cell.innerHTML = formHtml;
        
        cell.querySelectorAll('.choice-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const cpmkSection = this.closest('form').querySelector('.cpmk-section');
                if(this.value === 'Mengajukan') {
                    cpmkSection.style.display = 'block';
                } else {
                    cpmkSection.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush