<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionMedicationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MedicalRecordController;

Route::apiResources([
    'roles' => RoleController::class,
    'users' => UserController::class,
    'patients' => PatientController::class,
    'doctors' => DoctorController::class,
    'staffs' => StaffController::class,
    'departments' => DepartmentController::class,
    'schedules' => ScheduleController::class,
    'appointments' => AppointmentController::class,
    'medications' => MedicationController::class,
    'prescriptions' => PrescriptionController::class,
    'prescription-medications' => PrescriptionMedicationController::class,
    'invoices' => InvoiceController::class,
    'payments' => PaymentController::class,
    'medical-records' => MedicalRecordController::class,
]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/roles', [RoleController::class, 'index']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/verify-email', [UserController::class, 'verifyEmail']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/users', [UserController::class, 'user']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/roles', [UserController::class, 'getRoles']); 

Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);
    Route::post('/', [PatientController::class, 'store']);
    Route::put('/{id}', [PatientController::class, 'update']);
    Route::delete('/{id}', [PatientController::class, 'destroy']);
});
Route::get('/patients/{id}/details', [PatientController::class, 'showWithMedicalRecords']);
Route::get('/doctors', [DoctorController::class, 'index']);
Route::get('/appointments/by-patient/{patientId}', [AppointmentController::class, 'getByPatient']);
Route::apiResource('medical-records', MedicalRecordController::class);