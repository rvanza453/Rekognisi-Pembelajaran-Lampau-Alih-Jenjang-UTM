
<?php $__env->startSection('content'); ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Daftar Mahasiswa Mengajukan Banding</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Mahasiswa Banding</h5>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__empty_1 = true; $__currentLoopData = $mahasiswa_banding; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camaba_id => $banding_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $camaba = $banding_group->first()->calon_mahasiswa; ?>
                    
                    
                    <?php if($camaba): ?>
                    <tr>
                      <td><?php echo e($loop->iteration); ?></td>
                      <td><?php echo e($camaba->nama ?? '-'); ?></td>
                      <td><?php echo e($camaba->nim ?? '-'); ?></td>
                      <td>
                        <a href="<?php echo e(route('admin.detail-banding-mahasiswa', $camaba->id)); ?>" class="btn btn-primary btn-sm">Lihat Detail Banding</a>
                      </td>
                    </tr>
                    <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr>
                    <td colspan="4" class="text-center">Tidak ada mahasiswa yang mengajukan banding.</td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Admin/daftar-banding.blade.php ENDPATH**/ ?>