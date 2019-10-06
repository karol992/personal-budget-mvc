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
	 * @_POST [expense_category, payment_category, expense_value, expense_date, expense_note]
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
	
	/** Add an income to the database (AJAX)
	 * @_POST [income_category, income_value, income_date, income_note]
	 * @return void
	 */
	public function addExpenseAjaxAction() {
		$expense = new AddExpense($_POST);
		$response = [];
		if ($expense->send($expense->expense_value, $expense->expense_date, $expense->expense_note)) {
			$response['message']=$expense->successMessage;
			$response['success'] = true;
		} else {
			$response['message']='Operacja nie powiodła się. ';
			$response['success'] = false;
			foreach ($expense->errors as $error) {
				Flash::addMessage($error, 'warning');
			};
		}
		echo json_encode($response);
	}
	
	/** Get (AJAX)
	 * @_POST []
	 * @return 
	 */
	public function getPeriodedSumAjaxAction() {
		$response = [];
		$date=$_POST['date'];
		$response['date_error']=Timer::dateValidation($date);
		if (!$response['date_error']) {
			$cat_id = $_POST['cat_id'];
			$period=Timer::customMonthPeriod($date);
			$response['beforeSum'] = number_format(Data::getExpenseSum($cat_id, $period), 2, '.', '');
			$response['afterSum'] = number_format(($response['beforeSum'] + $_POST['amount']), 2, '.', '');
			$limit = Data::getCategoryLimit($cat_id);
			$response['limit_reached'] = (($response['afterSum']>$limit ) ? true : false);
			$response['success'] = true;
		} else {
			$response['success'] = false;
		}
		echo json_encode($response);
	}
}
