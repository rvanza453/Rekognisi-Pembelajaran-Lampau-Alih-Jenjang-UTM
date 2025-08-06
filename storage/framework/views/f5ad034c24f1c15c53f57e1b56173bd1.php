
<?php $__env->startSection('content'); ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Daftar Mahasiswa Mengajukan Banding (Semua Jurusan)</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Mahasiswa Banding</h5>
            
            <form method="GET" action="<?php echo e(route('super.daftar-banding')); ?>" id="filterForm">
              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="jurusan_filter" class="form-label">Filter Jurusan</label>
                  <select name="jurusan_id" id="jurusan_filter" class="form-select">
                    <option value="">Semua Jurusan</option>
                    <?php $__currentLoopData = $jurusans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jurusan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($jurusan->id); ?>" <?php echo e(request('jurusan_id') == $jurusan->id ? 'selected' : ''); ?>>
                        <?php echo e($jurusan->nama_jurusan); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>
            </form>
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>Jurusan</th>
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
                      <td><?php echo e($camaba->jurusan->nama_jurusan ?? '-'); ?></td>
                      <td>
                        
                        <a href="<?php echo e(route('super.detail-banding-mahasiswa', $camaba->id)); ?>" class="btn btn-primary btn-sm">Lihat Detail Banding</a>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form ketika filter jurusan berubah
    document.getElementById('jurusan_filter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.super_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Super_admin/daftar-banding.blade.php ENDPATH**/ ?>