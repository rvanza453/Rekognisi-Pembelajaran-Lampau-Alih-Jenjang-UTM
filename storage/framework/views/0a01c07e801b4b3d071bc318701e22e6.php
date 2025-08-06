<?php $__env->startSection('content'); ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Lihat Nilai Akhir</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Lihat Nilai Akhir</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">

        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title center" align="center">Rekapitulasi Hasil Penilaian Konversi SKS</h5>
              <?php
                  $user = auth()->user();
                  $calon_mahasiswa = $user ? $user->calon_mahasiswa : null;
                  $assessment = $calon_mahasiswa ? \App\Models\Assessment::where('calon_mahasiswa_id', $calon_mahasiswa->id)->first() : null;
              ?>
              <div class="">Status RPL saat ini: <strong><?php echo e($assessment ? ucfirst(str_replace('-', ' ', $assessment->rpl_status)) : 'Belum mendapatkan assessor, mohon ditunggu'); ?></strong></div>
              <?php if($published_at && is_array($final_results) && !empty($final_results)): ?>
                <?php
                  $is_banding_publish = false;
                  $calon_mahasiswa_id = $calon_mahasiswa ? $calon_mahasiswa->id : null;
                  if($calon_mahasiswa_id) {
                    foreach($final_results as $result) {
                      if(isset($result['matkul']->id)) {
                        $banding = \App\Models\Matkul_score::where('matkul_id', $result['matkul']->id)
                          ->where('calon_mahasiswa_id', $calon_mahasiswa_id)
                          ->where('is_banding', true)
                          ->whereIn('banding_status', ['diterima','ditolak'])
                          ->first();
                        if($banding) { $is_banding_publish = true; break; }
                      }
                    }
                  }
                ?>
                <?php if($is_banding_publish): ?>
                  <div class="alert alert-info">
                    <strong>Perhatian:</strong> Nilai ini adalah <u>hasil setelah proses banding</u>.
                  </div>
                <?php else: ?>
                  <div class="alert alert-success">
                    Hasil penilaian Anda telah dipublikasikan pada <?php echo e(\Carbon\Carbon::parse($published_at)->format('d F Y, H:i')); ?>.
                  </div>
                <?php endif; ?>

                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">No</th>
                      <th scope="col">Mata Kuliah</th>
                      <th scope="col">Status Kelulusan</th>
                      <th scope="col">Nilai Akhir (Skala 100)</th>
                      <th scope="col">Status Banding</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $final_results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                      <?php if(isset($result['matkul']) && $result['matkul']): ?>
                      <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($result['matkul']->nama_matkul ?? '-'); ?></td>
                        <td>
                          <span class="badge <?php echo e($result['status'] == 'Lolos' ? 'bg-success' : 'bg-danger'); ?>">
                            <?php echo e($result['status']); ?>

                          </span>
                        </td>
                        <td>
                          <?php if($result['status'] == 'Lolos' && $result['nilai'] !== '-'): ?>
                            <?php echo e($result['nilai']); ?>

                          <?php else: ?>
                            -
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php
                            $banding = null;
                            $calon_mahasiswa_id = $calon_mahasiswa ? $calon_mahasiswa->id : null;
                            if(isset($result['matkul']->id) && $calon_mahasiswa_id) {
                              $banding = \App\Models\Matkul_score::where('matkul_id', $result['matkul']->id)
                                ->where('calon_mahasiswa_id', $calon_mahasiswa_id)
                                ->where('is_banding', true)
                                ->whereIn('banding_status', ['diterima','ditolak'])
                                ->first();
                            }
                          ?>
                          <?php if($banding && $banding->banding_status == 'diterima'): ?>
                            <span class="badge bg-info text-dark">Diterima</span>
                          <?php elseif($banding && $banding->banding_status == 'ditolak'): ?>
                            <span class="badge bg-warning text-dark">Ditolak</span>
                          <?php else: ?>
                            <!-- kosong jika tidak ada banding atau pending -->
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                      <tr>
                        <td colspan="4" class="text-center">Tidak ada data penilaian yang tersedia.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
                
                <?php
                  // Cek apakah sudah pernah banding untuk salah satu matkul
                  $sudah_banding = false;
                  $calon_mahasiswa_id = $calon_mahasiswa ? $calon_mahasiswa->id : null;
                  if($calon_mahasiswa_id) {
                    foreach($final_results as $result) {
                      if(isset($result['matkul']->id)) {
                        $banding = \App\Models\Matkul_score::where('matkul_id', $result['matkul']->id)
                          ->where('calon_mahasiswa_id', $calon_mahasiswa_id)
                          ->where('is_banding', true)
                          ->first();
                        if($banding) { $sudah_banding = true; break; }
                      }
                    }
                  }
                ?>
                <?php if(!$sudah_banding): ?>
                <form action="<?php echo e(route('user.submit-banding')); ?>" method="POST" class="mt-4">
                  <?php echo csrf_field(); ?>
                  <div class="mb-3">
                    <label for="matkul_ids">Pilih Mata Kuliah yang Ingin Dibanding dan Berikan Keterangan:</label>
                    <div class="row">
                      <?php $__currentLoopData = $final_results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($result['matkul']) && $result['matkul'] && isset($result['matkul']->id)): ?>
                        <div class="col-md-6 mb-3">
                          <div class="form-check">
                            <input class="form-check-input matkul-checkbox" type="checkbox" name="matkul_ids[]" value="<?php echo e($result['matkul']->id); ?>" id="matkul_<?php echo e($result['matkul']->id); ?>">
                            <label class="form-check-label" for="matkul_<?php echo e($result['matkul']->id); ?>">
                              <?php echo e($result['matkul']->nama_matkul); ?>

                            </label>
                          </div>
                          <textarea name="keterangan[<?php echo e($result['matkul']->id); ?>]" class="form-control keterangan-textarea mt-2" rows="2" placeholder="Keterangan banding untuk matkul ini..." style="display:none;"></textarea>
                        </div>
                        <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-warning">Ajukan Banding</button>
                </form>
                <script>
                  document.querySelectorAll('.matkul-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                      var textarea = this.closest('.col-md-6').querySelector('.keterangan-textarea');
                      if (this.checked) {
                        textarea.style.display = 'block';
                        // textarea.required = true;
                      } else {
                        textarea.style.display = 'none';
                        textarea.required = false;
                        textarea.value = '';
                      }
                    });
                  });
                </script>
                <?php else: ?>
                  <div class="alert alert-info mt-4">Anda sudah pernah mengajukan banding. Banding hanya bisa dilakukan satu kali.</div>
                <?php endif; ?>
              <?php elseif($published_at && (empty($final_results) || !is_array($final_results))): ?>
                <div class="alert alert-warning text-center">
                  <h5 class="alert-heading">Hasil Penilaian Belum Lengkap</h5>
                  <p>Nilai telah dipublikasikan tetapi belum ada data penilaian yang tersedia. Mohon hubungi administrator untuk informasi lebih lanjut.</p>
                </div>
              <?php else: ?>
                <div class="alert alert-info text-center">
                  <h5 class="alert-heading">Hasil Penilaian Belum Tersedia</h5>
                  <p>Proses penilaian oleh para asesor masih berlangsung. Mohon periksa kembali halaman ini secara berkala. Anda akan dapat melihat hasil akhir setelah semua penilaian selesai dan dipublikasikan oleh administrator.</p>
                </div>
              <?php endif; ?>

            </div>
          </div>

          
        </div>
      </div>
    </section>


  </main><!-- End #main -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/User/view-nilai.blade.php ENDPATH**/ ?>