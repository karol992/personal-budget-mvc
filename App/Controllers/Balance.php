<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Balance controller
 * PHP version 7.0
 */
class Balance extends \Core\Controller
{

    /**
     * Show the Balance page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Balance/index.html');
    }

}
