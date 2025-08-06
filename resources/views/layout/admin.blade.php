<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>RPL_UTM - Admin</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
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
    .table-admin {
        table-layout: auto;
        width: 100%;
    }
    .admin-tabb {
        white-space: nowrap;
    }
    .alamat {
        padding: 8px;
        max-width: 400px;
        min-width: 250px;
    }
  </style>

</head>

<body>

  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/admin/dashboard" class=" d-flex align-items-center">
        <img src="{{ asset('assets/img/logo3.png') }}" alt=""  width="90" height="auto">
        <spanmini class="me-4"> Admin </spanmini>
      </a>
      <i class="bi bi-list toggle-sidebar-btn ms-5"></i>
    </div><nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!--<li class="nav-item d-block d-lg-none">-->
        <!--   <a class="nav-link nav-icon search-bar-toggle " href="#">-->
        <!--    <i class="bi bi-search"></i>-->
        <!--  </a>-->
        <!--</li>-->
        <li class="nav-item me-2">
          <a class="nav-link btn btn-outline-primary btn-sm" href="https://rpl-eporto-utm.uinfaq.org/" target="_blank" title="Pindah ke sistem E-Portofolio">
            <i class="bi bi-arrow-right-circle"></i>
            <span class="d-none d-md-block">Ke E-Portofolio</span>
          </a>
        </li>
         <!-- <li class="nav-item dropdown"> ... </li> 
         <li class="nav-item dropdown"> ... </li>  -->

        @php
            $namaUserDiHeaderAdmin = 'Guest';
            $fotoUrlDiHeaderAdmin = null;
            $detailUserDiHeaderAdmin = 'Pengguna';
            // Path foto default (sesuaikan jika nama file atau pathnya berbeda)
            $fotoDefaultPathAdmin = asset('assets/img/profile-img.jpg');

            if (Auth::check()) {
                $currentUserAdmin = Auth::user();
                // Pastikan method getProfileInfo() ada di model User dan mengembalikan data yang benar
                $profileInfoDariUserAdmin = $currentUserAdmin->getProfileInfo();

                $namaUserDiHeaderAdmin = $profileInfoDariUserAdmin['nama'] ?? $currentUserAdmin->name;

                // Logika untuk mendapatkan URL foto admin
                // Asumsi $profileInfoDariUserAdmin['foto_url'] berisi URL lengkap foto.
                if (!empty($profileInfoDariUserAdmin['foto_url'])) {
                    $fotoUrlDiHeaderAdmin = $profileInfoDariUserAdmin['foto_url'];
                } else {
                    $fotoUrlDiHeaderAdmin = $fotoDefaultPathAdmin;
                }

                $detailUserDiHeaderAdmin = $profileInfoDariUserAdmin['jurusan'] ?? ($profileInfoDariUserAdmin['peran'] ?? ucfirst($currentUserAdmin->role ?? 'Admin'));
            }
        @endphp

        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <div style="width: 35px; height: 35px; overflow: hidden; position: relative; border-radius: 50%;">
                <img src="{{ $fotoUrlDiHeaderAdmin ?? $fotoDefaultPathAdmin }}" alt="Profile"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ $namaUserDiHeaderAdmin }}</span>
            </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{ $namaUserDiHeaderAdmin }}</h6>
              <span>{{ $detailUserDiHeaderAdmin }}</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            @if (Auth::check())
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form-admin-header').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
              <form id="logout-form-admin-header" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
            @endif
          </ul>
        </li>

      </ul>
    </nav></header><aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link" href="/admin/dashboard"> 
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link " href="{{ route('profile-view-admin') }}">
          <i class="bi bi-person"></i><span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-target="#account-user-sidebar" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i><span>Tambah Akun RPL</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="account-user-sidebar" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/account-user-table"> 
              <i class="bi bi-circle"></i><span>Mahasiswa RPL</span>
            </a>
          </li>
          <li>
            <a href="/account-assessor-table"> 
              <i class="bi bi-circle"></i><span>Assesor</span>
            </a>
          </li>
          <li>
            <a href="/account-admin-table"> 
              <i class="bi bi-circle"></i><span>Admin</span>
            </a>
          </li>
        </ul>
        <a class="nav-link" data-bs-target="#data-user-sidebar" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Data User</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="data-user-sidebar" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="/data-user-table"> 
              <i class="bi bi-circle"></i><span>Mahasiswa RPL</span>
            </a>
          </li>
          <li>
            <a href="/data-assessor-table"> 
              <i class="bi bi-circle"></i><span>Assessor</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/kelola-matkul-table">
          <i class="bi bi-book"></i><span>Mata Kuliah</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/kelola-assessor-mahasiswa">
          <i class="bi bi-card-checklist"></i><span>Penugasan Assessor</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/admin/daftar-banding">
          <i class="bi bi-exclamation-circle"></i>
          <span>Daftar Banding</span>
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
        <a class="nav-link" href="{{ route('login') }}">
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
  {{-- <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script> --}}

  <script src="{{ asset('assets/js/main.js') }}"></script>

  @stack('scripts')
</body>

     <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> 
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script> 
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script> 
</html>