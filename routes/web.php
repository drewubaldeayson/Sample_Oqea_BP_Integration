<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/providers/{email}/check','API\Providers@checkEmail')->name('checkEmail');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/appointments', 'API\Appointments@getAppointments');
Route::post('/appointments/new', 'API\Appointments@addRawAppointment');
Route::get('/patients/list','API\Patients@getPatientList');
Route::post('/appointments/cancel','API\Appointments@cancelAppointment');
Route::get('/patients', 'API\Patients@getPatients');
Route::post('/patients', 'API\Patients@addRawPatient');
Route::post('/patients/remove', 'API\Patients@removeRawPatient');
Route::get('/providers','API\Providers@getProviders');
