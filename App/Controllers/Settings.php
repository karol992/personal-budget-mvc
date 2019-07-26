<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Income controller
 * PHP version 7.0
 */
class Settings extends Authenticated
{

    /**
     * Show the Settings page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Settings/index.html');
    }

}
