<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\DataProperties\ShowBalance;

/**
 * Balance controller
 * PHP version 7.0
 */
class Balance extends Authenticated
{

    /**
     * Show the Balance page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Balance/index.html');
    }

}
