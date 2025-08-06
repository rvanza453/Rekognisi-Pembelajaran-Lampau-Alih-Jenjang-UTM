@extends('layout.user')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Bukti Alih Jenjang</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Bukti Alih Jenjang</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title center" align="center">Bukti Alih Jenjang</h5>

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

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBuktiModal">
                                <i class="bi bi-plus-circle"></i> Tambah Bukti
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Nama File</th>
                                        <th>Tanggal Upload</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bukti_list as $index => $bukti)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $bukti->jenis_dokumen }}</td>
                                            <td>{{ $bukti->file }}</td>
                                            <td>{{ $bukti->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('bukti-alih-jenjang-file', ['filename' => $bukti->file]) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-info me-1">
                                                        <i class="bi bi-eye"></i> Lihat
                                                    </a>
                                                    <form action="{{ route('bukti-alih-jenjang-delete', ['id' => $bukti->nomor_dokumen]) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus bukti ini?');"
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada bukti yang diunggah</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Tambah Bukti -->
                        <div class="modal fade" id="addBuktiModal" tabindex="-1" aria-labelledby="addBuktiModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addBuktiModalLabel">Tambah Bukti Alih Jenjang</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('bukti-alih-jenjang-add') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="jenis_dokumen" class="form-label">Jenis Dokumen</label>
                                                <select class="form-select" id="jenis_dokumen" name="jenis_dokumen" required>
                                                    <option value="">Pilih Jenis Dokumen</option>
                                                    <option value="Screenshot PDDIKTI">Screenshot PDDIKTI</option>
                                                    <option value="Panduan Kurikulum">Panduan Kurikulum (Prodi asal)</option>
                                                    <option value="Surat ket. pernah kuliah">Surat ket. pernah kuliah (Bagi Mahasiswa yang belum lulus)</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="file" class="form-label">Upload File Bukti (PDF/JPG/PNG)</label>
                                                <input type="file" class="form-control" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                                <small class="text-muted">Format yang didukung: PDF, JPG, JPEG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
@endsection 