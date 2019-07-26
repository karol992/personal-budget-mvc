<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;

/**
 * Expense controller
 * PHP version 7.0
 */
class Expense extends \Core\Controller
{

    /**
     * Show the Add-Expense page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Expense/index.html');
    }

}
