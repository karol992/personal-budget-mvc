<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\DataProperties\ShowBalance;
use \App\Timer;
use \App\Models\Data;

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
		$period=$this->getPeriodForView();
		$args['balance_period']=Timer::dottedDate($period);
		$balance = new ShowBalance($period);
		$args['incomes'] = $balance->incomeData;
		$args['expenses'] = $balance->expenseData;
		$args['payment_cats'] = $balance->paymentCategories;
		$args['js_expenses_sums'] = $balance->pieChartExpenseSums;
		$args['balance_value'] = $balance->balanceValue;
		$args['motivation_info'] = $balance->motivationInfo;
		$args['expense_sum'] = $balance->expenseSum;
        View::renderTemplate('Balance/index.html', $args);
    }
	
	/** Processes session and post period variables
	 * @return assoc array $outPeriod [start, end], format ("Y-m-d") 
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
	
	/** Save Period To Session
	 * @param assoc array 
	 */
	protected function savePeriodToSession($period) {
		$_SESSION['remembered_period']['start']=$period['start'];
		$_SESSION['remembered_period']['end']=$period['end'];
	}
	
}
