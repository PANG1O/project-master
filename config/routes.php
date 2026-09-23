<?php
use PHPCore\Core\Http\Router;

/**
 * Define your routes in this file.
 *
 * Example:
 * http://localhost:8000/home
 * return [
 *      'home' => ['Homes', 'index']
 * ];
 *
 * Example including Middleware:
 * http://localhost:8000/home
 * return [
 *      'home' => ['Homes', 'index', [
 *              'AuthMiddleWare', 'check'
 *          ]
 *      ]
 * ];
 */

/**
 * Define your routes in this file.
 *
 * Examples:
 *
 * https://localhost:8000/users
 * Router::get('/users', 'Users@index');
 *
 * https://localhost:8000/create-user
 * Router::post('/create-user', 'Users@create');
 *
 * Example incl. Middleware:
 *
 * https://www.localhost:8000/administration
 * Router::get('/administration', 'Administration@index')
 *      ->middleware('AuthMiddleware@isAdministrator');
 */