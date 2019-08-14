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
	
	/**
	 */
	protected function getPeriodForView() {
		if (isset($_POST['balance_start_day']) && 
		isset ($_POST['balance_end_day'])) {
			if($_POST['balance_start_day'] &&
			$_POST['balance_end_day']) {
				$outPeriod=Timer::bindUserPeriod($_POST['balance_start_day'], $_POST['balance_end_day']);
			} else {
				$outPeriod = Timer::currentMonthPeriod();
			}
		} else if(isset($_POST['balance_period'])) {
			$inPeriod=$_POST['balance_period'];
			switch ($inPeriod) {
				case 'current_month':
					$outPeriod = Timer::currentMonthPeriod(); break;
				case 'last_month':
					$outPeriod = Timer::lastMonthPeriod(); break;
				case 'current_year':
					$outPeriod = Timer::currentYearPeriod(); break;
			}
		} else {
			if(isset($_SESSION['remembered_period'])) {
				$outPeriod = Timer::bindUserPeriod($_SESSION['remembered_period']['start'], $_SESSION['remembered_period']['end']);
			} else {
				$outPeriod = Timer::currentMonthPeriod();
			}
		}
		$this->savePeriodToSession($outPeriod);
		return $outPeriod;
	}
	
	/**
	 */
	protected function savePeriodToSession($period) {
		$_SESSION['remembered_period']['start']=$period['start'];
		$_SESSION['remembered_period']['end']=$period['end'];
	}
	
}
