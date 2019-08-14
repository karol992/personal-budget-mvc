<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\DataProperties\ShowBalance;
use \App\Timer;

/**
 * Balance controller
 * PHP version 7.0
 */
class Balance extends Authenticated
{

    /**
     * Show the Balance page
     * @return void
     */
    public function indexAction() {
		$args=[];
		$args['balance_period']=$this->getPeriodForView();
        View::renderTemplate('Balance/index.html', $args);
    }
	
	protected function getPeriodForView() {
		if(isset($_POST['balance_period'])) {
			$inPeriod=$_POST['balance_period'];
			switch ($inPeriod) {
				case 'current_month':
					$outPeriod = 'current_month'; break;
				case 'last_month':
					$outPeriod = 'last_month'; break;
				case 'current_year':
					$outPeriod = 'current_year'; break;
			}
		} else {
			$outPeriod = 'current_month___';
		}
		return $outPeriod;
	}
		
	
}
