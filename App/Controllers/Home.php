<?php

namespace App\Controllers;

use \Core\View;
use \App\Flash;
use \App\Auth;

/**
 * Home controller
 * PHP version 7.0
 */
class Home extends Authenticated
{

    /**
     * Show the Home page
     * @return void
     */
    public function indexAction() {
		Flash::addMessage('Witaj '.Auth::getUserName().'!');
        View::renderTemplate('Home/index.html');
    }
}
