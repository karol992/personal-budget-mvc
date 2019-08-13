<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\AddExpense;
use \App\Timer;
use \App\Flash;

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
    public function indexAction($args = []) {
		$args['expense_value'] = '0.00';
		$args['expense_cats'] = \App\Models\Data::getUserExpenseCats();
		$args['payment_cats'] = \App\Models\Data::getUserPaymentCats();
		$args['transaction_date'] = $_POST['expense_date'] ?? \App\Timer::getCurrentDate();
        View::renderTemplate('Expense/index.html', $args);
    }

	/** Add an expense to the database
	 * @return void*/
	public function addExpenseAction() {
		$args=[];
		$expense = new AddExpense();
		if (! $expense->send($_POST['expense_value'], $_POST['expense_date'], $_POST['expense_note'])) {
			Flash::addMessage('Operacja nie powiodła się.', 'warning');
		}
		$args['errors'] = $expense->errors;
		$this->indexAction($args);
	}
}
