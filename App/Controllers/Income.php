<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Income controller
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Show the Add-Income page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Income/index.html');
    }

}
