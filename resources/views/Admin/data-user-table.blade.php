@extends('layout.admin')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Mahasiswa RPL</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
          <li class="breadcrumb-item active">Data Mahasiswa RPL</li>
        </ol>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Data Mahasiswa RPL</h5>

              <!-- Default Table -->
              <div class="table-container">
                <table class="table-admin table-bordered mb-3">
                  <thead>
                    <tr>
                      <th class="admin-tabb" scope="col">No</th>
                      <th class="admin-tabb" scope="col">Nama</th>
                      <th class="admin-tabb" scope="col">Jurusan</th>
                      <th class="admin-tabb" scope="col">Periode</th>
                      <!-- <th scope="col">Email</th>
                      <th scope="col">Username</th>
                      <th scope="col">Password</th> -->
                      <th class="admin-tabb" scope="col">Tempat Lahir</th>
                      <th class="admin-tabb" scope="col">Tanggal Lahir</th>
                      <th class="admin-tabb" scope="col">No.HP</th>
                      <th class="admin-tabb" scope="col">No.Whatsapp</th>
                      <th class="admin-tabb" scope="col">Kelamin</th>
                      <th class="alamat" scope="col">Alamat</th>
                      <th class="admin-tabb" scope="col">Kota</th>
                      <th class="admin-tabb" scope="col">Provinsi</th>
                      <th class="admin-tabb" scope="col">Kode Pos</th>
                      <th class="admin-tabb" scope="col">Jenjang</th>
                      <th class="admin-tabb" scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($users_camaba as $index => $user)
                      <tr>
                        <td class="admin-tabb">{{ $index + 1 }}</td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->nama }}
                          @else
                              Data tidak tersedia
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa && $user->calon_mahasiswa->jurusan)
                              {{ $user->calon_mahasiswa->jurusan->nama_jurusan }}
                          @else
                              
                          @endif
                      </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa && $user->calon_mahasiswa->periode)
                              {{ $user->calon_mahasiswa->periode->tahun_ajaran }}
                          @else
                              Data tidak tersedia
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->tempat_lahir }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->tanggal_lahir }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa && $user->calon_mahasiswa->nomor_rumah)
                              +62{{ preg_replace('/^0/', '', $user->calon_mahasiswa->nomor_rumah) }}
                          @else
                              -
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa && $user->calon_mahasiswa->nomor_telepon)
                              +62{{ preg_replace('/^0/', '', $user->calon_mahasiswa->nomor_telepon) }}
                          @else
                              -
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->kelamin }}
                          @else
                              
                          @endif
                        </td>
                        <td class="alamat">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->alamat }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->kota }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->provinsi }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa)
                              {{ $user->calon_mahasiswa->kode_pos }}
                          @else
                              
                          @endif
                        </td>
                        <td class="admin-tabb">
                          @if($user->calon_mahasiswa && $user->calon_mahasiswa->jurusan)
                                    {{ $user->calon_mahasiswa->jurusan->jenjang }}
                                @else
                                    Data tidak tersedia
                                @endif
                        </td>
                        <td>
                          <form action="{{ route('delete-user', $user->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Delete</button>
                          </form>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Export
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($user->calon_mahasiswa)
                                        <li><a class="dropdown-item" href="{{ route('export-word02', $user->calon_mahasiswa->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F02</a></li>
                                        <!-- <li><a class="dropdown-item" href="{{ route('exportPdfFromWordF02', $user->calon_mahasiswa->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF V2</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export-pdfF02', $user->calon_mahasiswa->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF</a></li> -->
                                        <li><a class="dropdown-item" href="{{ route('export-word08', $user->calon_mahasiswa->id) }}"><i class="bi bi-file-word me-1"></i> Export Word F08</a></li>
                                        <!-- <li><a class="dropdown-item" href="{{ route('exportPdfFromWordF08', $user->calon_mahasiswa->id) }}"><i class="bi bi-file-pdf me-1"></i> Export PDF F08</a></li> -->
                                    @else
                                        <li><span class="dropdown-item text-muted">Data Mahasiswa Tidak Tersedia</span></li>
                                    @endif
                                </ul>
                            </div>                          
                        </td>
                      </tr>

                    @endforeach
                    
                  </tbody>
                </table>
              </div>
              <!-- End Default Table Example -->

            </div>
          </div>

          
        </div>
      </div>
    </section>


  </main><!-- End #main -->
@endsection
