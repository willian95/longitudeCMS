<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;


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
})->middleware("guest")->name("login");

Route::post("login", [AuthController::class, "login"]);
Route::get("logout", [AuthController::class, "logout"])->name("logout");

Route::get('/home', function () {
    return view('dashboard');
})->middleware("auth");

Route::post("/upload/file", [FileController::class, "upload"]);

Route::view("project/create", "projects.create.index")->name("project.create")->middleware("auth");
Route::view("project/list", "projects.list.index")->name("project.list")->middleware("auth");
Route::post("/project/store", [ProjectController::class, "store"])->name("project.store");
Route::get("/project/edit/{id}", [ProjectController::class, "edit"])->name("project.edit");
Route::post("/project/update", [ProjectController::class, "update"])->name("project.update");
Route::get("/project/fetch", [ProjectController::class, "fetch"])->name("project.fetch");
Route::post("/project/delete", [ProjectController::class, "delete"])->name("project.delete");

Route::view("services/list", "services.list.index")->name("services.list")->middleware("auth");
Route::get("/services/fetch", [ServiceController::class, "fetch"])->name("services.fetch");
Route::get("/services/edit/{id}", [ServiceController::class, "edit"])->name("services.edit");
Route::post("/services/update", [ServiceController::class, "update"])->name("services.update");



