<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;

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

	/** Add an income to the database
	 * @return void*/
	public function addExpenseAction() {
		Data::addExpense();
		$this->indexAction();
	}
}
