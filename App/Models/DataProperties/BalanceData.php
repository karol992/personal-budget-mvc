<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Models\Data;

/**
 * BalanceData model
 * PHP version 7.0
 */
class BalanceData extends \Core\Model
{
	/** Create table with user income records of specified category
	 * @param $category_id
	 * @param assoc array $period [start, end]
	 * @return assoc array [id, amount, date_of_income, income_comment]
	 */
	public static function getIncomeRecords($category_id,$period){
		$sql ="SELECT id, amount, date_of_income, income_comment FROM incomes 
		WHERE user_id = :user_id
		AND income_category_assigned_to_user_id = :category_id
		AND (date_of_income BETWEEN :start AND :end)";
		$user_id = Auth::getUserId();
		return Data::dbCategoryQuery($sql, $user_id, $category_id, $period);
	}
	
	public static function getExpenseRecords($category_id,$period){
		$sql ="SELECT id, amount, date_of_expense, 	payment_method_assigned_to_user_id, expense_comment FROM expenses 
		WHERE user_id = :user_id
		AND expense_category_assigned_to_user_id = :category_id
		AND (date_of_expense BETWEEN :start AND :end)";
		$user_id = Auth::getUserId();
		return Data::dbCategoryQuery($sql, $user_id, $category_id, $period);
	}
	
	/** Create table with user income records of specified category
	 * @param $category_id
	 * @param assoc array $period [start, end]
	 * @return assoc array [id, amount, date_of_income, income_comment]
	 */
	public static function getIncomeSum($category_id,$period){
		$sql ="SELECT SUM(amount) new_sum FROM incomes 
		WHERE user_id = :user_id
		AND income_category_assigned_to_user_id = :category_id
		AND (date_of_income BETWEEN :start AND :end)";
		$user_id = Auth::getUserId();
		return Data::dbCategoryQuery($sql, $user_id, $category_id, $period);
	}
	
	public static function getExpenseSum($category_id,$period){
		$sql ="SELECT SUM(amount) new_sum FROM expenses 
		WHERE user_id = :user_id
		AND expense_category_assigned_to_user_id = :category_id
		AND (date_of_expense BETWEEN :start AND :end)";
		$user_id = Auth::getUserId();
		return Data::dbCategoryQuery($sql, $user_id, $category_id, $period);
	}
	
	public static function getAllExpenseSums($period) {
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
	
	public static function getAllIncomeSums($period) {
		$sql = ("SELECT icat.name, icat.id, SUM(ic.amount) sum
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
}
