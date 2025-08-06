@extends('layout.super_admin')

@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Kelola Periode</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('super.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Kelola Periode</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Periode</h5>
                        
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

                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPeriodeModal">
                                Tambah Periode
                            </button>
                        </div>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tahun Ajaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodes as $periode)
                                <tr>
                                    <td>{{ $periode->tahun_ajaran }}</td>
                                    <td>
                                        @if($periode->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($periode->is_active)
                                            <form action="{{ route('super.deactivate-periode', $periode->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Nonaktifkan periode ini?')">
                                                    Nonaktifkan
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('super.activate-periode', $periode->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Aktifkan periode ini?')">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('super.delete-periode', $periode->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus periode ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Add Periode Modal -->
<div class="modal fade" id="addPeriodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Periode Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super.add-periode') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" required 
                               placeholder="Contoh: 2024/2025">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 