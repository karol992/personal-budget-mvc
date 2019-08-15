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
	
	/** Counts sums of user categories found in (database table:) incomes
	 * @return assoc array [name, id, iSum]
	 */
	public static function getIncomesSums($period) {
		$db = static::getDB();
		$query = $db->prepare("
			SELECT icat.name, icat.id, SUM(ic.amount) iSum
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
			ORDER BY iSum DESC;");
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		$query->bindValue(':start', $period['start'], PDO::PARAM_STR);
		$query->bindValue(':end', $period['end'], PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}

}
