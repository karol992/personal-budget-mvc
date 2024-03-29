<?php
/**
 * Front controller
 * PHP version 7.0
 */

ini_set('session.cookie_lifetime', '864000'); //ten days in second

/**
 * Composer
 */
require '../vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
session_start();

/**
 * Routing
 */
$router = new Core\Router();

//Add the routes

$router->add('',['controller'=>'Login','action'=>'new']);
$router->add('login',['controller'=>'Login','action'=>'new']);
$router->add('logout',['controller'=>'Login','action'=>'destroy']);
$router->add('signup',['controller'=>'Signup','action'=>'new']);
$router->add('income',['controller'=>'Income','action'=>'index']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);

