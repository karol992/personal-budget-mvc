<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Models\Data;

/**
 * ShowBalance model
 * PHP version 7.0
 */
class ShowBalance extends \Core\Model
{	
	/**
     * Class constructor - create all own parameters:
		* public $incomeSum;
		* public $expenseSum;
		* public $incomeData;
		* public $expenseData;
		* public $paymentCategories;
		* public $pieChartExpenseSums;
		* public $balanceValue;
		* public $motivationInfo;
     * @param assoc array $period [start, end]
     * @return void
     */
    public function __construct($period) {
		$this->incomeSum = 0;
		$this->expenseSum = 0;
		$this->incomeData = $this->loadIncomeData($period);
		$this->expenseData = $this->loadExpenseData($period);
		$this->paymentCategories = Data::getUserPaymentCats();
		$this->pieChartExpenseSums = $this->getExpenseSums($period);
		$this->balanceValue = $this->incomeSum - $this->expenseSum;
		$this->motivationInfo = $this->loadMotivationText($this->balanceValue);
    }
	
	
	/** Create table with all user income categories sums also empty ones
	 * @param assoc array $period [start, end]
	 * @return assoc array $allIncomeSums [name, id, sum, list[]]
	 */
	protected function loadIncomeData($period) {
		$incomeCategories = Data::getUserIncomeCats();
		$incomeData = $this->createIncomeArray($period);
		foreach ($incomeData as $id) {
			$this->incomeSum += $id['sum'];
		}
		return $this->addZeroSums($incomeCategories, $incomeData);
	}
	
	/** Create table with all user expense categories sums also empty ones
	 * @param assoc array $period [start, end]
	 * @return assoc array $allExpenseSums [name, id, sum]
	 */
	protected function loadExpenseData($period) {
		$expenseCategories = Data::getUserExpenseCats();
		$expenseData = $this->createExpenseArray($period);
		foreach ($expenseData as $ed) {
			$this->expenseSum += $ed['sum'];
		}
		return $this->addZeroSums($expenseCategories, $expenseData);
	}
	
	/** Fill array with user categories not occuring in $dataArray
	 * @param $categoriesArray [id, name]
	 * @param $dataArray  [name, id, sum, list[]]
	 * @return assoc array $dataArray [name, id, sum, list[]]
	 */
	protected function addZeroSums($categoriesArray, $dataArray) {
		foreach($categoriesArray as $ca) {
			//search the $categoriesArray for a every category assigned to users
			$key = array_search($ca['name'], array_column($dataArray, 'name'));
			//that way below, because of [0] in array; isset, isnull, empty was useless here
			if(strlen((string)$key)==0) { 
				$empty_category=array( 'name' => $ca['name'], 'id' => $ca['id'], 'sum' => 0.00, 'list' => NULL);
				array_push($dataArray, $empty_category);
			}
			unset($key);
		}
		return $dataArray;
	}
	
	/** Create array with income categories data
	 * @param assoc array $period [start, end]
	 * @return assoc array $incomeArray [name, id, sum, list[id, amount, date, comment]]
	 */
	public function createIncomeArray($period) {
		$incomeArray = $this->getIncomeSums($period);
		foreach($incomeArray as $key => $value) {
			$incomeArray[$key]['list'] = $this->incomeModalList($period, $incomeArray[$key]['id']);
		}
		return $incomeArray;
	}
	
	/** Create array with expense categories data
	 * @param assoc array $period [start, end]
	 * @return assoc array $expenseArray [name, id, sum, list [id, payId, payName, amount, date, comment]]
	 */
	public function createExpenseArray($period) {
		$expenseArray = $this->getExpenseSums($period);
		foreach($expenseArray as $key => $value) {
			$expenseArray[$key]['list'] = $this->expenseModalList($period, $expenseArray[$key]['id']);
		}
		return $expenseArray;
	}
	
	/** Counts sums of user categories found in (database table:) incomes
	 * @param assoc array $period [start, end]
	 * @return assoc array [name, id, sum]
	 */
	public function getIncomeSums($period) {
		$sql = ("
			SELECT icat.name, icat.id, SUM(ic.amount) sum
			FROM incomes ic
			INNER JOIN incomes_category_assigned_to_users icat
			ON ic.income_category_assigned_to_user_id = icat.id
			AND (ic.date_of_income BETWEEN :start AND :end)
			AND icat.id IN (
				SELECT icat.id FROM incomes_category_assigned_to_users icat
				INNER JOIN users
				ON users.id = icat.user_id
				AND users.id = :id
			)
			GROUP BY icat.id
			ORDER BY sum DESC;");
		$id = Auth::getUserId();
		return Data::dbQuery($sql, $id,$period);
	}
	
	/** Counts sums of user categories found in (database table:) expenses
	 * @param assoc array $period [start, end]
	 * @return assoc array [name, id, sum]
	 */
	public function getExpenseSums($period) {
		$sql = ("SELECT ecat.name, ecat.id, SUM(ex.amount) sum
		FROM expenses ex
		INNER JOIN expenses_category_assigned_to_users ecat
		ON ex.expense_category_assigned_to_user_id = ecat.id
		AND (ex.date_of_expense BETWEEN :start AND :end)
		AND ecat.id IN (
			SELECT ecat.id FROM expenses_category_assigned_to_users ecat
			INNER JOIN users
			ON users.id = ecat.user_id
			AND users.id = :id
		)
		GROUP BY ecat.id
		ORDER BY sum DESC;");
		$id = Auth::getUserId();
		return Data::dbQuery($sql, $id,$period);
	}
	
	/** Incomes of one Category
	 * @param assoc array $period [start, end]
	 * @param $id, Integer id of income category
	 * @return assoc array [id, amount, date, comment]
	 */
	protected function incomeModalList($period, $id) {
		$sql = ("SELECT id, amount, date_of_income date, income_comment comment
		FROM incomes
		WHERE income_category_assigned_to_user_id = :id
		AND (incomes.date_of_income BETWEEN :start AND :end) 
		ORDER BY id DESC");
		return Data::dbQuery($sql, $id, $period);
	}
	
	/** Expenses of one category
	 * @param assoc array $period [start, end]
	 * @param $id, Integer id of expense category
	 * @return assoc array [id, payId, payName, amount, date, comment]
	 */
	protected function expenseModalList($period, $id) {
		$sql = ("SELECT ex.id, pm.id payId, pm.name payName, ex.amount, ex.date_of_expense date, ex.expense_comment comment FROM expenses ex 
		INNER JOIN payment_methods_assigned_to_users pm
		WHERE ex.expense_category_assigned_to_user_id = :id
		AND (ex.date_of_expense BETWEEN :start AND :end)
		AND ex.payment_method_assigned_to_user_id = pm.id
		ORDER BY ex.id DESC");
		return Data::dbQuery($sql, $id, $period);
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
	
	protected function checkAnyExpenseExist () {
		;
	}
}
