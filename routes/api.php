<?php
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\JobController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Candidate
Route::get("/get-candidates",[UserController::class,"index"]);
Route::post("/add-candidates",[UserController::class,"store"]);
Route::get("/candidate/{email}/edit",[UserController::class,"edit"]);
Route::post("/candidate/login",[UserController::class,"userLogin"]);
Route::put("/candidate/{email}/confirm-email",[UserController::class,"confirmEmail"]);
Route::put("/candidate/{email}/change-pass",[UserController::class,"recoverPass"]);
Route::post("/candidate/update/{id}",[UserController::class,"update"]);
Route::put("/candidate/change-password/{id}",[UserController::class,"userChangePassword"]);
Route::post("/candidate/compare-password/{id}",[UserController::class,"comparePassword"]);
Route::post("/candidate/get-token/{token}",[UserController::class,"getUserToken"]);


// Route::get("/user",[UserController::class,"test"]);


// Company
Route::get("/company",[CompanyController::class,"index"]);
Route::post("/company",[CompanyController::class,"store"]);
Route::get("/company/{email}/edit",[CompanyController::class,"edit"]);
Route::put("/company/{email}/confirm-email",[CompanyController::class,"confirmEmail"]);
Route::put("/company/{email}/change-pass",[CompanyController::class,"update"]);
Route::post("/company/login",[CompanyController::class,"EmployeeLogin"]);
Route::post("/company/get-token/{token}",[CompanyController::class,"getCompanyToken"]);

Route::post("/company/update/{id}",[CompanyController::class,"updateCompanyInfo"]);

Route::put("/company/change-password/{id}",[CompanyController::class,"CompanyChangePassword"]);
Route::post("/company/compare-password/{id}",[CompanyController::class,"comparePassword"]);


Route::post("/company/compare-password/{id}",[CompanyController::class,"comparePassword"]);

// Route::group(['middleware' => 'api'], function ($router) {
//     Route::resource('/categories', CategoryController::class);

// });
// Route::group(['middleware' => 'api'], function ($router) {
//     Route::resource('/categories', CategoriesController::class);
//     Route::resource('/applications', ApplicationController::class);
// });

//job
// Route::get('/home',[JobApiController::class,'index']);


Route::get('/get-openedJob',[JobApiController::class,'getOpenedJob']);

Route::get('/get-detailJob/{id}',[JobApiController::class,'getJobDetails']);





// Application
Route::post('/applications',[ApplicationController::class,"store"]);

// 
Route::get('/get-company', [CompanyController::class, 'selectdata']);

Route::get('/get-company/{id}', function (Request $request, $id) {
    $controller = new CompanyController(); 
    return $controller->getCompany($request, $id);
});

Route::get('/get-applications',[ApplicationController::class,"index"]);

// Route::get('/get-cv/{name}', [ApplicationController::class, 'getApplication']);

// Route::get('/cv/{name}', [ApplicationController::class, 'getCV']);

// Route::get('/applications/job/{job_id}', [ApplicationController::class, 'getApplicationsByJob']);

Route::get("candidate/{email}/apply",[ApplicationController::class,"show"]);


// Route::get("/user",[UserController::class,"test"]);


// jobs id

// Route::get('jobs/{id}', [CompanyController::class, 'getPositionById']);

// Route::get('getCandidateByCompany', [CompanyController::class, 'getCandidateByCompany']);

// Route::delete('/delete-candidate/{id}', [CompanyController::class, 'deleteUsers']);

// Route::get('/posts', [PostController::class, 'index']);
// Route::post('add_posts', [PostController::class, 'store']);
// Route::get('/posts/{id}', [PostController::class, 'show']);
// Route::put('/posts/{id}', [PostController::class, 'update']);
// Route::delete('/posts/{id}', [PostController::class, 'destroy']);


// post 
Route::get('/get-posts', [PostController::class, 'index']);
Route::post('add-posts', [PostController::class, 'store']);
// Route::get('/get-posts/{id}', [PostController::class, 'show']);
// Route::put('/update-posts/{id}', [PostController::class, 'update']);
// Route::delete('/delete-posts/{id}', [PostController::class, 'destroy']);
// Comment

Route::post("/add-comment",[CommentController::class,"store"]);
Route::get("/get-comment/{id}",[CommentController::class,"show"]);


Route::get('/getcompanies', [CompanyController::class, 'getCompanyname']);


// Route::get('/companies/{companyId}/applications', [ApplicationController::class, 'getCompanyApplications']);

// Route::get('/company/applications/{token}', [ApplicationController::class, 'getCompanyApplications']);

// Route::group(['prefix' => 'api'], function () {
//     // Đăng ký tuyến API để lấy danh sách ứng dụng theo công ty
//     // Route::get('applications/{token}', [ApplicationController::class, 'getApplicationsByCompany']);

  
// });

Route::get('/get-applications/{token}', [ApplicationController::class, 'getApplicationByCompany']);

Route::get('/get-jobs/{token}', [CompanyController::class, 'getJobByCompany']);

Route::post('/add-jobs', [JobApiController::class, 'addJob']);


// Route để cập nhật công việc
Route::put('/update-jobs/{id}', [JobApiController::class, 'updateJob']);

// Route để xóa công việc
Route::delete('/delete-jobs/{id}', [JobApiController::class, 'deleteJob']);

// Route::put('/applications/{user_id}/{job_id}/status', [ApplicationController::class, 'updateStatus']);
// Route::delete('/deleteCompany/{id}',[CompanyController::class,'deleteCompany']);


Route::get('/datajobs',[CompanyController::class,'getdatauser']);




Route::get('/getjob', [JobController::class, 'getJob']);
Route::delete('/deletejob/{id}',[JobController::class,'deletejob']);

Route::get('/get-status-job', [JobController::class, 'getstatus']);


Route::put('/update-status/{id}',[JobController::class,'Updatestatus']);


Route::put('/change-status-applications/{user_id}/{job_id}', [ApplicationController::class, 'ChangeStatusApplication']);



Route::get('/count', [UserController::class, 'countEndUser']);

// xóa company bên admin
Route::delete('/companies/{companyId}', [CompanyController::class,'deleteadminCompany']);

// xóa user bên admin
Route::delete('/useradmin/{userId}',[UserController::class,'deleteUsers']);
