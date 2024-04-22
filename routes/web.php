<?php

use Illuminate\Console\Scheduling\Event;
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
// Route::get('solter/live', function(){
//     return "aqui aadmin/list/configqui";
// });

//Route::get('inactive/user', 'Auth\InsactiveUserController@index')->name('inactive.user');
Route::get('inactive/user', 'HomeController@inactive')->name('inactive.user');
Route::get('auth/logout', 'Auth\LoginController@logout');
Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'HomeController@index');
    Route::post('/notification', 'HomeController@getNotification');
    Route::get('/notification', 'HomeController@indexNotification')->name('notification.list');
    Route::prefix('helper')->group(function () {
        Route::get('get_functions', 'Helper\SelectController@getFunctions')->name('helper.locals');
        Route::get('get_locals', 'Helper\SelectController@getLocals')->name('helper.locals');
        Route::get('get_sectors', 'Helper\SelectController@getSectors')->name('helper.sectors');
        Route::get('get_users', 'Helper\SelectController@getUsers')->name('helper.sectors');
        Route::get('get_occurrences', 'Helper\SelectController@getOccurrences')->name('helper.sectors');
    });

    Route::prefix('admin')->group(function () {
        
        //user
        Route::get('list/user', 'Admin\UserController@index')->name('list.users');
        Route::get('new/user', 'Admin\UserController@create')->name('new.users');
        Route::post('new/user/create', 'Admin\UserController@store');
        Route::get('edit/user/{id}', 'Admin\UserController@edit')->name('edit.users');
        Route::post('user/update', 'Admin\UserController@update');

        //accesscontrolllist
        Route::post('user/update/image', 'Admin\UserController@updateImage')->name('user.updateImage');
        Route::get('edit/user/password', 'Admin\UserController@editPassword')->name('user.editPassword');
        Route::get('update/user/password', 'Admin\UserController@updatePassword')->name('user.updatePassword');

        Route::get('user/delete/{id}', 'Admin\UserController@destroy')->name('delete.users');
        Route::get('view/profile', 'Admin\UserController@profile')->name('view.profile');
        
        //profile
        Route::get('list/profile', 'Admin\ProfileController@index')->name('list.profile');
        Route::get('new/profile', 'Admin\ProfileController@create')->name('new.profile');
        Route::post('new/profile/create', 'Admin\ProfileController@store');
        Route::get('edit/profile/{id}', 'Admin\ProfileController@edit')->name('edit.profile');
        Route::post('new/profile/update', 'Admin\ProfileController@update');
        Route::get('profile/destroy/{id}', 'Admin\ProfileController@destroy')->name('destroy.profile');

        //profile permission
        Route::get('list/permission/{id}', 'Admin\PermissionController@index')->name('list.permission');
        Route::post('permission/create/{id}', 'Admin\PermissionController@create')->name('new.permission');
        Route::get('permission/remove/{id}', 'Admin\PermissionController@destroy')->name('permission.remove');
        
        //configuracoes
        Route::get('config', 'Admin\ConfigController@index')->name('config');
        Route::post('config/forms/{ConfigForm}', 'Admin\ConfigController@updateConfigForm')->name('config.save');
        
    
    });

    Route::prefix('register')->group(function () {
        Route::resource('sector', 'Register\SectorController');
        Route::resource('local', 'Register\LocalController');
        Route::resource('function', 'Register\FunctionController');
        Route::get('procedure/download/{procedureFiles}', 'Register\ProcedureController@download')->name('procedure.download');
        Route::post('procedure/upload/{procedure}', 'Register\ProcedureController@attachFile')->name('procedure.attach');
        Route::get('procedure/files/{procedure}', 'Register\ProcedureController@filesProcedure')->name('procedure.files');
        Route::delete('procedure/files/{procedureFiles}', 'Register\ProcedureController@deleteFilesProcedure')->name('procedure.files.delete');
        Route::resource('procedure', 'Register\ProcedureController');
    });

    Route::prefix('occurrence')->group(function () {
        Route::get('list/occurrence', 'Occurrence\OccurrenceController@index')->name('occurrence.list');
        Route::get('list/create', 'Occurrence\OccurrenceController@create')->name('occurrence.create');
        Route::post('occurrence/store', 'Occurrence\OccurrenceController@store');
        Route::get('list/edit/download_file/{occurrence}', 'Occurrence\OccurrenceController@downloadFile')->name('occurrence.edit.download_file');
        Route::get('list/edit/{id}', 'Occurrence\OccurrenceController@edit')->name('occurrence.edit');
        Route::post('occurrence/update', 'Occurrence\OccurrenceController@update');
        Route::get('list/view/{id}', 'Occurrence\OccurrenceController@show')->name('occurrence.view');
        Route::get('list/destroy/{id}', 'Occurrence\OccurrenceController@destroy')->name('occurrence.delete');
        Route::get('get/occurrence', 'Occurrence\OccurrenceController@getOccurrence');
        Route::get('get/export_pdf/{name}', 'Occurrence\OccurrenceController@exportPdf');
    });

    Route::prefix('event')->group(function () {
        //meeting
        Route::get('list/meeting', 'Event\Meeting\MeetingController@index')->name('meeting.list');
        Route::get('meeting/create', 'Event\Meeting\MeetingController@create')->name('meeting.create');
        Route::post('meeting/store', 'Event\Meeting\MeetingController@store');
        Route::get('meeting/edit/{id}', 'Event\Meeting\MeetingController@edit')->name('meeting.edit');
        Route::post('meeting/update', 'Event\Meeting\MeetingController@update');
        Route::get('meeting/view/{id}', 'Event\Meeting\MeetingController@show')->name('meeting.view');
        Route::get('meeting/destroy/{id}', 'Event\Meeting\MeetingController@destroy')->name('meeting.delete');
        Route::post('meeting/start_meeting/{meeting}', 'Event\Meeting\MeetingController@startMeeting')->name('meeting.start_meeting');
        Route::get('meeting/export_pdf/{meeting}', 'Event\Meeting\MeetingController@exportPdfMeeting')->name('meeting.export_pdf_meeting');

        Route::get('meeting/downlaod/{id}', 'Event\Meeting\MeetingController@file_download')->name('meeting.downlaod.file');

        Route::get('meeting/getUsersRegistered/{id}', 'Event\Meeting\MeetingController@getUserRegistered');
        Route::get('meeting/getInvitedUsers/{id}', 'Event\Meeting\MeetingController@getInvitedUsers');

        Route::post('meeting/store/participants', 'Event\Meeting\MeetingController@store_participants');

        //shiftReport
        Route::get('list/shiftreport', 'Event\ShiftReport\ShifitReportController@index')->name('shiftreport.list');
        Route::get('shiftreport/create', 'Event\ShiftReport\ShifitReportController@create')->name('shiftreport.create');
        Route::post('shiftreport/store', 'Event\ShiftReport\ShifitReportController@store');
        Route::get('shiftreport/edit/{id}', 'Event\ShiftReport\ShifitReportController@edit')->name('shiftreport.edit');
        Route::get('shiftreport/view/{id}', 'Event\ShiftReport\ShifitReportController@show')->name('shiftreport.view');
        Route::post('shiftreport/update', 'Event\ShiftReport\ShifitReportController@update');
        Route::get('shiftreport/delete/{id}', 'Event\ShiftReport\ShifitReportController@destroy')->name('shiftreport.delete');
        Route::get('shiftreport/tested/{id}', 'Event\ShiftReport\ShifitReportController@tested');
        Route::get('shiftreport/tested/remove/{id}', 'Event\ShiftReport\ShifitReportController@testedRemove');

        Route::resource('check_suite','Event\CheckSuites\CheckSuitesController');    
        Route::resource('inspection_suite','Event\InspectionSuites\InspectionSuiteController');    
        Route::resource('work_diary','Event\WorkDiary\WorkDiaryController');    
        Route::get('work_diary/download_activity/{id}','Event\WorkDiary\WorkDiaryController@downloadActivity');    
        Route::get('work_diary/export_pdf/{id}/{name}', 'Event\WorkDiary\WorkDiaryController@exportPdf')->name('work_diary_export_pdf');

    });
});
