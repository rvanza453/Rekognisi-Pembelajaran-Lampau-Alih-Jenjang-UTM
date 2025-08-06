<?php $__env->startSection('content'); ?>
<main id="main" class="main">
  <div class="pagetitle">
    <h1>Detail Banding Mahasiswa</h1>
  </div>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Detail Mahasiswa: <?php echo e($camaba->nama); ?></h5>
            <!-- Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#profil" type="button" role="tab" aria-controls="profil" aria-selected="true">Profil</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="ijazah-tab" data-bs-toggle="tab" data-bs-target="#ijazah" type="button" role="tab" aria-controls="ijazah" aria-selected="false">Ijazah</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="transkrip-tab" data-bs-toggle="tab" data-bs-target="#transkrip" type="button" role="tab" aria-controls="transkrip" aria-selected="false">Transkrip</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="bukti-tab" data-bs-toggle="tab" data-bs-target="#bukti" type="button" role="tab" aria-controls="bukti" aria-selected="false">Bukti Alih Jenjang</button>
              </li>
            </ul>
            <div class="tab-content pt-2" id="borderedTabContent">
              <!-- PROFIL -->
              <div class="tab-pane fade show active" id="profil" role="tabpanel" aria-labelledby="profil-tab">
                <h2>Data Diri Mahasiswa</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nama</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->nama ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Prodi</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->jurusan->nama_jurusan ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Alamat</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->alamat ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Email</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->user->email ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">No Wa</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->nomor_telepon ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tempat Lahir</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->tempat_lahir ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tanggal Lahir</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->tanggal_lahir ? \Carbon\Carbon::parse($camaba->tanggal_lahir)->format('d/m/Y') : '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jenis Kelamin</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->kelamin ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kota</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->kota ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Provinsi</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->provinsi ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kode Pos</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->kode_pos ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kebangsaan</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->kebangsaan ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nomor Rumah</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->nomor_rumah ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nomor Kantor</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->nomor_kantor ?? '-'); ?></div></div>
                  </div>
                </div>
              </div>
              <!-- IJAZAH -->
              <div class="tab-pane fade" id="ijazah" role="tabpanel" aria-labelledby="ijazah-tab">
                <h2>Data Ijazah</h2>
                <div class="card">
                  <div class="ijazah-overview-assessor mx-4 my-3">
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Institusi Pendidikan</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->institusi_pendidikan ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jenjang</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->jenjang ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Provinsi</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->provinsi ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Kota</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->kota ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Negara</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->negara ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Fakultas</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->fakultas ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Jurusan</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->jurusan ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Nilai/IPK</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->ipk_nilai ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">Tahun Lulus</div><div class="col-lg-9 col-md-8"><?php echo e($camaba->ijazah->tahun_lulus ?? '-'); ?></div></div>
                    <div class="row mb-3"><div class="col-lg-3 col-md-4 label">File Ijazah</div><div class="col-lg-9 col-md-8">
                      <?php if($camaba->ijazah && ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah)): ?>
                        <a href="<?php echo e(asset('Data/Ijazah/' . ($camaba->ijazah->file ?? $camaba->ijazah->file_ijazah))); ?>" target="_blank" class="btn btn-primary btn-sm">
                          <i class="bi bi-download"></i> Download Ijazah
                        </a>
                      <?php else: ?>
                        <span class="text-muted">File tidak tersedia</span>
                      <?php endif; ?>
                    </div></div>
                  </div>
                </div>
              </div>
              <!-- TRANSKRIP -->
              <div class="tab-pane fade" id="transkrip" role="tabpanel" aria-labelledby="transkrip-tab">
                <h2>Data Transkrip</h2>
                <div class="card">
                  <div class="card-body">
                    <?php if($camaba->transkrip && ($camaba->transkrip instanceof \Illuminate\Support\Collection ? $camaba->transkrip->count() > 0 : true)): ?>
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr><th>No</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th></tr>
                          </thead>
                          <tbody>
                            <?php if($camaba->transkrip instanceof \Illuminate\Support\Collection): ?>
                              <?php $__currentLoopData = $camaba->transkrip; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transkrip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td><?php echo e((int)$index + 1); ?></td><td><?php echo e($transkrip->nama_matkul ?? '-'); ?></td><td><?php echo e($transkrip->sks ?? '-'); ?></td><td><?php echo e($transkrip->nilai ?? '-'); ?></td></tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                              <tr><td>1</td><td><?php echo e($camaba->transkrip->nama_matkul ?? '-'); ?></td><td><?php echo e($camaba->transkrip->sks ?? '-'); ?></td><td><?php echo e($camaba->transkrip->nilai ?? '-'); ?></td></tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                      <div class="mt-3">
                        <strong>File Transkrip:</strong>
                        <?php
                          $fileTranskrip = $camaba->transkrip instanceof \Illuminate\Support\Collection ? ($camaba->transkrip->first()->file_transkrip ?? $camaba->transkrip->first()->file ?? null) : ($camaba->transkrip->file_transkrip ?? $camaba->transkrip->file ?? null);
                        ?>
                        <?php if($fileTranskrip): ?>
                          <a href="<?php echo e(asset('Data/Transkrip/' . $fileTranskrip)); ?>" target="_blank" class="btn btn-primary btn-sm ms-2">
                            <i class="bi bi-download"></i> Download Transkrip
                          </a>
                        <?php else: ?>
                          <span class="text-muted ms-2">File tidak tersedia</span>
                        <?php endif; ?>
                      </div>
                    <?php else: ?>
                      <div class="alert alert-info" role="alert">Data transkrip tidak tersedia.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <!-- BUKTI ALIH JENJANG -->
              <div class="tab-pane fade" id="bukti" role="tabpanel" aria-labelledby="bukti-tab">
                <h2>Bukti Alih Jenjang</h2>
                <div class="card">
                  <div class="card-body">
                    <?php if($camaba->bukti_alih_jenjang && ($camaba->bukti_alih_jenjang instanceof \Illuminate\Support\Collection ? $camaba->bukti_alih_jenjang->count() > 0 : true)): ?>
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead><tr><th>No</th><th>Jenis Bukti</th><th>Deskripsi</th><th>File</th></tr></thead>
                          <tbody>
                            <?php if($camaba->bukti_alih_jenjang instanceof \Illuminate\Support\Collection): ?>
                              <?php $__currentLoopData = $camaba->bukti_alih_jenjang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bukti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td><?php echo e((int)$index + 1); ?></td><td><?php echo e($bukti->jenis_bukti ?? $bukti->jenis_dokumen ?? '-'); ?></td><td><?php echo e($bukti->deskripsi ?? '-'); ?></td><td><?php if($bukti->file_bukti ?? $bukti->file): ?><a href="<?php echo e(asset('Data/Bukti_alih_jenjang/' . ($bukti->file_bukti ?? $bukti->file))); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Download</a><?php else: ?><span class="text-muted">File tidak tersedia</span><?php endif; ?></td></tr>
                              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                              <tr><td>1</td><td><?php echo e($camaba->bukti_alih_jenjang->jenis_bukti ?? $camaba->bukti_alih_jenjang->jenis_dokumen ?? '-'); ?></td><td><?php echo e($camaba->bukti_alih_jenjang->deskripsi ?? '-'); ?></td><td><?php if($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file): ?><a href="<?php echo e(asset('Data/Bukti_alih_jenjang/' . ($camaba->bukti_alih_jenjang->file_bukti ?? $camaba->bukti_alih_jenjang->file))); ?>" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-download"></i> Download</a><?php else: ?><span class="text-muted">File tidak tersedia</span><?php endif; ?></td></tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    <?php else: ?>
                      <div class="alert alert-info" role="alert">Data bukti alih jenjang tidak tersedia.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- Tabel Banding -->
            <div class="card mt-4">
              <div class="card-body">
                <h5 class="card-title">Mata Kuliah yang Diajukan Banding</h5>
                <form action="<?php echo e(route('super.proses-banding', $camaba->id)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Mata Kuliah</th>
                          <th>Keterangan Banding</th>
                          <th>Nilai Akhir</th>
                          <th>Status Banding</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $banding_matkul; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $matkul_id => $banding): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                          <td><?php echo e($loop->iteration); ?></td>
                          <td>
                            <?php echo e($banding->matkul->nama_matkul ?? '-'); ?>

                            <?php if($banding->matkul): ?>
                              <button type="button" class="btn btn-info btn-sm view-cpmk-btn ms-2"
                                data-matkul-id="<?php echo e($banding->matkul->id); ?>"
                                data-matkul-name="<?php echo e($banding->matkul->nama_matkul); ?>">
                                View CPMK
                              </button>
                            <?php endif; ?>
                          </td>
                          <td><?php echo e($banding->banding_keterangan); ?></td>
                          <td>
                            <input type="number" name="nilai_akhir[<?php echo e($matkul_id); ?>]" class="form-control" min="0" max="100" value="<?php echo e($banding->nilai_akhir ?? ''); ?>" >
                          </td>
                          <td>
                            <select name="banding_status[<?php echo e($matkul_id); ?>]" class="form-select" required>
                              <option value="pending" <?php echo e($banding->banding_status=='pending' ? 'selected' : ''); ?>>Pending</option>
                              <option value="diterima" <?php echo e($banding->banding_status=='diterima' ? 'selected' : ''); ?>>Diterima</option>
                              <option value="ditolak" <?php echo e($banding->banding_status=='ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                            </select>
                          </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                          <td colspan="5" class="text-center">Tidak ada matkul yang diajukan banding.</td>
                        </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                  <?php if(count($banding_matkul)): ?>
                  <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Simpan Semua Banding</button>
                  </div>
                  <?php endif; ?>
                </form>
                <?php
                  $ada_banding_selesai = $banding_matkul->whereIn('banding_status', ['diterima','ditolak'])->count() > 0;
                ?>
                <?php if($ada_banding_selesai): ?>
                  <form action="<?php echo e(route('super.publish-results-banding', $camaba->id)); ?>" method="POST" class="mt-3" id="publishBandingForm">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary">Publikasikan Ulang Hasil Setelah Banding</button>
                  </form>
                  <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    var form = document.getElementById('publishBandingForm');
                    if(form) {
                      form.addEventListener('submit', function(e) {
                        if(!confirm('Apakah Anda yakin ingin mempublish hasil banding?')) {
                          e.preventDefault();
                        }
                      });
                    }
                  });
                  </script>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<div class="modal fade" id="cpmkModal" tabindex="-1" aria-labelledby="cpmkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cpmkModalLabel">CPMK Mata Kuliah: <span id="matkulNameInModal"></span></h5>
      </div>
      <div class="modal-body" id="cpmkModalBody">
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Menambahkan logika untuk TAB yang sebelumnya tidak ada di file ini
    const tabButtons = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Hapus kelas 'active' dari semua tombol tab
            tabButtons.forEach(b => b.classList.remove('active'));
            // Tambahkan 'active' ke tombol yang diklik
            this.classList.add('active');
            
            // Logika untuk menampilkan konten tab yang sesuai
            document.querySelectorAll('.tab-content').forEach(tab => {
                if(tab.id === this.getAttribute('data-bs-target').substring(1)) {
                    tab.classList.add('show', 'active');
                } else {
                    tab.classList.remove('show', 'active');
                }
            });
        });
    });

    // Logika untuk MODAL, disamakan dengan file detail-banding yang sudah berfungsi
    var cpmkModal = new bootstrap.Modal(document.getElementById('cpmkModal'));
    var matkulNameInModal = document.getElementById('matkulNameInModal');
    var cpmkModalBody = document.getElementById('cpmkModalBody');

    document.querySelectorAll('.view-cpmk-btn').forEach(button => {
        button.addEventListener('click', function () {
            const matkulId = this.getAttribute('data-matkul-id');
            const matkulName = this.getAttribute('data-matkul-name');

            matkulNameInModal.textContent = matkulName;
            cpmkModalBody.innerHTML = '<div class="text-center">Loading CPMK...</div>';

            fetch(`/super/matkul/${matkulId}/cpmk`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    let html = '';
                    let cpmkList = [];

                    if (Array.isArray(data)) {
                        cpmkList = data;
                    } else if (data.success && Array.isArray(data.cpmks)) {
                        cpmkList = data.cpmks;
                    }

                    if (cpmkList.length > 0) {
                        html += '<div class="table-responsive"><table class="table table-striped">';
                        html += '<thead><tr><th>No</th><th>Kode CPMK</th><th>Penjelasan</th></tr></thead><tbody>';
                        cpmkList.forEach((cpmk, idx) => {
                            html += `<tr><td>${idx + 1}</td><td>${cpmk.kode_cpmk ?? '-'}</td><td>${cpmk.penjelasan ?? '-'}</td></tr>`;
                        });
                        html += '</tbody></table></div>';
                    } else {
                        html = '<div class="alert alert-info">Tidak ada CPMK yang tersedia untuk mata kuliah ini.</div>';
                    }
                    cpmkModalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching CPMK:', error);
                    cpmkModalBody.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data CPMK.</div>';
                });

            cpmkModal.show();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout.super_admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SKRIPSI\Pembuatan Sistem\Deploy Temp\rpl-lintas-jenjang.igsindonesia.org - Tumpuk saja\rpl-lintas-jenjang.igsindonesia.org - percobaan implementasi similarity - Done\resources\views/Super_admin/detail-banding-mahasiswa.blade.php ENDPATH**/ ?>