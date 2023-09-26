<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Appointment;
use App\Mail\AppointmentMail;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/dashboard', 'DashboardController@index');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/', 'FrontendController@index');

Route::get('/new-appointment/{doctorId}/{date}', 'FrontendController@show')->name('create.appointment');

/**
 * Routes that are only accessed by logged in patients
 */
Route::group(['middleware' => ['auth', 'patient']], function () {

    Route::post('/book/appointment', 'FrontendController@store')->name('booking.appointment');
    Route::get('/my-booking', 'FrontendController@myBookings')->name('my.booking');
    Route::get('/user-profile', 'ProfileController@index');
    Route::post('/profile', 'ProfileController@store')->name('profile.store');
    Route::post('/profile-pic', 'ProfileController@profilePic')->name('profile.pic');
    Route::get('/my-prescription', 'FrontendController@myPrescription')->name('my.prescription');
});

/**
 * Routes that are only accessed by the user admin
 */
Route::group(['middleware' => ['auth', 'admin']], function () {
    Route::resource('doctor', 'DoctorController');
    Route::get('/patients', 'PatientListController@index')->name('patient');
    Route::get('/patients/all', 'PatientListController@allTimeAppointment')->name('all.appointments');
    Route::get('/status/update/{id}', 'PatientListController@toggleStatus')->name('update.status');
    Route::resource('department', 'DepartmentController');
});

/**
 * Routes that are only accessed by doctors of the app
 */
Route::group(['middleware' => ['auth', 'doctor']], function () {
    Route::resource('appointment', 'AppointmentController');
    Route::post('/appointment/check', 'AppointmentController@check')->name('appointment.check');
    /* ou: Route::post('\appointment\check', [AppointmentController::class, 'check'])->name('appointment.check'); */
    Route::post('/appointment/update', 'AppointmentController@updateTime')->name('update');
    Route::get('patient-today', 'PrescriptionController@index')->name('patients.today');
    Route::post('/prescription', 'PrescriptionController@store')->name('prescription');
    Route::get('/prescription/{userId}/{date}', 'PrescriptionController@show')->name('prescription.show');
    Route::get('/prescription/prescribed-patients', 'PrescriptionController@patientsFromPrescription')->name('prescribed.patients');
});
