<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HierarchyController;
use App\Http\Controllers\Admin\UnitTemplateController;
use App\Http\Controllers\Admin\ViewTemplateController;
use App\Http\Controllers\Admin\UnitManagementController;
use App\Http\Controllers\Admin\ViewManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\TerminologyController;
use App\Http\Controllers\Admin\DrugManagementController;
use App\Http\Controllers\Clinical\DashboardController as ClinicalDashboardController;
use App\Http\Controllers\Clinical\PatientController;
use App\Http\Controllers\Clinical\DoctorController;
use App\Http\Controllers\Clinical\NurseController;
use App\Http\Controllers\Clinical\PharmacistController;
use App\Http\Controllers\Clinical\GpDoctorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Authentication
// ---------------------------------------------------------------------------
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ---------------------------------------------------------------------------
// Admin panel
// ---------------------------------------------------------------------------
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Hierarchy management
    Route::resource('hierarchy', HierarchyController::class)->only(['index', 'store', 'update', 'destroy']);

    // Static template lists (read-only)
    Route::get('unit-templates', [UnitTemplateController::class, 'index'])->name('unit-templates.index');
    Route::get('view-templates', [ViewTemplateController::class, 'index'])->name('view-templates.index');

    // Units management
    Route::get('units',            [UnitManagementController::class, 'index'])  ->name('units.index');
    Route::post('units',           [UnitManagementController::class, 'store'])  ->name('units.store');
    Route::patch('units/{unit}',   [UnitManagementController::class, 'update'])  ->name('units.update');
    Route::delete('units/{unit}',  [UnitManagementController::class, 'destroy'])->name('units.destroy');

    // Views management
    Route::get('views',                [ViewManagementController::class, 'index'])  ->name('views.index');
    Route::post('views',               [ViewManagementController::class, 'store'])  ->name('views.store');
    Route::delete('views/{unitView}',  [ViewManagementController::class, 'destroy'])->name('views.destroy');

    // User management — AJAX routes MUST come before the resource to avoid collision
    Route::get('users/units-for-institution/{institution}', [UserManagementController::class, 'unitsForInstitution'])
         ->name('users.units-for-institution');
    Route::get('users/views-for-units', [UserManagementController::class, 'viewsForUnits'])
         ->name('users.views-for-units');
    Route::resource('users', UserManagementController::class)->except(['show']);

    // Terminology management
    Route::get('terminology',                          [TerminologyController::class, 'index'])  ->name('terminology.index');
    Route::post('terminology',                         [TerminologyController::class, 'store'])  ->name('terminology.store');
    Route::delete('terminology/{terminologyTerm}',     [TerminologyController::class, 'destroy'])->name('terminology.destroy');

    // Drugs management — specific routes BEFORE wildcard {drug} / {drugDefault}
    Route::post('drugs/defaults',                      [DrugManagementController::class, 'storeDefault'])   ->name('drugs.defaults.store');
    Route::delete('drugs/defaults/{drugDefault}',      [DrugManagementController::class, 'destroyDefault']) ->name('drugs.defaults.destroy');
    Route::get('drugs',                                [DrugManagementController::class, 'index'])          ->name('drugs.index');
    Route::post('drugs',                               [DrugManagementController::class, 'storeDrug'])      ->name('drugs.store');
    Route::delete('drugs/{drug}',                      [DrugManagementController::class, 'destroyDrug'])    ->name('drugs.destroy');

    // System management
    Route::get('system', [SystemController::class, 'index'])->name('system.index');
});

// ---------------------------------------------------------------------------
// Profile — accessible by all authenticated users
// ---------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile',           [ProfileController::class, 'edit'])           ->name('profile.edit');
    Route::post('/profile',          [ProfileController::class, 'update'])         ->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword']) ->name('profile.password');
});

// ---------------------------------------------------------------------------
// Reports — accessible by all authenticated users
// ---------------------------------------------------------------------------
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/',                                 [ReportController::class, 'index'])                    ->name('index');
    Route::get('/patient-search',                   [ReportController::class, 'patientSearch'])            ->name('patient-search');
    Route::get('/patient/{patient}/letter-data',    [ReportController::class, 'letterData'])               ->name('letter-data');
    Route::post('/clinic-confirmation-letter',      [ReportController::class, 'clinicConfirmationLetter']) ->name('clinic-confirmation-letter');
    Route::get('/monthly-clinic',                   [ReportController::class, 'monthlyClinic'])            ->name('monthly-clinic');
});

// ---------------------------------------------------------------------------
// Terminology search — accessible by all authenticated users (admin + clinical)
// Used for autocomplete in clinical forms
// ---------------------------------------------------------------------------
Route::middleware('auth')->get('/terminology/search', [TerminologyController::class, 'search'])->name('terminology.search');
Route::middleware('auth')->get('/drugs/search',       [DrugManagementController::class, 'search'])          ->name('drugs.search');
Route::middleware('auth')->get('/drug/defaults',      \App\Http\Controllers\Clinical\DrugDefaultsController::class)->name('drug.defaults');

// ---------------------------------------------------------------------------
// Clinical area
// ---------------------------------------------------------------------------
Route::prefix('clinical')->middleware('clinical')->name('clinical.')->group(function () {
    Route::get('/',           [ClinicalDashboardController::class, 'index'])->name('dashboard');

    // Patient management — MUST be before /{unitView} catch-all
    Route::get('/{unitView}/register',               [PatientController::class, 'create'])         ->name('patients.create');
    Route::post('/{unitView}/register',              [PatientController::class, 'store'])          ->name('patients.store');
    Route::post('/{unitView}/add-to-queue/{patient}',[PatientController::class, 'addToQueue'])     ->name('patients.add-to-queue');
    Route::post('/{unitView}/check-duplicate',       [PatientController::class, 'checkDuplicate']) ->name('patients.check-duplicate');
    Route::get('/{unitView}/patients',                   [PatientController::class, 'patientList'])  ->name('patients.list');
    Route::get('/{unitView}/today-queue',                [PatientController::class, 'todayQueue'])   ->name('patients.today-queue');
    Route::get('/{unitView}/gmc-queue',                  [PatientController::class, 'gmcQueue'])     ->name('gmc.queue');
    Route::get('/{unitView}/patients/{patient}/edit',    [PatientController::class, 'edit'])             ->name('patients.edit');
    Route::patch('/{unitView}/patients/{patient}',       [PatientController::class, 'update'])           ->name('patients.update');
    Route::post('/{unitView}/queue/{visit}/remove',      [PatientController::class, 'removeFromQueue'])  ->name('patients.remove-from-queue');
    Route::post('/{unitView}/queue/reset',               [PatientController::class, 'resetQueue'])       ->name('patients.reset-queue');

    // Nurse routes — all before /{unitView} catch-all
    Route::get('/{unitView}/nurse/patients',          [NurseController::class, 'patientList'])   ->name('nurse.patients');
    Route::get('/{unitView}/nurse/patient/{patient}', [NurseController::class, 'patientHistory'])->name('nurse.patient-history');
    Route::get('/{unitView}/nurse/visit/{visit}',     [NurseController::class, 'visitSummary'])  ->name('nurse.visit-summary');

    // Doctor routes — all before /{unitView} catch-all
    Route::get('/{unitView}/doctor/patient-search',                          [DoctorController::class, 'patientSearch']) ->name('doctor.patient-search');
    Route::get('/{unitView}/doctor-queue',                                   [DoctorController::class, 'queuePartial'])  ->name('doctor.queue');
    Route::get('/{unitView}/doctor/patient/{patient}',                       [DoctorController::class, 'patientHistory'])->name('doctor.patient-history');
    Route::post('/{unitView}/doctor/visit/{visit}/start',                    [DoctorController::class, 'startVisit'])    ->name('doctor.start-visit');
    Route::get('/{unitView}/doctor/visit/{visit}',                           [DoctorController::class, 'visitPage'])     ->name('doctor.visit-page');
    Route::post('/{unitView}/doctor/visit/{visit}/end',                      [DoctorController::class, 'endVisit'])      ->name('doctor.end-visit');
    Route::get('/{unitView}/doctor/visit/{visit}/summary',                   [DoctorController::class, 'visitSummary'])  ->name('doctor.visit-summary');
    Route::post('/{unitView}/visit/{visit}/notes',                           [DoctorController::class, 'saveNotes'])        ->name('doctor.save-notes');
    Route::post('/{unitView}/patient/{patient}/allergy',                     [DoctorController::class, 'addAllergy'])       ->name('doctor.allergy.add');
    Route::delete('/{unitView}/patient/{patient}/allergy/{allergy}',         [DoctorController::class, 'removeAllergy'])    ->name('doctor.allergy.remove');
    Route::post('/{unitView}/visit/{visit}/bp',                              [DoctorController::class, 'storeBpReading'])       ->name('doctor.bp.store');
    Route::patch('/{unitView}/bp/{bpReading}',                               [DoctorController::class, 'updateBpReading'])      ->name('doctor.bp.update');
    Route::delete('/{unitView}/bp/{bpReading}',                              [DoctorController::class, 'deleteBpReading'])      ->name('doctor.bp.delete');
    Route::post('/{unitView}/visit/{visit}/investigation',                   [DoctorController::class, 'storeInvestigation'])   ->name('doctor.investigation.store');
    Route::delete('/{unitView}/investigation/{investigation}',               [DoctorController::class, 'deleteInvestigation'])  ->name('doctor.investigation.delete');
    Route::post('/{unitView}/visit/{visit}/drug',                            [DoctorController::class, 'storeDrug'])            ->name('doctor.drug.store');
    Route::patch('/{unitView}/drug/{drug}',                                  [DoctorController::class, 'updateDrug'])           ->name('doctor.drug.update');
    Route::delete('/{unitView}/drug/{drug}',                                 [DoctorController::class, 'deleteDrug'])           ->name('doctor.drug.delete');
    Route::get('/{unitView}/drug-stock-check',                               [DoctorController::class, 'drugStockCheck'])       ->name('doctor.drug-stock-check');

    // Pharmacist routes — all before /{unitView} catch-all
    Route::get('/{unitView}/pharmacist/queue',                    [PharmacistController::class, 'queue'])          ->name('pharmacist.queue');
    Route::get('/{unitView}/pharmacist/search',                   [PharmacistController::class, 'searchPatient'])  ->name('pharmacist.search');
    Route::get('/{unitView}/pharmacist/alerts',                   [PharmacistController::class, 'stockAlerts'])    ->name('pharmacist.alerts');
    Route::get('/{unitView}/pharmacist/visit/{visit}',            [PharmacistController::class, 'visitDetail'])    ->name('pharmacist.visit-detail');
    Route::post('/{unitView}/pharmacist/visit/{visit}/dispense',  [PharmacistController::class, 'dispense'])       ->name('pharmacist.dispense');
    Route::get('/{unitView}/pharmacist/stock',                    [PharmacistController::class, 'stockIndex'])     ->name('pharmacist.stock.index');
    Route::post('/{unitView}/pharmacist/stock',                   [PharmacistController::class, 'stockStore'])     ->name('pharmacist.stock.store');
    Route::patch('/{unitView}/pharmacist/stock/{stock}/oos',      [PharmacistController::class, 'stockToggleOos'])->name('pharmacist.stock.toggle-oos');
    Route::patch('/{unitView}/pharmacist/stock/{stock}/restock', [PharmacistController::class, 'stockRestock'])  ->name('pharmacist.stock.restock');
    Route::delete('/{unitView}/pharmacist/stock/{stock}',         [PharmacistController::class, 'stockDestroy'])   ->name('pharmacist.stock.destroy');
    Route::get('/{unitView}/pharmacist/log',                      [PharmacistController::class, 'stockLog'])        ->name('pharmacist.log');

    // GP Doctor / MO observer routes
    Route::get('/{unitView}/gp-doctor/summary',    [GpDoctorController::class, 'summary'])    ->name('gp-doctor.summary');
    Route::get('/{unitView}/gp-doctor/stock',      [GpDoctorController::class, 'stock'])      ->name('gp-doctor.stock');
    Route::get('/{unitView}/gp-doctor/dispensing', [GpDoctorController::class, 'dispensing']) ->name('gp-doctor.dispensing');
    Route::get('/{unitView}/gp-doctor/log',        [GpDoctorController::class, 'log'])        ->name('gp-doctor.log');

    Route::get('/{unitView}', [ClinicalDashboardController::class, 'show']) ->name('show');
});
