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
    }
	
	
	/** Create table with all user income categories sums also empty ones (empty categories turned off)
	 * @param assoc array $period [start, end]
	 * @return assoc array $allIncomeSums [name, id, sum]
	 */
	protected function loadIncomeData($period) {
		$incomeCategories = Data::getUserIncomeCats();
		$incomeData = $this->getIncomeSums($period);
		foreach ($incomeData as $id) {
			$this->incomeSum += $id['sum'];
		}
		return $incomeData;
	}
	
	/** Create table with all user expense categories sums also empty ones (empty categories turned off)
	 * @param assoc array $period [start, end]
	 * @return assoc array $allExpenseSums [name, id, sum]
	 */
	protected function loadExpenseData($period) {
		$expenseCategories = Data::getUserExpenseCats();
		$expenseData = $this->getExpenseSums($period);
		foreach ($expenseData as $ed) {
			$this->expenseSum += $ed['sum'];
		}
		return $expenseData;
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
}
