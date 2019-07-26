<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

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
        View::renderTemplate('Home/index.html');
    }
}
