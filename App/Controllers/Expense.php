<?php

namespace App\Controllers;

use \Core\View;

/**
 * Expense controller
 * PHP version 7.0
 */
class Expense extends Authenticated
{

    /**
     * Show the Add-Expense page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Expense/index.html');
    }

}
