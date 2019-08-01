<?php
use GuzzleHttp\Psr7\Request;

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
    Log::channel('logInfo')->info('Был заход на сайт!');
    return view('home');
});

Route::get('/create', function (){
    return view('create_table_form');
})->name('seeFormContacts');

Route::post('/create','emailController@newEmailTable' )->name('createTableContacts');

Route::get('/new_mailing', 'emailController@getmailingPage')->name('new_mailing');

Route::get('/FAQ', function(){
    return view('FAQ');
})->name('FAQ');

Route::post('/send_mail', 'emailController@sendMail')->name('sendMail');

Route::get('/storage1/template/{template_name}','emailController@getTemplate')->name('seeTemplate') ;



