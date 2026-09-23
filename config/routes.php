<?php
use PHPCore\Core\Http\Router;

# ======================================================================================================================
# USERS
# ======================================================================================================================

/**
 * @uses Users::index()
 */
Router::get('/users', 'Users@index');

/**
 * @uses Users::create()
 */
Router::get('/create-user', 'Users@create');

/**
 * @uses Users::create()
 */
Router::post('/create-user', 'Users@create');

/**
 * @uses Users::show()
 */
Router::get('/user/{id}', 'Users@show');

/**
 * @uses Users::update()
 */
Router::get('/update-user/{id}', 'Users@update');

/**
 * @uses Users::update()
 */
Router::post('/update-user/{id}', 'Users@update');

/**
 * @uses Users::delete()
 */
Router::get('/delete-user/{id}', 'Users@delete');