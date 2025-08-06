@extends('layout.user')
@section('content')
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Form Layouts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item"><a href="/self-assessment-table">Mata Kuliah</a></li>
                <li class="breadcrumb-item active">Self Assessment</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center">Pilihlah Mata Kuliah yang akan dikonversi</h5>

                        <form class="row g-3" action="{{ route('self-assessment') }}" method="GET">
                            <div class="col-6 my-3">
                                <label for="matkul" class="form-label">Mata Kuliah</label>
                                <div>
                                    <select name="matkul_id" id="matkul" class="form-select" required onchange="this.form.submit()">
                                        @foreach($matkul as $matkul)
                                            <option value="{{ $matkul->id }}" {{ $matkul_id == $matkul->id ? 'selected' : '' }}>{{ $matkul->nama_matkul }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>

                        <h5 class="card-title text-center">Mata Kuliah yang akan diisi</h5>
                        <h6>Sub CPMK yang harus dipenuhi oleh mata kuliah tersebut</h6>

                        <form action="{{ route('add-self-assessment') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Capaian Pembelajaran Mata Kuliah</th>
                                        <th scope="col">Profisiensi Pengetahuan</th>
                                        <th scope="col">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cpmks as $index => $cpmk)
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $cpmk->penjelasan }}</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="self_assessment_value[{{ $cpmk->id }}]" id="sb_{{ $cpmk->id }}" value="Sangat Baik" required>
                                                <label class="form-check-label" for="sb_{{ $cpmk->id }}">Sangat Baik</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="self_assessment_value[{{ $cpmk->id }}]" id="b_{{ $cpmk->id }}" value="Baik">
                                                <label class="form-check-label" for="b_{{ $cpmk->id }}">Baik</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="self_assessment_value[{{ $cpmk->id }}]" id="tp_{{ $cpmk->id }}" value="Tidak Pernah">
                                                <label class="form-check-label" for="tp_{{ $cpmk->id }}">Tidak Pernah</label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="matkul_dasar[{{ $cpmk->id }}]" class="form-control" placeholder="Mata Kuliah Dasar dari Transkrip" required>
                                        </td>
                                        <td>
                                            <input type="text" name="nilai_matkul_dasar[{{ $cpmk->id }}]" class="form-control" placeholder="Nilai Matkul Dasar" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main><!-- End #main -->

<script>
    function toggleUploadFields(selectElement, index) {
        const selectedValue = selectElement.value;
        const modalId = '#exampleModal' + index;

        if (selectedValue === 'Tidak Pernah') {
            $(modalId).find('input').val(''); // Clear inputs
            $(modalId).find('input').prop('required', false); // Remove required attribute
        } else {
            $(modalId).find('input').prop('required', true); // Make inputs required again
        }
    }
</script>
@endsection
