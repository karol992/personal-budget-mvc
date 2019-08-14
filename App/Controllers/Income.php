<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\Data\AddIncome;
use \App\Timer;
use \App\Flash;

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
    public function indexAction($args = []) {
		$args['income_value'] = '0.00';
		$args['income_cats'] = \App\Models\Data::getUserIncomeCats();
		$args['transaction_date'] = $_POST['income_date'] ?? \App\Timer::getCurrentDate();
        View::renderTemplate('Income/index.html', $args);
    }
	
	/** Add an income to the database
	 * @return void*/
	public function addIncomeAction() {
		$args=[];
		$income = new AddIncome();
		if (! $income->send($_POST['income_value'], $_POST['income_date'], $_POST['income_note'])) {
			Flash::addMessage('Operacja nie powiodła się.', 'warning');
		}
		$args['errors'] = $income->errors;
		$this->indexAction($args);
	}
}
