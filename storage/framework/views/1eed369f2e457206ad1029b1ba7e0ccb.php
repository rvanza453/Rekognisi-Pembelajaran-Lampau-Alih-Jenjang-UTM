<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <!-- css untuk review bintang -->
  <style>
    .star-rating {
      direction: rtl;
      display: inline-block;
      font-size: 3em;
    }
    .star-rating input[type="radio"] {
      display: none;
    }
    .star-rating label {
      color: #ddd;
      cursor: pointer;
    }
    .star-rating input[type="radio"]:checked ~ label {
      color: #f5b301;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #f5b301;
    }
    .notes {
      margin-top: 20px;
      font-size: 0.875em; /* Ukuran font lebih kecil */
      color: #888; /* Warna abu-abu */
    }

  </style>

  <title>RPL_UTM</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- data diri assessor -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"  rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
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
    /* Efek hover */
    .nav-pills .nav-link:hover {
        background-color: #0d6efd;
        color: white !important;
    }

    /* Tab aktif */
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white !important;
        font-weight: bold;
    }

    /* Tab non-aktif */
    .nav-pills .nav-link:not(.active) {
        background-color: transparent;
        color: #0d6efd !important;
    }

    /* Padding internal */
    .nav-pills .nav-link {
        padding: 0.5rem 1rem;
    }

    /* Efek hover pada tombol utama */
    .dropdown-toggle:hover {
        background-color: #007bff;
        border-color: #007bff;
    }

    /* Efek hover pada item dropdown */
    .dropdown-item:hover {
        background-color: #e9ecef;

    .table th,
    .table td {
      white-space: nowrap;
      font-size: 0.85rem;
    }

    @media (max-width: 768px) {
      .label {
        min-width: 120px !important;
      }

      .ijazah-overview-assessor .row {
        flex-direction: column;
      }

      .input-group .form-control {
        width: 100%;
      }

      .table thead {
        display: none;
      }

      .table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
      }

      .table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0.6rem;
        border: none;
        border-bottom: 1px solid #dee2e6;
      }

      .table tbody tr:last-child td {
        border-bottom: none;
      }
    }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="/" class=" d-flex align-items-center">
        <img src="<?php echo e(asset('assets/img/logo3.png')); ?>" alt=""  width="90" height="auto">
        <spanmini> Assessor</spanmini>
      </a>
      <i class="bi bi-list toggle-sidebar-btn ms-5"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <?php
            $assessor = \App\Models\Assessor::where('user_id', auth()->id())->first();
            $deadlineStats = $assessor ? \App\Models\Assessment::getAssessorDeadlineStatistics($assessor->id) : ['total' => 0, 'overdue' => 0, 'approaching' => 0];
            $totalNotifications = $deadlineStats['overdue'] + $deadlineStats['approaching'];
        ?>

        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <?php if($totalNotifications > 0): ?>
              <span class="badge bg-danger badge-number"><?php echo e($totalNotifications); ?></span>
            <?php endif; ?>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              <?php if($totalNotifications > 0): ?>
                Anda memiliki <?php echo e($totalNotifications); ?> notifikasi deadline
              <?php else: ?>
                Tidak ada notifikasi deadline
              <?php endif; ?>
              <a href="<?php echo e(route('list-name-table')); ?>"><span class="badge rounded-pill bg-primary p-2 ms-2">Lihat semua</span></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <?php if($deadlineStats['overdue'] > 0): ?>
              <li class="notification-item">
                <i class="bi bi-exclamation-triangle text-danger"></i>
                <div>
                  <h4>Deadline Terlambat</h4>
                  <p><?php echo e($deadlineStats['overdue']); ?> penilaian telah melewati deadline</p>
                  <p>Segera selesaikan penilaian</p>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
            <?php endif; ?>

            <?php if($deadlineStats['approaching'] > 0): ?>
              <li class="notification-item">
                <i class="bi bi-clock text-warning"></i>
                <div>
                  <h4>Deadline Mendekati</h4>
                  <p><?php echo e($deadlineStats['approaching']); ?> penilaian mendekati deadline</p>
                  <p>≤ 3 hari lagi</p>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
            <?php endif; ?>

            <?php if($totalNotifications == 0): ?>
              <li class="notification-item">
                <i class="bi bi-check-circle text-success"></i>
                <div>
                  <h4>Semua Penilaian Tepat Waktu</h4>
                  <p>Tidak ada deadline yang mendesak</p>
                  <p>Teruskan dengan baik</p>
                </div>
              </li>
            <?php endif; ?>

            <li class="dropdown-footer">
              <a href="<?php echo e(route('list-name-table')); ?>">Lihat semua penilaian</a>
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

        </li><!-- End Messages Nav -->

        <?php
            $profileInfo = auth()->user()->getProfileInfo();
        ?>

        <li class="nav-item dropdown pe-3">
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <div style="width: 35px; height: 35px; overflow: hidden; position: relative; border-radius: 50%;">
                <img src="<?php echo e($profileInfo['foto_url']); ?>" alt="Profile" 
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo e($profileInfo['nama']); ?></span>
            </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo e($profileInfo['nama']); ?></h6>
              <span><?php echo e($profileInfo['jurusan']); ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('logout')); ?>" 
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
              <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
              </form>
            </li>
          </ul>
        </li>

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="/assessor/dashboard">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('profile-view-assessor')); ?>">
          <i class="bi bi-person"></i><span>Profile</span>
        </a>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link" href="/list-name-table">
          <i class="bi bi-journal-text"></i><span>List Ajuan Form</span>
        </a>
      </li><!-- End Forms Nav -->

      <li class="nav-item">
        <a class="nav-link" href="<?php echo e(route('logout')); ?>"
           onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
          <i class="bi bi-door-open"></i><span>Logout</span>
        </a>
        <form id="logout-form-sidebar" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
      </li>

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

  <!-- data diri assessor -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</html>
<?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/layout/assessor.blade.php ENDPATH**/ ?>