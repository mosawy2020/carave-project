<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
/// });
/*..............Articles...........................*/
// Route::get('/articles',[ArticleController::class,'get_all']);
Route::get('/articles','ArticleController@get_all');
Route::get('/getarticle','ArticleController@get_article_by_id');

// Route::get('/articles/{id}',[ArticleController::class,'get_article_by_id']);

Route::put('/article/update/{id}',[ArticleController::class,'update_article']);
Route::delete('/article/delete/{id}',[ArticleController::class,'destroy_article']);
Route::get('/index','signupcontroller@index');

Route::post('/login','mocontroller@login');

Route::get('/doctorrsdata','insidecontroller@doctorrsdata');
Route::post('/logout','mocontroller@logout');

Route::get('/getdocbyid','insidecontroller@getdocbyid');  

Route::get('/getdocclinics','insidecontroller@getdocclinics'); 

Route::get('sendbasicemail','MailController@basic_email');


Route::group([

    'middleware' => 'auth:api',
   // 'prefix' => 'auth'

], function ($router) {
Route::post('paypal', 'paypalcontroller@index')->name('paypal_call');
Route::get('paypal/return', 'paypalcontroller@paypalReturn')->name('paypal_return');
Route::get('paypal/cancel', 'paypalcontroller@paypalCancel')->name('paypal_cancel');
Route::post('/rateadoctor','insidecontroller@rateadoctor'); 
Route::post('/getallrecords','insidecontroller@getallrecords');
Route::post('/getallappointments','insidecontroller@getallappointments');
Route::post('/editrecord','insidecontroller@editrecord'); 
Route::post('/addappointment','insidecontroller@addappointment');
Route::post('/removeappointment','insidecontroller@removeappointment');
Route::post('/addrecord','insidecontroller@addrecord');
Route::post('/removerecord','insidecontroller@removerecord');
Route::post('/patientupdateprofile','signupcontroller@patientupdateprofile'); 
Route::post('/patientsignup','signupcontroller@patientsignup');

});
Route::group([

    'middleware' => 'auth:doctor',
   // 'prefix' => 'auth'

], function ($router) {
    Route::post('/updateclinic','insidecontroller@updateclinic'); 
    Route::post('/deleteclinic','insidecontroller@deleteclinic'); 
    Route::post('/getalldocappointments','insidecontroller@getalldocappointments'); 
    Route::post('/getpatientbyid','insidecontroller@getpatientbyid'); 
    Route::post('/clinics','insidecontroller@clinics'); 
    Route::post('/doctorupdateprofile','signupcontroller@doctorupdateprofile');
    Route::post('/docsignup','signupcontroller@docsignup');
    Route::post('/article/write','ArticleController@write_article');

});

Route::get('allConversation/{id}','MessageController@getAllConversation');


Route::middleware('auth:api,doctor')->group(function (){

    Route::get('fetchUsers',[MessageController::class,'fetchUsers'] );
    Route::get('fetchMessages/{id}',[MessageController::class,'fetchMessages'] );
    Route::post('sendMessage','MessageController@store');
    Route::get('allConversationsForCurrentUser','MessageController@allConversationForDetectUser' );
    Route::get('privateMessage/{user}/{friend}', [MessageController::class,'privateMessage']);

});
