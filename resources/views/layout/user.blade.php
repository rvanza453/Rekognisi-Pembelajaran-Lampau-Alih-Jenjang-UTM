<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>RPL_UTM</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  {{-- Menghapus referensi Bootstrap 4.1.3 jika Anda sudah menggunakan Bootstrap 5 dari vendor template --}}
  {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"> --}}

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <style>
    .table-container {
      width: 100%;
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    @media (max-width: 600px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }
      thead tr {
        display: none;
      }
      tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        padding: 10px;
      }
      td {
        display: flex;
        justify-content: space-between;
        padding-left: 50%;
        position: relative;
      }
      td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        white-space: nowrap;
        font-weight: bold;
      }
    }
  </style>
</head>

<body>

  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/user/dashboard" class=" d-flex align-items-center">
        <img src="{{ asset('assets/img/logo3.png') }}" alt=""  width="90" height="auto">
        <spanmini> Mahasiswa RPL</spanmini>
      </a>
      <i class="bi bi-list toggle-sidebar-btn ms-2"></i>
    </div><nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!--<li class="nav-item d-block d-lg-none">-->
        <!--  <a class="nav-link nav-icon search-bar-toggle " href="#">-->
        <!--    <i class="bi bi-search"></i>-->
        <!--  </a>-->
        <!--</li>-->
        <li class="nav-item me-2">
          <a class="nav-link btn btn-outline-primary btn-sm" href="https://rpl-eporto-utm.uinfaq.org/" target="_blank" title="Pindah ke sistem E-Portofolio">
            <i class="bi bi-arrow-right-circle"></i>
            <span class="d-none d-md-block">Ke E-Portofolio</span>
          </a>
        </li>
        <li class="nav-item dropdown">
           <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
             ...
           </ul>
        </li>

        @php
            $namaUserDiHeader = 'Guest'; // Nama default jika tidak login
            $fotoUrlDiHeader = null;    // Foto default null
            $detailUserDiHeader = 'Pengunjung'; // Detail default

            // Path foto default (sesuaikan jika nama file atau pathnya berbeda)
            $fotoDefaultPath = asset('assets/img/profile-img.jpg'); // Atau default-profile.jpg

            if (Auth::check()) {
                $currentUser = Auth::user();
                $profileInfoDariUser = $currentUser->getProfileInfo(); // Panggil method dari objek user

                $namaUserDiHeader = $profileInfoDariUser['nama'] ?? $currentUser->name; // Ambil dari profileInfo atau default ke user->name

                // Logika untuk mendapatkan URL foto dari $profileInfoDariUser
                // Asumsi $profileInfoDariUser['foto_url'] berisi URL lengkap foto.
                if (!empty($profileInfoDariUser['foto_url'])) {
                    $fotoUrlDiHeader = $profileInfoDariUser['foto_url'];
                } else {
                    $fotoUrlDiHeader = $fotoDefaultPath;
                }

                // Untuk detail user (jurusan/peran)
                $detailUserDiHeader = $profileInfoDariUser['jurusan'] ?? ($profileInfoDariUser['peran'] ?? ucfirst($currentUser->role ?? 'Mahasiswa'));
            }
        @endphp

        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <div style="width: 35px; height: 35px; overflow: hidden; position: relative; border-radius: 50%;">
                <img src="{{ $fotoUrlDiHeader ?? $fotoDefaultPath }}" alt="Profile"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ $namaUserDiHeader }}</span>
            </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{ $namaUserDiHeader }}</h6>
              <span>{{ $detailUserDiHeader }}</span> 
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              @if (Auth::check())
                <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              @endif
            </li>
          </ul></li></ul>
    </nav></header><aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="/user/dashboard">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link " href="/profile-view-camaba"> 
          <i class="bi bi-person"></i>
          <span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Pengisian Berkas</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/view-ijazah"> 
              <i class="bi bi-file-earmark-text"></i>
              <span>Ijazah</span>
            </a>
          </li>
          <li>
            <a href="/bukti-alih-jenjang"> 
              <i class="bi bi-file-earmark-text"></i>
              <span>Bukti</span>
            </a>
          </li>
          <li>
            <a href="/input-transkrip"> 
              <i class="bi bi-circle"></i><span>Input Transkrip Nilai</span>
            </a>
          </li>
          <li>
            <a href="/matkul-self-assessment">
              <i class="bi bi-circle"></i><span>Self-Assessment</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link " href="/view-nilai"> 
          <i class="bi bi-person"></i><span>Hasil Penilaian</span>
        </a>
      </li>
      @if (Auth::check())
      <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
          <i class="bi bi-door-open"></i><span>Logout</span>
        </a>
        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
      </li>
      @else
      <li class="nav-item">
        <a class="nav-link" href="{{ route('login') }}"> {{-- Arahkan ke halaman login jika belum login --}}
          <i class="bi bi-door-closed"></i><span>Login</span>
        </a>
      </li>
      @endif
    </ul>
  </aside>@yield('content')
  <footer id="footer" class="footer">
    <div class="copyright">
    <strong><span>RPL Universitas Trunojoyo Madura</span></strong>
    </div>
  </footer><a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  {{-- <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script> --}} {{-- Komentari jika tidak digunakan --}}

  <script src="{{ asset('assets/js/main.js') }}"></script>

  @stack('scripts')
</body>
     <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script> 
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script> 
</html>