<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\DataProperties\AddIncome;
use \App\Timer;
use \App\Flash;

/**
 * Income controller
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Render the Add-Income page
	 * @_POST [income_date]
     * @return void
     */
    public function indexAction($args = []) {
		$args['income_cats'] = \App\Models\Data::getUserIncomeCats();
		$args['transaction_date'] = $_POST['income_date'] ?? \App\Timer::getCurrentDate();
        View::renderTemplate('Income/index.html', $args);
    }
	
	/** Add an income to the database
	 * @_POST [income_category, income_value, income_date, income_note]
	 * @return void
	 */
	public function addIncomeAction() {
		$args = [];
		$income = new AddIncome($_POST);
		if ($income->send($income->income_value, $income->income_date, $income->income_note)) {
			Flash::addMessage($income->successMessage);
		} else {
			Flash::addMessage('Operacja nie powiodła się.', 'warning');
		}
		$args['errors'] = $income->errors;
		$this->indexAction($args);
	}
	
	/** Add an income to the database (AJAX)
	 * @_POST [income_category, income_value, income_date, income_note]
	 * @return void
	 */
	public function addIncomeAjaxAction() {
		$income = new AddIncome($_POST);
		$response = [];
		if ($income->send($income->income_value, $income->income_date, $income->income_note)) {
			$response['message']=$income->successMessage;
			$response['success'] = true;
		} else {
			$response['message']='Operacja nie powiodła się. ';
			$response['success'] = false;
			foreach ($income->errors as $error) {
				Flash::addMessage($error, 'warning');
			};
		}
		echo json_encode($response);
	}
}
