<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\AddIncome;
use \App\Timer;

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
		
		$args['income_cats'] = \App\Models\Data::getUserIncomeCats();
		$args['transaction_date'] = $_SESSION['transaction_date'] ?? \App\Timer::getCurrentDate();
        View::renderTemplate('Income/index.html', $args);
    }
	
	/** Add an income to the database
	 * @return void*/
	public function addIncomeAction() {
		$args=[];
		$income = new AddIncome();
		$income->send();
		$args['errors'] = $income->errors;
		$this->indexAction($args);
	}
}
