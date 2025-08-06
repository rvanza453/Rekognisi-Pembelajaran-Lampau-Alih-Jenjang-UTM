@extends('layout.user')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Upload Transkrip / KHS</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Upload Transkrip</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title center" align="center">Upload Transkrip / KHS</h5>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if($existing_transkrip)
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5>Transkrip / KHS yang sudah diunggah:</h5>
                                        <p>File: {{ $existing_transkrip->file }}</p>
                                        <p>Tanggal Upload: {{ $existing_transkrip->created_at->format('d-m-Y H:i:s') }}</p>
                                    </div>
                                    <form action="{{ route('delete-transkrip', $existing_transkrip->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus transkrip ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- PDF Viewer -->
                                <div class="pdf-container mt-3 mb-3">
                                    <embed 
                                        src="{{ route('view-transkrip', $existing_transkrip->file) }}"
                                        type="application/pdf"
                                        width="100%"
                                        height="600px"
                                    />
                                </div>

                                <!-- Tombol untuk menambah mata kuliah -->
                                <!--<button class="btn btn-primary" data-toggle="modal" data-target="#tambahMatkulModal">-->
                                <!--    Tambah Mata Kuliah-->
                                <!--</button>-->

                                <!-- Tabel Mata Kuliah yang Sudah Diinput -->
                                @if($existing_transkrip->mata_kuliah_transkrip)
                                    <div class="mt-4">
                                        <h5>Mata Kuliah yang Sudah Diinput:</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Mata Kuliah</th>
                                                        <th>Nilai</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($existing_transkrip->mata_kuliah_transkrip as $mk)
                                                        <tr>
                                                            <td>{{ $mk['nama'] }}</td>
                                                            <td>{{ $mk['nilai'] }}</td>
                                                            <td>
                                                                <button class="btn btn-sm btn-danger delete-mk" 
                                                                        data-mk-index="{{ $loop->index }}">
                                                                    Hapus
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @else
                            <!-- Form Upload File Transkrip -->
                            <form action="{{ route('transkrip-add-data') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label">Upload File Transkrip / KHS (PDF)</label>
                                    <input type="file" class="form-control" id="file" name="file" accept="application/pdf" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        @endif

                        <!-- Modal Tambah Mata Kuliah -->
                        <div class="modal fade" id="tambahMatkulModal" tabindex="-1" role="dialog" aria-labelledby="tambahMatkulModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tambahMatkulModalLabel">Tambah Mata Kuliah</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('store-matkul-transkrip', $existing_transkrip->id ?? '') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nama Mata Kuliah</label>
                                                <input type="text" class="form-control" name="mata_kuliah[nama]" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nilai</label>
                                                <input type="text" class="form-control" name="mata_kuliah[nilai]" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.pdf-container {
    border: 1px solid #dee2e6;
    background: #f8f9fa;
}
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the transcript ID from a data attribute or a hidden input
        const transkripId = {{ $existing_transkrip->id ?? 'null' }};

        if (transkripId) {
            document.querySelectorAll('.delete-mk').forEach(button => {
                button.addEventListener('click', function () {
                    const matkulIndex = this.getAttribute('data-mk-index');

                    if (confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?')) {
                        fetch(`/user/transkrip/${transkripId}/matkul/${matkulIndex}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                // Reload the page or remove the row from the table
                                window.location.reload();
                            } else {
                                alert('Gagal menghapus mata kuliah: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat menghapus mata kuliah.');
                        });
                    }
                });
            });
        }
    });
</script>
@endpush