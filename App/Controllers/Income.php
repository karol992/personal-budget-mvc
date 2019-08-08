<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;

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
    public function indexAction() {
        View::renderTemplate('Income/index.html');
    }
	
	/** Add an income to the database
	 * @return void*/
	public function addIncomeAction() {
		Data::addIncome();
		$this->indexAction();
	}
}
