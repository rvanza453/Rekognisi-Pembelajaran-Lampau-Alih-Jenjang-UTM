
<?php $__env->startSection('content'); ?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>List Mata Kuliah</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
        <li class="breadcrumb-item active">List Mata Kuliah</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->
  <section class="section">
    <div class="row">

      <div class="col-lg-12">


        <div class="card">
          <div class="card-body">
            <h5 class="card-title center" align="center">List Mata Kuliah</h5>
            <button class="btn btn-primary mb-3 float-end" data-toggle="modal"
              data-target="#tambahModal">Tambah</button>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Ada kesalahan validasi:<br><br>
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Dropdown for selecting Jurusan -->
            
            <!-- Default Table -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th width="50">No</th>
                  <th width="300">Mata Kuliah</th>
                  <th width="150">Kode Matkul</th>
                  <th width="100">SKS</th>
                  <th width="100">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $matkuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $matkul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr data-jurusan-id="<?php echo e($matkul->jurusan_id ?? ''); ?>">
                      <td><?php echo e($index + 1); ?></td>
                      <td>
                        <?php if($matkul->nama_matkul): ?>
                            <?php echo e($matkul->nama_matkul); ?>

                        <?php else: ?>
                            Data tidak tersedia
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if($matkul->kode_matkul): ?>
                            <?php echo e($matkul->kode_matkul); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if($matkul->sks): ?>
                            <?php echo e($matkul->sks); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                      </td>
                      <td>
                            <!-- Edit button -->
                            <i type="button" class="bi-pencil-square fs-3 me-2" data-toggle="modal" data-target="#editMatkulModal<?php echo e($matkul->id); ?>"></i>
                            
                            <!-- Trigger the modal with a button (trash icon) -->
                            <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteMatkulModal<?php echo e($matkul->id); ?>"></i>

                            <!-- Link to manage CPMK -->
                            <a type="button" href="<?php echo e(route('kelola-cpmk-table', $matkul->id)); ?>" class="bi-box-arrow-in-right fs-2"></a>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editMatkulModal<?php echo e($matkul->id); ?>" tabindex="-1" role="dialog" aria-labelledby="editMatkulModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editMatkulModalTitle">Edit Mata Kuliah</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="<?php echo e(route('edit-matkul', $matkul->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div class="form-group mb-3">
                                                    <label for="nama_matkul">Nama Matkul</label>
                                                    <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" value="<?php echo e($matkul->nama_matkul); ?>" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="kode_matkul">Kode Matkul</label>
                                                    <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" value="<?php echo e($matkul->kode_matkul); ?>">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="sks">SKS</label>
                                                    <input type="number" class="form-control" id="sks" name="sks" value="<?php echo e($matkul->sks); ?>" min="1" max="6">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteMatkulModal<?php echo e($matkul->id); ?>" tabindex="-1" role="dialog" aria-labelledby="deleteMatkulModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteMatkulModalTitle">Peringatan!</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            Apakah kamu yakin ingin menghapus mata kuliah <strong><?php echo e($matkul->nama_matkul); ?></strong>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <form action="<?php echo e(route('delete-matkul', $matkul->id)); ?>" method="POST" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
              </tbody>
            </table>
            <!-- End Default Table Example -->

            <!-- Modal -->
            <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Mata Kuliah</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <form id="tambahForm" action="<?php echo e(route('kelola-matkul-add-data')); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                          <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </ul>
                        </div>
                      <?php endif; ?>
                      <div class="col-12 mb-3">
                        <label for="nama_matkul" class="form-label">Nama Matkul</label>
                        <input type="text" name="nama_matkul" class="form-control <?php $__errorArgs = ['nama_matkul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="nama_matkul" value="<?php echo e(old('nama_matkul')); ?>" required>
                        <?php $__errorArgs = ['nama_matkul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                      <div class="col-12 mb-3">
                        <label for="kode_matkul" class="form-label">Kode Matkul</label>
                        <input type="text" name="kode_matkul" class="form-control <?php $__errorArgs = ['kode_matkul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="kode_matkul" value="<?php echo e(old('kode_matkul')); ?>">
                        <?php $__errorArgs = ['kode_matkul'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                      <div class="col-12 mb-3">
                        <label for="sks" class="form-label">SKS</label>
                        <input type="number" name="sks" class="form-control <?php $__errorArgs = ['sks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="sks" value="<?php echo e(old('sks')); ?>" min="1" max="6">
                        <?php $__errorArgs = ['sks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                          <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                      </div>
                      <button type="submit" class="btn btn-primary float-end me-3">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>
    </div>
  </section>


</main><!-- End #main -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Admin/kelola-matkul-table.blade.php ENDPATH**/ ?>