<?php

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

// Route to create a new role
Route::post('role', 'JwtAuthenticateController@createRole');
// Route to create a new permission
Route::post('permission', 'JwtAuthenticateController@createPermission');
// Route to assign role to user
Route::post('assign-role', 'JwtAuthenticateController@assignRole');
// Route to attache permission to a role
Route::post('attach-permission', 'JwtAuthenticateController@attachPermission');

//Only for admin (testing in server)
Route::post('getPhpInfo', 'AdminController@getPhpInfo');
Route::post('dbManageForAdmin', 'AdminController@dbManageForAdmin');

// Log Viewer route
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

/*====================| Sign up and login for Admin |====================*/
//login for admin
Route::post('doLoginForAdmin','LoginController@doLoginForAdmin');
Route::post('doLogout','LoginController@doLogout');

/*====================| Sign up and login for Admin |====================*/
//sign for user(Email)
Route::post('signupUser','RegisterController@signupUser');
Route::post('doLoginForSocialUser','RegisterController@doLoginForSocialUser');
Route::post('verifyOTPForRegisterUser','RegisterController@verifyOTPForRegisterUser');

//User login
Route::post('doLoginForUser', 'LoginController@doLoginForUser');

//forgot Password for User
Route::post('forgotPasswordForSendOTP','LoginController@forgotPasswordForSendOTP');
Route::post('resendOTPForUser','LoginController@resendOTPForUser');
Route::post('verifyOTP','LoginController@verifyOTP');
Route::post('newPasswordForUser','LoginController@newPasswordForUser');



Route::group(['prefix' => '', 'middleware' => ['ability:admin,admin_permission']], function () {

    //change password for admin
    Route::post('changePassword','LoginController@changePassword');

    //registeradmin by admin
    Route::post('adminRegisterByAdmin','AdminController@adminRegisterByAdmin');
    Route::post('setAdminStatus','AdminController@setAdminStatus');
    Route::post('updateAdminData','AdminController@updateAdminData');
    Route::post('getAdminData','AdminController@getAdminData');

    // Redis Keys
    Route::post('getRedisKeys', 'AdminController@getRedisKeys');
    Route::post('deleteRedisKeys', 'AdminController@deleteRedisKeys');
    Route::post('getRedisKeyDetail', 'AdminController@getRedisKeyDetail');
    Route::post('clearRedisCache', 'AdminController@clearRedisCache');

    //User For Admin
    Route::post('getAllUserForAdmin', 'AdminController@getAllUserForAdmin');
    Route::post('searchUserForAdmin', 'AdminController@searchUserForAdmin');

    //Round detail
    Route::post('addRoundDetailByAdmin','AdminController@addRoundDetailByAdmin');
    Route::post('updateRoundDetailByAdmin','AdminController@updateRoundDetailByAdmin');
    Route::post('deleteRoundDetailByAdmin','AdminController@deleteRoundDetailByAdmin');
    Route::post('getRoundDetailByAdmin','AdminController@getRoundDetailByAdmin');

    //Question Answer
    Route::post('addQuestionAnswerByAdmin','AdminController@addQuestionAnswerByAdmin');
    Route::post('updateQuestionAnswerByAdmin','AdminController@updateQuestionAnswerByAdmin');
    Route::post('deleteQuestionAnswerByAdmin','AdminController@deleteQuestionAnswerByAdmin');
    Route::post('getQuestionAnswerByAdmin','AdminController@getQuestionAnswerByAdmin');
    Route::post('getQuestionAnswerFromRoundByAdmin','AdminController@getQuestionAnswerFromRoundByAdmin');
    Route::post('addQuestionAnswerFromExcelByAdmin','AdminController@addQuestionAnswerFromExcelByAdmin');

    //FAQ
    Route::post('addFAQByAdmin','AdminController@addFAQByAdmin');
    Route::post('updateFAQByAdmin','AdminController@updateFAQByAdmin');
    Route::post('deleteFAQByAdmin','AdminController@deleteFAQByAdmin');
    Route::post('setStatusOfFAQByAdmin','AdminController@setStatusOfFAQByAdmin');
    Route::post('getFAQByAdmin','AdminController@getFAQByAdmin');

    //Terms and Conditions
    Route::post('addTermsNConditionsByAdmin','AdminController@addTermsNConditionsByAdmin');
    Route::post('updateTermsNConditionsByAdmin','AdminController@updateTermsNConditionsByAdmin');
    Route::post('deleteTermsNConditionsByAdmin','AdminController@deleteTermsNConditionsByAdmin');
    Route::post('setStatusOfTermsNConditionsByAdmin','AdminController@setStatusOfTermsNConditionsByAdmin');
    Route::post('getTermsNConditionsByAdmin','AdminController@getTermsNConditionsByAdmin');

    //Contact detail by admin
    Route::post('getContactDetailByAdmin','AdminController@getContactDetailByAdmin');
    Route::post('replayToContactByAdmin','AdminController@replayToContactByAdmin');
    Route::post('deleteContactByAdmin','AdminController@deleteContactByAdmin');

    //Debit
    Route::post('addDebitByAdmin','AdminController@addDebitByAdmin');
    Route::post('updateDebitByAdmin','AdminController@updateDebitByAdmin');
    Route::post('deleteDebitByAdmin','AdminController@deleteDebitByAdmin');
    Route::post('getDebitByAdmin','AdminController@getDebitByAdmin');

    //Notification
    Route::post('addNotifyByAdmin','AdminController@addNotifyByAdmin');
    Route::post('updateNotifyByAdmin','AdminController@updateNotifyByAdmin');
    Route::post('deleteNotifyByAdmin','AdminController@deleteNotifyByAdmin');
    Route::post('getNotifyByAdmin','AdminController@getNotifyByAdmin');

    //Notification
    Route::post('getKeywordByAdmin','AdminController@getKeywordByAdmin');
    Route::post('addKeywordByAdmin','AdminController@addKeywordByAdmin');
    Route::post('updateKeywordByAdmin','AdminController@updateKeywordByAdmin');
    Route::post('deleteKeywordByAdmin','AdminController@deleteKeywordByAdmin');

    //Expense
    Route::post('getExpenseDetailByAdmin','AdminController@getExpenseDetailByAdmin');
    Route::post('exportExpenseDetailByAdmin','AdminController@exportExpenseDetailByAdmin');
    Route::post('payRSFromByAdmin','AdminController@payRSFromByAdmin');

});

Route::group(['prefix' => '', 'middleware' => ['ability_user:user,user_permission']], function () {

    Route::post('getUserProfileByUser', 'UserController@getUserProfileByUser');

    //Add coin by some task
    Route::post('addCoinBySomeTaskForUser', 'UserController@addCoinBySomeTaskForUser');

    //get round detail
    Route::post('getRoundDetailByUser', 'UserController@getRoundDetailByUser');
    Route::post('getQuestionByRoundForUser', 'UserController@getQuestionByRoundForUser');

    //contact us
    Route::post('contactByUser','UserController@contactByUser');
    Route::post('getContactDetailByUser','UserController@getContactDetailByUser');

    //Coin to pay
    Route::post('requestForCoinsToPay','UserController@requestForCoinsToPay');
});

//get faq detail
Route::post('getFAQByUser','GeneralController@getFAQByUser');
Route::post('getTermsNConditionsByUser','GeneralController@getTermsNConditionsByUser');

Route::post('testMail','AdminController@testMail');
