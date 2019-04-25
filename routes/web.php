<?php
Route::resource('admin/users', 'Admin\UserController');
Route::resource('admin/plans', 'Admin\PlanController');

//datatables
Route::resource('datatable/plans', 'DataTable\PlanController');
Route::resource('datatable/users', 'DataTable\UserController');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
