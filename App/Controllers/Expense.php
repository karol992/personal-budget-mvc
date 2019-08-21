<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\DataProperties\AddExpense;
use \App\Timer;
use \App\Flash;

/**
 * Expense controller
 * PHP version 7.0
 */
class Expense extends Authenticated
{

    /**
     * Render the Add-Expense page
	 * @_POST [expense_date]
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
	 * @_POST [expense_value, expense_date, expense_note]
	 * @return void
	 */
	public function addExpenseAction() {
		$args = [];
		$expense = new AddExpense($_POST);
		if ($expense->send($expense->expense_value, $expense->expense_date, $expense->expense_note)) {
			Flash::addMessage($expense->successMessage);
		} else {
			Flash::addMessage('Operacja nie powiodła się.', 'warning');
		}
		$args['errors'] = $expense->errors;
		$this->indexAction($args);
	}
}
