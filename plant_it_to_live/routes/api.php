<?php

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DiseasesDetectionController;
use App\Http\Controllers\CroprecommendationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('unauthenticated',function()
{
    return response()->json(
        [
            'sucess'=>false,
            'message'=>'Unauthenticated',
        ],401);
})->name('unauthenticated');

Route::group(['prefix'=>'admin'],function()
{
    //***********************normal admin actions ********************************************
    Route::post('login',[AdminController::class,'login']);//log admin
    Route::get('home',[AdminController::class,'home']);//return admin data
    Route::get('users',[AdminController::class,'users']);//get all users
    Route::post('edit',[AdminController::class,'edit']);//edit admin name and email
    Route::post('delete_user',[AdminController::class,'delete_user']);
    Route::post('changepassword',[AdminController::class,'changepassword']);//change admin password using old password
    Route::get('forgetpassword', [AdminController::class, 'forgetpassword']);//send email to admin with url for reset password form
    Route::post('resetpassword', [AdminController::class, 'resetpassword'])->name('adminresetpassword');//reset password form
    Route::get('logout',[AdminController::class,'logout']);//logout admin
    //Route::get('delete_user',[AdminController::class,'delete_user']);//logout admin

    /***************************************************************************************************/
    /*************************************Admin actions on the plant ************************************************************/
    Route::post('addplant',[AdminController::class,'addplant']);//add new plant to the user
    Route::get('plants',[AdminController::class,'plants']);//get all the plants
    Route::get('plant',[AdminController::class,'plant']);//get one plant
    Route::post('editplant',[AdminController::class,'editplant']);//edit plant
    Route::get('deleteplant',[AdminController::class,'deleteplant']);//delete plant
    /***********************************print method **************************************************/
    Route::get('export', [AdminController::class, 'export']);
   // Route::get('/download/{fileName}', [AdminController::class,'download']);
    /****************************************************************************************************/
    /******************************************Suggestion************************************************/
    // view all Suggestions
    Route::get('allsuggestions',[AdminController::class,'allsuggestions']);
    // view the Suggestion
    Route::get('suggestion',[AdminController::class,'suggestion']);
    //add to plants table
    Route::get('acceptsuggestion',[AdminController::class,'acceptsuggestion']);
    //edit the Suggestion
    Route::post('editsuggestion',[AdminController::class,'editsuggestion']);
    //delete the Suggestion
    Route::get('deletesuggestion',[AdminController::class,'deletesuggestion']);
    //print the Suggestions
    Route::get('exportsuggest', [AdminController::class, 'exportsuggest']);
   // Route::get('/downloadsuggest/{fileName}', [AdminController::class,'downloadsuggest']);
    /***************************************************************************************************/

});
Route::group(['prefix'=>'/'],function()
{
    //*********************** normal user actions ********************************************
    Route::post('login',[UserController::class,'login']);//login user
    Route::post('signup',[UserController::class,'signup']);//signup user
    Route::post('edit',[UserController::class,'edit']);//edit_user_name and email
    Route::get('delete',[UserController::class,'delete']);//delete user
    Route::get('user',[UserController::class,'user']);//return the user data
    Route::get('logout',[UserController::class,'logout']);//logout the user
    //------------------------------------------------------Google---------------------------------------------------------------//
    Route::get('activate', [UserController::class, 'activate'])->name('activate');//active user account
    Route::get('forgetpassword', [UserController::class, 'forgetpassword']);//send email to user with url for reset password form
    Route::post('resetpassword', [UserController::class, 'resetpassword'])->name('resetpassword');//reset password form
    Route::post('changepassword',[UserController::class,'changepassword']);//change admin password using old password
    /*******************************************************************************************************************************/
//--------------------------------------------AI Integration-----------------------------------------------------------//
    Route::Post('sendRequestToCropRecommendation',[CroprecommendationController::class,'sendRequestToCropRecommendation']);
    Route::Post('sendRequestToDiseasesDetection',[DiseasesDetectionController::class,'sendRequestToDiseasesDetection']);
    /***************************************************************************************************************************/
    //all plants
    Route::get('allplants',[Usercontroller::class,'allplants']);
    //singel plant
    Route::get('plant',[Usercontroller::class,'getplant']);
    //select plant
    Route::post('selectplant',[Usercontroller::class,'selectplant']);
    //view all user plants
    Route::get('userplants',[Usercontroller::class,'userplants']);
    //remove plant from user plants
    Route::post('removeplant',[Usercontroller::class,'removeplant']);
    /*************************************************Suggestion*******************************************************************/
    //Add new Suggestion
    Route::post('addsuggestion',[Usercontroller::class,'addsuggestion']);
    //Get All user Suggestion
    Route::get('usersuggestions',[Usercontroller::class,'usersuggestions']);
    /******************************************************************************************************************************/
});

