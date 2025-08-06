<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Super_admin_data_controller;
use App\Http\Controllers\User_data_Controller;
use App\Http\Controllers\Admin_data_Controller;
use App\Http\Controllers\Assessor_data_Controller;
use App\Http\Controllers\logincontroller;
use App\Http\Controllers\dashboardcontroller;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/', function () {
    return view('login');
});

//Assessor_controller
Route::middleware('auth')->group(function () {
    Route::get('/form-user/{matkul_id}',[Assessor_data_Controller::class,'form_user'])->name('form-user');
    Route::post('/input_calculate', [Assessor_data_Controller::class, 'input_calculate'])->name('input_calculate');
    Route::post('/nilai-matkul-input', [Assessor_data_Controller::class, 'input_nilai_matkul'])->name('nilai-matkul-input');
    Route::get('/list-name-table',[Assessor_data_Controller::class,'list_name_table'])->name('list-name-table');
    Route::get('/detail-user/{id}', [Assessor_data_Controller::class, 'detail_user'])->name('detail-user');
    Route::get('/profile-view-assessor', [Assessor_data_Controller::class, 'profile_view_assessor'])->name('profile-view-assessor');
    Route::get('/assessor/profile/edit/{id}', [Assessor_data_Controller::class, 'profile_assessor_edit_view'])->name('profile-assessor-edit-view');
    Route::post('/assessor/profile/edit/{id}', [Assessor_data_Controller::class, 'profile_edit_assessor'])->name('profile-edit-assessor');
    Route::get('/assessor/view-transkrip/{filename}', [Assessor_data_Controller::class, 'view_transkrip'])->name('assessor.view-transkrip');
    Route::get('/assessor/view-bukti-alih-jenjang/{filename}', [Assessor_data_Controller::class, 'view_bukti_alih_jenjang'])->name('assessor.view-bukti-alih-jenjang');
    Route::get('/assessor/download-bukti-alih-jenjang/{filename}', [Assessor_data_Controller::class, 'download_bukti_alih_jenjang'])->name('assessor.download-bukti-alih-jenjang');
    Route::post('/handle-matkul-input', [Assessor_data_Controller::class, 'handleMatkulInput'])->name('handle_matkul_input');
    Route::post('/assessor/save-matkul-assessment', [Assessor_data_Controller::class, 'saveMatkulAssessorAssessment'])->name('assessor.save-matkul-assessment');
    Route::post('/assessor/submit-final-assessment/{camaba_id}', [Assessor_data_Controller::class, 'submitFinalAssessment'])->name('assessor.submit-final');
    Route::get('/assessor/matkul/{matkulId}/cpmk', [Assessor_data_Controller::class, 'getCpmkByMatkul'])->name('assessor.get-cpmk-by-matkul');
});

// Super Admin Routes
Route::prefix('super')->middleware(['web', 'auth', 'super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('super.dashboard');
    Route::get('/profile-view', [SuperAdminController::class, 'profileView'])->name('super.profile-view');
    Route::get('/profile/edit/{id}', [SuperAdminController::class, 'profile_edit_admin_view'])->name('super.profile-edit-view');
    Route::post('/profile/edit/{id}', [SuperAdminController::class, 'profile_edit_admin'])->name('super.profile-edit');

    // Account Management
    Route::get('/account-assessor-table', [SuperAdminController::class, 'account_assessor_table'])->name('super.account-assessor-table');
    Route::get('/account-assessor-add', [SuperAdminController::class, 'account_assessor_add'])->name('super.account-assessor-add');
    Route::post('/account-assessor-add-data', [SuperAdminController::class, 'account_assessor_add_data'])->name('super.account-assessor-add-data');

    Route::get('/account-user-table', [SuperAdminController::class, 'account_user_table'])->name('super.account-user-table');
    Route::get('/account-user-add', [SuperAdminController::class, 'account_user_add'])->name('super.account-user-add');
    Route::post('/account-user-add-data', [SuperAdminController::class, 'account_user_add_data'])->name('super.account-user-add-data');

    Route::get('/account-admin-table', [SuperAdminController::class, 'account_admin_table'])->name('super.account-admin-table');
    Route::get('/account-admin-add', [SuperAdminController::class, 'account_admin_add'])->name('super.account-admin-add');
    Route::post('/account-admin-add-data', [SuperAdminController::class, 'account_admin_add_data'])->name('super.account-admin-add-data');

    // Data Management
    Route::get('/data-assessor-table', [SuperAdminController::class, 'data_assessor_table'])->name('super.data-assessor-table');
    Route::get('/data-user-table', [SuperAdminController::class, 'data_user_table'])->name('super.data-user-table');
    
    // Matkul Management
    Route::get('/kelola-matkul-table', [SuperAdminController::class, 'kelola_matkul_table'])->name('super.kelola-matkul-table');
    Route::post('/kelola-matkul-add-data', [SuperAdminController::class, 'kelola_matkul_add_data'])->name('super.kelola-matkul-add-data');
    Route::delete('/kelola-matkul-table/{matkul}', [SuperAdminController::class, 'delete_matkul'])->name('super.delete-matkul');

    // CPMK Management
    Route::get('/kelola-cpmk/{matkul_id}', [SuperAdminController::class, 'kelola_cpmk_table'])->name('super.kelola-cpmk-table');
    Route::get('/kelola-cpmk/create/{matkul_id}', [SuperAdminController::class, 'create_data_cpmk'])->name('super.create-data-cpmk');
    Route::post('/kelola-cpmk/store', [SuperAdminController::class, 'add_data_cpmk'])->name('super.add-data-cpmk');
    Route::delete('/kelola-cpmk/{cpmk}', [SuperAdminController::class, 'delete_cpmk'])->name('super.delete-cpmk');
    
    // User Management
    Route::delete('/data-user-table/{user}', [SuperAdminController::class, 'delete_user'])->name('super.delete-user');

    // Assessor Management
    Route::get('/kelola-assessor-table', [SuperAdminController::class, 'kelola_assessor_table'])->name('super.kelola-assessor-table');
    Route::get('/kelola-assessor-mahasiswa', [SuperAdminController::class, 'kelola_assessor_mahasiswa'])->name('super.kelola-assessor-mahasiswa');
    Route::post('/kelola-assessor-mahasiswa/add', [SuperAdminController::class, 'kelola_assessor_mahasiswa_add'])->name('super.kelola-assessor-mahasiswa-add');
    Route::post('/publish-results/{mahasiswa_id}', [SuperAdminController::class, 'publishResults'])->name('super.publish-results');
    Route::post('/publish-results-banding/{mahasiswa_id}', [SuperAdminController::class, 'publishResultsBanding'])->name('super.publish-results-banding');

    // Super Admin Account Management
    Route::get('/account-super-admin-table', [SuperAdminController::class, 'account_super_admin_table'])->name('super.account-super-admin-table');
    Route::get('/account-super-admin-add', [SuperAdminController::class, 'account_super_admin_add'])->name('super.account-super-admin-add');
    Route::post('/account-super-admin-add-data', [SuperAdminController::class, 'account_super_admin_add_data'])->name('super.account-super-admin-add-data');

    // Periode Management
    Route::get('/kelola-periode', [SuperAdminController::class, 'kelola_periode'])->name('super.kelola-periode');
    Route::post('/add-periode', [SuperAdminController::class, 'add_periode'])->name('super.add-periode');
    Route::post('/activate-periode/{id}', [SuperAdminController::class, 'activate_periode'])->name('super.activate-periode');
    Route::post('/deactivate-periode/{id}', [SuperAdminController::class, 'deactivate_periode'])->name('super.deactivate-periode');
    Route::delete('/delete-periode/{id}', [SuperAdminController::class, 'delete_periode'])->name('super.delete-periode');

    // Edit Matkul
    Route::put('/edit-matkul/{matkul}', [SuperAdminController::class, 'edit_matkul'])->name('super.edit-matkul');

    // Super Admin view assessor's students
    Route::get('/view-assessor-students/{assessor_id}', [SuperAdminController::class, 'view_assessor_student'])->name('super.view-assessor-students');
    Route::get('/view-student-as-assessor/{assessor_id}/{student_id}', [SuperAdminController::class, 'view_student_as_assessor'])->name('super.view-student-as-assessor');

    // Super Admin daftar banding
    Route::get('/daftar-banding', [SuperAdminController::class, 'daftar_banding'])->name('super.daftar-banding');
    Route::post('/proses-banding/{camaba_id}', [SuperAdminController::class, 'proses_banding'])->name('super.proses-banding');

    // Super Admin detail banding mahasiswa
    Route::get('/detail-banding-mahasiswa/{camaba_id}', [SuperAdminController::class, 'detail_banding_mahasiswa'])->name('super.detail-banding-mahasiswa');

    Route::get('/rekap-nilai/{mahasiswa_id}', [SuperAdminController::class, 'rekapNilaiAkhir']);
    Route::post('/ubah-status-rpl/{mahasiswa_id}', [SuperAdminController::class, 'ubahStatusRpl'])->name('super.ubah-status-rpl');
});

// Other routes with middleware
Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('admin/dashboard', [dashboardcontroller::class, 'dashboard']);
        Route::get('/profile-view-admin', [Admin_data_Controller::class, 'profile_view_admin'])->name('profile-view-admin');
        Route::get('/admin/profile/edit/{id}', [Admin_data_Controller::class, 'profile_edit_admin_view'])->name('profile-edit-admin-view');
        Route::post('/admin/profile/edit/{id}', [Admin_data_Controller::class, 'profile_edit_admin'])->name('profile-edit-admin');
        Route::get('/account-assessor-table',[Admin_data_Controller::class, 'account_assessor_table'])->name('account-assessor-table');
        Route::get('/account-assessor-add', [Admin_data_Controller::class, 'account_assessor_add'])->name('account-assessor-add');
        Route::post('/account-assessor-add-data', [Admin_data_Controller::class, 'account_assessor_add_data'])->name('account-assessor-add-data');

        Route::get('/account-user-table', [Admin_data_Controller::class, 'account_user_table'])->name('account-user-table');
        Route::get('/account-user-add', [Admin_data_Controller::class, 'account_user_add'])->name('account-user-add');
        Route::post('/account-user-add-data', [Admin_data_Controller::class, 'account_user_add_data'])->name('account-user-add-data');

        Route::get('/account-admin-table', [Admin_data_Controller::class, 'account_admin_table'])->name('account-admin-table');
        Route::get('/account-admin-add', [Admin_data_Controller::class, 'account_admin_add'])->name('account-admin-add');
        Route::post('/account-admin-add-data', [Admin_data_Controller::class, 'account_admin_add_data'])->name('account-admin-add-data');

        Route::get('/data-assessor-table', [Admin_data_Controller::class, 'data_assessor_table'])->name('data-assessor-table');
        Route::get('/data-user-table', [Admin_data_Controller::class, 'data_user_table'])->name('data-user-table');
        Route::get('/kelola-matkul-table', [Admin_data_Controller::class, 'kelola_matkul_table'])->name('kelola-matkul-table');
        Route::post('/kelola-matkul-add-data', [Admin_data_Controller::class, 'kelola_matkul_add_data'])->name('kelola-matkul-add-data');
        Route::delete('/kelola-matkul-table/{matkul}', [Admin_data_Controller::class, 'delete_matkul'])->name('delete-matkul');

        Route::get('/kelola-cpmk/{matkul_id}', [Admin_data_Controller::class, 'kelola_cpmk_table'])->name('kelola-cpmk-table');
        Route::get('/kelola-cpmk/create/{matkul_id}', [Admin_data_Controller::class, 'create_data_cpmk'])->name('create-data-cpmk');
        Route::post('/kelola-cpmk/store', [Admin_data_Controller::class, 'add_data_cpmk'])->name('add-data-cpmk');
        Route::delete('/kelola-cpmk/{cpmk}', [Admin_data_Controller::class, 'delete_cpmk'])->name('delete-cpmk');
        Route::delete('/data-user-table/{user}', [Admin_data_Controller::class, 'delete_user'])->name('delete-user');

        Route::get('/kelola-assessor-table', [Admin_data_Controller::class, 'kelola_assessor_table'])->name('kelola-assessor-table');
        Route::get('/kelola-assessor-mahasiswa', [Admin_data_Controller::class, 'kelola_assessor_mahasiswa'])->name('kelola-assessor-mahasiswa');
        Route::post('/kelola-assessor-mahasiswa/add', [Admin_data_Controller::class, 'kelola_assessor_mahasiswa_add'])->name('kelola-assessor-mahasiswa-add');
        Route::post('/admin/publish-results/{mahasiswa_id}', [Admin_data_Controller::class, 'publishResults'])->name('admin.publish-results');
        Route::post('/admin/publish-results-banding/{mahasiswa_id}', [Admin_data_Controller::class, 'publishResultsBanding'])->name('admin.publish-results-banding');

        Route::put('/edit-matkul/{matkul}', [Admin_data_Controller::class, 'edit_matkul'])->name('edit-matkul');

        // Admin view assessor's students
        Route::get('/admin/view-assessor-students/{assessor_id}', [Admin_data_Controller::class, 'view_assessor_student'])->name('admin.view-assessor-students');
        Route::get('/admin/view-student-as-assessor/{assessor_id}/{student_id}', [Admin_data_Controller::class, 'view_student_as_assessor'])->name('admin.view-student-as-assessor');
        Route::get('/admin/daftar-banding', [Admin_data_Controller::class, 'daftar_banding'])->name('admin.daftar-banding');
        Route::post('/admin/proses-banding/{id}', [Admin_data_Controller::class, 'proses_banding'])->name('admin.proses-banding');
        Route::get('/admin/detail-banding-mahasiswa/{camaba_id}', [Admin_data_Controller::class, 'detail_banding_mahasiswa'])->name('admin.detail-banding-mahasiswa');
        Route::post('/admin/set-rpl-selesai/{mahasiswa_id}', [Admin_data_Controller::class, 'setRplStatusSelesai'])->name('admin.set-rpl-selesai');
        Route::get('/admin/rekap-nilai/{mahasiswa_id}', [Admin_data_Controller::class, 'rekapNilaiAkhir']);
        Route::post('/admin/ubah-status-rpl/{mahasiswa_id}', [Admin_data_Controller::class, 'ubahStatusRpl'])->name('admin.ubah-status-rpl');
    });

    // Assessor Routes
    Route::middleware(['assessor'])->group(function () {
        Route::get('assessor/dashboard', [dashboardcontroller::class, 'dashboard']);
        Route::get('/profile-view-assessor', [Assessor_data_Controller::class, 'profile_view_assessor'])->name('profile-view-assessor');
        Route::get('/form-user/{matkul_id}',[Assessor_data_Controller::class,'form_user'])->name('form-user');
        Route::post('/input_calculate', [Assessor_data_Controller::class, 'input_calculate'])->name('input_calculate');
        Route::get('/assessor/matkul/{matkulId}/cpmk', [Assessor_data_Controller::class, 'getCpmkByMatkul'])->name('assessor.get-cpmk-by-matkul');
        Route::get('/assessor/download-ijazah/{calon_mahasiswa_id}', [Assessor_data_Controller::class, 'downloadIjazah'])->name('assessor.download-ijazah');
        Route::post('/assessor/store-cpmk-scores', [Assessor_data_Controller::class, 'store_cpmk_scores'])->name('assessor.store-cpmk-scores');
        // Add other assessor routes here
    });

    // Pendaftar Routes
    Route::middleware(['pendaftar'])->group(function () {
        Route::get('user/dashboard', [dashboardcontroller::class, 'dashboard']);
        Route::get('/redirect-eporto', [logincontroller::class, 'redirectEporto'])->name('redirect-eporto');
        Route::get('/profile-view-camaba', [User_data_Controller::class, 'profile_view_camaba'])->name('profile-view-camaba');
        Route::get('/self-assessment',[User_data_Controller::class, 'self_assessment'])->name('self-assessment');
        //User_controller
        Route::get('/ijazah-edit-view/{id}',[User_data_Controller::class, 'ijazah_edit_view'])->name('ijazah-edit-view');
        Route::post('ijazah/edit/{id}', [User_data_Controller::class, 'ijazah_edit'])->name('ijazah_edit');
        Route::get('ijazah/add-view', [User_data_Controller::class, 'ijazah_add_view'])->name('ijazah-add-view');
        Route::post('ijazah/add', [User_data_Controller::class, 'ijazah_add'])->name('ijazah-add');
        Route::get('view-ijazah',[User_data_Controller::class, 'view_ijazah'])->name('view-ijazah');
        Route::get('/download-ijazah/{filename}', [User_data_Controller::class, 'download_ijazah'])->name('download-ijazah');
        Route::get('profile/edit/{id}', [User_data_Controller::class, 'profile_edit_camaba_view'])->name('profile_edit_camaba_view');
        Route::post('profile/edit/{id}', [User_data_Controller::class, 'profile_edit_camaba'])->name('profile_edit_camaba');
        Route::get('/profile-view-camaba',[User_data_Controller::class, 'profile_view_camaba'])->name('profile-view-camaba');
        Route::get('/self-assessment',[User_data_Controller::class, 'self_assessment'])->name('self-assessment');
        Route::post('/add-self-assessment', [User_data_Controller::class, 'add_self_assessment'])->name('add-self-assessment');
        Route::delete('/delete-self-assessment/{id}', [User_data_Controller::class, 'delete_self_assessment'])->name('delete-self-assessment')->middleware(['auth']);
        Route::get('/input-transkrip',[User_data_Controller::class,'input_transkrip'])->name('input-transkrip');
        Route::get('/self-assessment-table',[User_data_Controller::class,'self_assessment_table'])->name('self-assessment-table');
        Route::get('/view-nilai',[User_data_Controller::class,'view_nilai'])->name('view-nilai');
        Route::post('/transkrip-add-data', [User_data_Controller::class, 'transkrip_add_data'])->name('transkrip-add-data');
        Route::post('/store-matkul-transkrip/{transkrip}', [User_data_Controller::class, 'store_matkul_transkrip'])->name('store-matkul-transkrip');
        Route::get('/view-transkrip/{filename}', [User_data_Controller::class, 'view_transkrip'])->name('view-transkrip');
        Route::delete('/delete-transkrip/{id}', [User_data_Controller::class, 'delete_transkrip'])->name('delete-transkrip');     
        Route::get('/matkul-self-assessment', [User_data_Controller::class, 'matkul_self_assessment_view'])->name('matkul-self-assessment-view');
        Route::post('/store-matkul-self-assessment', [User_data_Controller::class, 'store_matkul_self_assessment'])->name('store-matkul-self-assessment');   
        Route::get('/user/matkul/{matkulId}/cpmk', [User_data_Controller::class, 'getCpmkByMatkul'])->name('get-cpmk-by-matkul');
        Route::delete('/user/transkrip/{transkripId}/matkul/{matkulIndex}', [User_data_Controller::class, 'delete_matkul_transkrip'])->name('delete-matkul-transkrip');
        Route::post('/user/submit-banding', [User_data_Controller::class, 'submit_banding'])->name('user.submit-banding');
        Route::post('submit-matkul-self-assessment', [User_data_Controller::class, 'submitMatkulSelfAssessment'])->name('submit-matkul-self-assessment');
        Route::get('/matkul-self-assessment', [User_data_Controller::class, 'matkul_self_assessment_view'])->name('matkul-self-assessment-view');
        Route::post('/matkul-self-assessment', [User_data_Controller::class, 'store_matkul_self_assessment'])->name('store-matkul-self-assessment');
    });
});

// Login routes
Route::middleware(['web'])->group(function () {
Route::get('/login', [logincontroller::class, 'login'])->name('login');
Route::post('/loginproses', [logincontroller::class, 'loginproses'])->name('loginproses');
Route::post('/logout', [logincontroller::class, 'logout'])->name('logout');
Route::get('/register', [logincontroller::class, 'register'])->name('register');
Route::post('/registeruser', [logincontroller::class, 'registeruser'])->name('registeruser');
});

// Bukti Alih Jenjang Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bukti-alih-jenjang', [User_data_Controller::class, 'bukti_alih_jenjang_view'])->name('bukti-alih-jenjang-view');
    Route::post('/bukti-alih-jenjang-add', [User_data_Controller::class, 'bukti_alih_jenjang_add'])->name('bukti-alih-jenjang-add');
    Route::get('/bukti-alih-jenjang-file/{filename}', [User_data_Controller::class, 'bukti_alih_jenjang_view_file'])->name('bukti-alih-jenjang-file');
    Route::delete('/bukti-alih-jenjang-delete/{id}', [User_data_Controller::class, 'bukti_alih_jenjang_delete'])->name('bukti-alih-jenjang-delete');
});

//Middleware



// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/Admin/welcome', function () {
//         return view('Admin.welcome');
//     })->name('Admin.welcome');
// });

// Route::middleware(['auth', 'role:assessor'])->group(function () {
//     Route::get('/Assessor/welcome', function () {
//         return view('Assessor.welcome');
//     })->name('Assessor.welcome');
// });

// Route::middleware(['auth', 'role:pendaftar'])->group(function () {
//     Route::get('/User/welcome', function () {
//         return view('User.welcome');
//     })->name('User.welcome');
// });

// Route::get('/user/matkul-self-assessment', [User_data_Controller::class, 'matkul_self_assessment_view'])->name('matkul-self-assessment-view');
// Route::post('/user/store-matkul-self-assessment', [User_data_Controller::class, 'store_matkul_self_assessment'])->name('store-matkul-self-assessment');

// Export routes
Route::middleware(['auth'])->group(function () {
    Route::get('/export/word-f02/{id}', [ExportController::class, 'exportWordF02'])->name('export-word02');
    Route::get('/export/pdf/{id}', [ExportController::class, 'exportPdf'])->name('export-pdfF02');
    Route::get('/export/pdf-f02/{id}', [ExportController::class, 'exportPdfFromWordF02'])->name('exportPdfFromWordF02');
    Route::get('/export/word-f08/{id}', [ExportController::class, 'exportWordF08'])->name('export-word08');
    Route::get('/export/pdf-f08/{id}', [ExportController::class, 'exportPdfFromWordF08'])->name('exportPdfFromWordF08');
    Route::get('/export/{id}', [ExportController::class, 'exportWord'])->name('export.word');
});

// Forgot Password Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// Untuk admin
Route::middleware(['auth', 'admin'])->get('/admin/matkul/{matkulId}/cpmk', [Admin_data_Controller::class, 'getCpmkByMatkul'])->name('admin.get-cpmk-by-matkul');
// Untuk super admin
Route::middleware(['auth', 'super_admin'])->get('/super/matkul/{matkulId}/cpmk', [SuperAdminController::class, 'getCpmkByMatkul'])->name('super.get-cpmk-by-matkul');



