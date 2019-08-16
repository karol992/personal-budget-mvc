<?php

namespace App\Models;

use PDO;
use \App\Auth;
use \App\Flash;

/**
 * User model
 * PHP version 7.0
 */
class Data extends \Core\Model
{
	
	/** Load income categories assigned to current user.
	 * @return assoc array 
	 */
	public static function getUserIncomeCats() {
		$table = 'incomes_category_assigned_to_users'; 
		return static::getUserCategories($table);
	}
	/** Load expense categories assigned to current user.
	 * @return assoc array
	 */
	public static function getUserExpenseCats() {
		$table = 'expenses_category_assigned_to_users'; 
		return static::getUserCategories($table);
	}
	/** Load payment categories assigned to current user.
	 * @return assoc array
	 */
	public static function getUserPaymentCats() {
		$table = 'payment_methods_assigned_to_users';
		return static::getUserCategories($table);
	}
	
	/** Get the categories of the current logged-in user 
     * @param string $table The name of searched table
	 * @return assoc array or null if not logged in
	 */
	protected static function getUserCategories($table) {
		$db = static::getDB();
		$query = $db->prepare("SELECT id, name FROM $table WHERE user_id=:id");
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll();
	}
	
	/**  Return searched name of category_id
	 * @param string $table Table name with searched category
	 * @param int $category_id Searched category id
	 * @return string $cat_name Name of searched category
	 */
	public static function getCategoryName($table, $id) {
		$db = static::getDB();
		$query = $db->prepare("SELECT name FROM $table WHERE id=:id");
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->execute();
		$name=$query->fetch();
		return $name[0];
	}
	
	/** Query execution based on id and period
	 * @return assoc array
	 */
	protected static function dbQuery($sql, $id, $period) {
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->bindValue(':start', $period['start'], PDO::PARAM_STR);
		$query->bindValue(':end', $period['end'], PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}
	
	/**
	 * @return 
	 */
	public static function createIncomeArray($period) {
		$incomeArray = static::getIncomeSums($period);
		foreach($incomeArray as $key => $value) {
			$incomeArray[$key]['list'] = static::incomeModalList($period, $incomeArray[$key]['id']);
		}
		return $incomeArray;
	}
	
	/**
	 * @return 
	 */
	public static function createExpenseArray($period) {
		$expenseArray = static::getExpenseSums($period);
		foreach($expenseArray as $key => $value) {
			$expenseArray[$key]['list'] = static::expenseModalList($period, $expenseArray[$key]['id']);
		}
		return $expenseArray;
	}
	
	/** Counts sums of user categories found in (database table:) incomes
	 * @return assoc array [name, id, iSum]
	 */
	public static function getIncomeSums($period) {
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
		return static::dbQuery($sql, $id,$period);
	}
	
	/** Counts sums of user categories found in (database table:) expenses
	 * @return assoc array [name, id, eSum]
	 */
	public static function getExpenseSums($period) {
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
		return static::dbQuery($sql, $id,$period);
	}
	
	/**
	 * @return assoc array
	 */
	protected static function incomeModalList($period, $id) {
		$sql = ("SELECT id, amount, date_of_income date, income_comment comment
		FROM incomes
		WHERE income_category_assigned_to_user_id = :id
		AND (incomes.date_of_income BETWEEN :start AND :end) 
		ORDER BY id DESC");
		return static::dbQuery($sql, $id, $period);
	}
	
	/**
	 * @return assoc array
	 */
	protected static function expenseModalList($period, $id) {
		$sql = ("SELECT ex.id, pm.id payId, pm.name payName, ex.amount, ex.date_of_expense date, ex.expense_comment comment FROM expenses ex 
		INNER JOIN payment_methods_assigned_to_users pm
		WHERE ex.expense_category_assigned_to_user_id = :id
		AND (ex.date_of_expense BETWEEN :start AND :end)
		AND ex.payment_method_assigned_to_user_id = pm.id
		ORDER BY ex.id DESC");
		return static::dbQuery($sql, $id, $period);
	}
	
}
