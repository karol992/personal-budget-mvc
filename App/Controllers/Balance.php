<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\DataProperties\ShowBalance;
use \App\Models\DataProperties\BalanceData;
use \App\Timer;
use \App\Flash;
use \App\Models\DataProperties\UpdateIncome;
use \App\Models\DataProperties\UpdateExpense;
use \App\Models\DataProperties\DataCleaner;
use \App\Models\Data;

/**
 * Balance controller
 * PHP version 7.0
 */
class Balance extends Authenticated
{

    /**
     * Show the Balance page, used on page load
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
		$args['motivation_info'] = $this->loadMotivationText($balance->balanceValue);
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
	
	/** Edit income record
	 * @return void
	 */
	public function editIncomeAction() {
		$action = $_POST['action'];
		if ($action == 'delete') {
			$this->deleteIncome();
		} else if ($action == 'update') {
			$this->updateIncome();
		}
	}
	
	/** Edit expense record
	 * @return void
	 */
	public function editExpenseAction() {
		$action = $_POST['action'];
		if ($action == 'delete') {
			$this->deleteExpense();
		} else if ($action == 'update') {
			$this->updateExpense();
		}
	}
	
	/** Update income record
	 * @return void
	 */
	public function updateIncome() {
		$update = new UpdateIncome($_POST);
		if (!$update->send($update->amount, $update->income_date, $update->comment)) {
			Flash::addMessage("Income update failed.",'warning');
		}
	}
	
	/** Remove income record
	 * @return void
	 */
	public function deleteIncome() {
		if (!DataCleaner::incomeRecord($_POST['income_id'])) {
			Flash::addMessage("Income delete failed.",'warning');
		}
	}
	
	public function deleteIncomeRecordAjaxAction() {
		$response=[];
		if (DataCleaner::incomeRecord($_POST['income_id'])) {
			$response['success']=true;
			$response['new_sum']=$this->getIncomeRecordSum($_POST['category_id']);
			$response['balance']=$this->getBalanceValue();
		} else {
			$response['success']=false;
		}
		echo json_encode($response);
	}
	
	/** Update expense record
	 * @return void
	 */
	public function updateExpense() {
		$update = new UpdateExpense($_POST);
		if (!$update->send($update->amount, $update->expense_date, $update->comment)) {
			Flash::addMessage("Expense update failed.",'warning');
		}
	}
	
	/** Remove expense record
	 * @return void
	 */
	public function deleteExpense() {
		if (!DataCleaner::expenseRecord($_POST['expense_id'])) {
			Flash::addMessage("Expense delete failed.",'warning');
		}
	}
	
	public function deleteExpenseRecordAjaxAction() {
		$response=[];
		if (DataCleaner::expenseRecord($_POST['expense_id'])) {
			$response['success']=true;
			$response['new_sum']=$this->getExpenseRecordSum($_POST['category_id']);
			$response['all_sums']=$this->getExpenseSums();
			$response['balance']=$this->getBalanceValue();
		} else {
			$response['success']=false;
		}
		echo json_encode($response);
	}
	
	/** Specify the motivation div in separated strings (cause of RWD)
	 * @param $value, Integer balance value
	 * @return assoc array $text ([0] - style, [1] - first span, [2] - second span)
	 */
	protected function loadMotivationText($value) {
		$text=[];
		if ($value >= 0) {
			$text[0]='';
			$text[1]='Gratulacje.';
			$text[2]='Świetnie zarządzasz finansami!';
		} else {
			$text[0]='color:red';
			$text[1]='Uważaj,';
			$text[2]='wpadasz w długi!';
		}
		return $text;
	}
	
	/** Get income-list
	 * @return void
	 */
	public function getIncomeRecordsAjaxAction() {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$response=BalanceData::getIncomeRecords($_POST['category_id'],$period);
		echo json_encode($response);
	}
	
	/** Get expense-list
	 * @return void
	 */
	public function getExpenseRecordsAjaxAction() {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$response=BalanceData::getExpenseRecords($_POST['category_id'],$period);
		echo json_encode($response);
	}
	
	/** Update income record
	 * @return void
	 */
	public function updateIncomeRecordAjaxAction() {
		$response =[];
		$update = new UpdateIncome($_POST);
		if ($update->send($update->amount, $update->income_date, $update->comment)) {
			$response['success']=true;
			$response['new_sum']=$this->getIncomeRecordSum($_POST['category_id']);
			$response['balance']=$this->getBalanceValue();
		} else {
			$response['success']=false;
			$response['errors']=$update->errors;
		}
		echo json_encode($response);
	}
	
	public function updateExpenseRecordAjaxAction() {
		$response =[];
		$update = new UpdateExpense($_POST);
		if ($update->send($update->amount, $update->expense_date, $update->payment, $update->comment)) {
			$response['success']=true;
			$response['new_sum']=$this->getExpenseRecordSum($_POST['category_id']);
			$response['all_sums']=$this->getExpenseSums();
			$response['balance']=$this->getBalanceValue();
		} else {
			$response['success']=false;
			$response['errors']=$update->errors;
		}
		echo json_encode($response);
	}
	
	protected function getIncomeRecordSum($category_id) {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$sum=BalanceData::getIncomeSum($category_id,$period);
		return $sum['0']['new_sum'];
	}
	
	protected function getExpenseRecordSum($category_id) {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$sum=BalanceData::getExpenseSum($category_id,$period);
		return $sum['0']['new_sum'];
	}
	
	public function getPaymentCategoriesAjaxAction() {
		echo json_encode(Data::getUserPaymentCats());
	}
	
	protected function getExpenseSums() {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$sums=BalanceData::getAllExpenseSums($period);
		return $sums;
	}
	
	protected function getIncomeSums() {
		$period=[];
		$period['start']=$_SESSION['remembered_period']['start'];
		$period['end']=$_SESSION['remembered_period']['end'];
		$sums=BalanceData::getAllIncomeSums($period);
		return $sums;
	}
	
	protected function getBalanceValue() {
		$incomeSums = $this->getIncomeSums();
		$expenseSums = $this->getExpenseSums();
		$balance = 0;
		foreach ($incomeSums as $sum) {
			$balance += $sum['sum'];
		}
		foreach ($expenseSums as $sum) {
			$balance -= $sum['sum'];
		}
		return $balance;
	}
}