<?php
/**
 * Organize routes in a separate file
 */
use App\Controllers\UserController;
use \Core\Route;

Route::get("/", "index");

Route::get("/login", [UserController::class, "login"]);

Route::get("/profile", [UserController::class, "profile"]);