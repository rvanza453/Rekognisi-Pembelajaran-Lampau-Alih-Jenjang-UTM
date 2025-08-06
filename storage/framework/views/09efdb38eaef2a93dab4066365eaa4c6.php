<?php $__env->startSection('content'); ?>

<main id="main" class="main">

    <div class="pagetitle">
        <h1>Akun Mahasiswa RPL</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item active">Akun Mahasiswa RPL</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title center" align="center">Akun Mahasiswa RPL</h5>
                        <a href="/account-user-add"><button type="button" class="btn btn-primary mb-3 float-end">Tambah</button></a>

                        <!-- Dropdown for selecting Jurusan -->
                        

                        <!-- Default Table -->
                        <div class="table-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Jurusan</th>
                                        <th scope="col">Periode</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $users_camaba; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                            <td><?php echo e($index + 1); ?></td>
                                            <td>
                                                <?php if($user->calon_mahasiswa): ?>
                                                    <?php echo e($user->calon_mahasiswa->nama); ?>

                                                <?php else: ?>
                                                    Data tidak tersedia
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($user->email); ?></td>
                                            <td><?php echo e($user->username); ?></td>
                                            <td>
                                                <?php if($user->calon_mahasiswa && $user->calon_mahasiswa->jurusan): ?>
                                                    <?php echo e($user->calon_mahasiswa->jurusan->nama_jurusan); ?>

                                                <?php else: ?>
                                                    Data tidak tersedia
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($user->calon_mahasiswa && $user->calon_mahasiswa->periode): ?>
                                                    <?php echo e($user->calon_mahasiswa->periode->tahun_ajaran); ?>

                                                <?php else: ?>
                                                    Data tidak tersedia
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <!-- Trigger the modal with a button (trash icon) -->
                                                <i type="button" class="bi-trash fs-3" data-toggle="modal" data-target="#deleteUserModal<?php echo e($user->id); ?>"></i>
                                            </td>
                                        </tr>
                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteUserModal<?php echo e($user->id); ?>" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalTitle<?php echo e($user->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteUserModalTitle<?php echo e($user->id); ?>">Peringatan!</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah kamu yakin ingin menghapus akun <strong><?php echo e($user->calon_mahasiswa ? $user->calon_mahasiswa->nama : 'Pengguna'); ?></strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <form action="<?php echo e(route('delete-user', $user->id)); ?>" method="POST" style="display: inline;">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Admin/account-user-table.blade.php ENDPATH**/ ?>