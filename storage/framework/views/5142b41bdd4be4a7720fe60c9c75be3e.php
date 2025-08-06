
<?php $__env->startSection('content'); ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>List Ajuan Form</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">List Ajuan Form</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">


          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">List Nama Mahasiswa</h5>

              <!-- Default Table -->
              <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Program Studi</th>
                        <th scope="col">Deadline Penilaian</th>
                        <th scope="col">Status RPL</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $camaba; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mahasiswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th scope="row"><?php echo e($index + 1); ?></th>
                            <td><?php echo e($mahasiswa->nama); ?></td>
                            <td><?php echo e($mahasiswa->jurusan->nama_jurusan); ?></td>
                            <td>
                              <?php if($mahasiswa->assessment && $mahasiswa->assessment->deadline): ?>
                                <?php echo e(\Carbon\Carbon::parse($mahasiswa->assessment->deadline)->format('d/m/Y H:i')); ?>

                                <?php
                                    $deadline = $mahasiswa->assessment->deadline;
                                    $now = now();
                                ?>
                                <?php if($mahasiswa->assessment->rpl_status == 'selesai'): ?>
                                    <div><span class="badge bg-success mt-1">Selesai</span></div>
                                <?php else: ?>
                                    <?php if($deadline < $now): ?>
                                        <span class="badge bg-danger">Terlambat</span>
                                    <?php elseif($deadline <= $now->addDays(3)): ?>
                                        <span class="badge bg-warning">Mendekati Deadline</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                              <?php else: ?>
                                  <span class="text-muted">Belum diatur</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if($mahasiswa->assessment && $mahasiswa->assessment->rpl_status): ?>
                                  <span class="badge bg-info"><?php echo e(ucfirst(str_replace('-', ' ', $mahasiswa->assessment->rpl_status))); ?></span>
                              <?php else: ?>
                                  <span class="badge bg-secondary">-</span>
                              <?php endif; ?>
                            </td>
                            <td>
                                <?php if($mahasiswa->id): ?>
                                    <a type="button" 
                                       href="<?php echo e(route('detail-user', ['id' => $mahasiswa->id])); ?>" 
                                       class="bi-box-arrow-right fs-2"
                                       onclick="console.log('Clicked ID: <?php echo e($mahasiswa->id); ?>')">
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger">ID tidak tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
              <!-- End Default Table Example -->

            </div>
          </div>

          
        </div>
      </div>
    </section>


  </main><!-- End #main -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.assessor', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Assessor/list-name-table.blade.php ENDPATH**/ ?>