<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>RPL_UTM</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo e(asset('assets/img/favicon.png')); ?>" rel="icon">
  <link href="<?php echo e(asset('assets/img/apple-touch-icon.png')); ?>" rel="apple-touch-icon">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo e(asset('assets/vendor/bootstrap/css/bootstrap.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/boxicons/css/boxicons.min.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/quill/quill.snow.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/quill/quill.bubble.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/remixicon/remixicon.css')); ?>" rel="stylesheet">
  <link href="<?php echo e(asset('assets/vendor/simple-datatables/style.css')); ?>" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo e(asset('assets/css/style.css')); ?>" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Mar 17 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  <!-- table responsif -->

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

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/super/dashboard" class="logo d-flex align-items-center">
        <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="">
        <span class="d-none d-lg-block">E-RPL</span>
        <spanmini> Super Admin</spanmini>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <!--<div class="search-bar">-->
    <!--  <form class="search-form d-flex align-items-center" method="POST" action="#">-->
    <!--    <input type="text" name="query" placeholder="Search" title="Enter search keyword">-->
    <!--    <button type="submit" title="Search"><i class="bi bi-search"></i></button>-->
    <!--  </form>-->
    <!--</div><!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown">

          <!-- <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">4</span>
          </a>End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              You have 4 new notifications
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Lorem Ipsum</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>30 min. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-x-circle text-danger"></i>
              <div>
                <h4>Atque rerum nesciunt</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>1 hr. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-check-circle text-success"></i>
              <div>
                <h4>Sit rerum fuga</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>2 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-info-circle text-primary"></i>
              <div>
                <h4>Dicta reprehenderit</h4>
                <p>Quae dolorem earum veritatis oditseno</p>
                <p>4 hrs. ago</p>
              </div>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li class="dropdown-footer">
              <a href="#">Show all notifications</a>
            </li>

          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <!-- <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-chat-left-text"></i>
            <span class="badge bg-success badge-number">3</span>
          </a>End Messages Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow messages">
            <li class="dropdown-header">
              You have 3 new messages
              <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo e(asset('assets/img/messages-1.jpg')); ?>" alt="" class="rounded-circle">
                <div>
                  <h4>Maria Hudson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>4 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo e(asset('assets/img/messages-2.jpg')); ?>" alt="" class="rounded-circle">
                <div>
                  <h4>Anna Nelson</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>6 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="message-item">
              <a href="#">
                <img src="<?php echo e(asset('assets/img/messages-3.jpg')); ?>" alt="" class="rounded-circle">
                <div>
                  <h4>David Muldon</h4>
                  <p>Velit asperiores et ducimus soluta repudiandae labore officia est ut...</p>
                  <p>8 hrs. ago</p>
                </div>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="dropdown-footer">
              <a href="#">Show all messages</a>
            </li>

          </ul><!-- End Messages Dropdown Items -->

        </li>
        <!-- End Messages Nav -->
        <?php
            $namaUserDiHeader = 'Guest'; // Nama default jika tidak login
            $fotoUrlDiHeader = null;    // Foto default null
            $detailUserDiHeader = 'Pengunjung'; // Detail default

            // Path foto default (sesuaikan jika nama file atau pathnya berbeda)
            $fotoDefaultPath = asset('assets/img/profile-img.jpg'); // Atau default-profile.jpg

            if (Auth::check()) {
                $currentUser = Auth::user();
                $profileInfoDariUser = $currentUser->getProfileInfo(); // Panggil method dari objek user

                $namaUserDiHeader = $profileInfoDariUser['nama'] ?? $currentUser->name; // Ambil dari profileInfo atau default ke user->name

            }
        ?>
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo e(asset('assets/img/profile-img.jpg')); ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">Super Admin</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Super Admin</h6>
              <span>Universitas Trunojoyo Madura</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <!--<li>-->
            <!--  <a class="dropdown-item d-flex align-items-center" href="users-profile.html">-->
            <!--    <i class="bi bi-person"></i>-->
            <!--    <span>My Profile</span>-->
            <!--  </a>-->
            <!--</li>-->
            <!--<li>-->
            <!--  <hr class="dropdown-divider">-->
            <!--</li>-->

            <!--<li>-->
            <!--  <a class="dropdown-item d-flex align-items-center" href="users-profile.html">-->
            <!--    <i class="bi bi-gear"></i>-->
            <!--    <span>Account Settings</span>-->
            <!--  </a>-->
            <!--</li>-->
            <!--<li>-->
            <!--  <hr class="dropdown-divider">-->
            <!--</li>-->

            <!--<li>-->
            <!--  <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">-->
            <!--    <i class="bi bi-question-circle"></i>-->
            <!--    <span>Need Help?</span>-->
            <!--  </a>-->
            <!--</li>-->
            <!--<li>-->
            <!--  <hr class="dropdown-divider">-->
            <!--</li>-->

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
              <form id="logout-form-sidebar" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                  <?php echo csrf_field(); ?>
              </form>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="/super/dashboard">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Kelola Akun</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo e(route('super.account-super-admin-table')); ?>">
              <i class="bi bi-circle"></i><span>Super Admin</span>
            </a>
          </li>
          <li>
            <a href="<?php echo e(route('super.account-admin-table')); ?>">
              <i class="bi bi-circle"></i><span>Admin</span>
            </a>
          </li>
          <li>
            <a href="<?php echo e(route('super.account-assessor-table')); ?>">
              <i class="bi bi-circle"></i><span>Assessor</span>
            </a>
          </li>
          <li>
            <a href="<?php echo e(route('super.account-user-table')); ?>">
              <i class="bi bi-circle"></i><span>Mahasiswa RPL</span>
            </a>
          </li>
        </ul>
      </li>
      <a class="nav-link" data-bs-target="#data-user-sidebar" data-bs-toggle="collapse" href="#">
        <i class="bi bi-journal-text"></i><span>Data User</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="data-user-sidebar" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="<?php echo e(route('super.data-user-table')); ?>">
            <i class="bi bi-circle"></i><span>Mahasiswa RPL</span>
          </a>
        </li>
        <li>
          <a href="<?php echo e(route('super.data-assessor-table')); ?>">
            <i class="bi bi-circle"></i><span>Assessor</span>
          </a>
        </li>
      </ul>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('super.kelola-matkul-table')); ?>">
          <i class="bi bi-journal-text"></i><span>Mata Kuliah</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('super.kelola-assessor-mahasiswa')); ?>">
          <i class="bi bi-journal-text"></i><span>Penugasan Assessor</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('super.kelola-periode')); ?>">
          <i class="bi bi-calendar-check"></i>
          <span>Jadwal Periode RPL</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/super/daftar-banding">
          <i class="bi bi-exclamation-circle"></i>
          <span>Daftar Banding</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('logout')); ?>"
           onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
          <i class="bi bi-door-open"></i><span>Logout</span>
        </a>
        <form id="logout-form-sidebar" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
      </li>
    </li><!-- End Forms Nav -->

    </ul>

  </aside><!-- End Sidebar-->

  <!-- Content Wrapper. Contains page content -->
  <?php echo $__env->yieldContent('content'); ?>
  <!-- /.content-wrapper -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
    <strong><span>RPL Universitas Trunojoyo Madura</span></strong>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?php echo e(asset('assets/vendor/apexcharts/apexcharts.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/chart.js/chart.umd.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/echarts/echarts.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/quill/quill.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/simple-datatables/simple-datatables.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/tinymce/tinymce.min.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/php-email-form/validate.js')); ?>"></script>

  <!-- Template Main JS File -->
  <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>


  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</html>
<?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/layout/super_admin.blade.php ENDPATH**/ ?>