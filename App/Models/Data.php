<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * Data model
 * PHP version 7.0
 */
class Data extends \Core\Model
{
	
	/** Load income categories assigned to current user.
	 * @return assoc array [id, name]
	 */
	public static function getUserIncomeCats() {
		$table = 'incomes_category_assigned_to_users'; 
		return static::getUserCategories($table);
	}
	/** Load expense categories assigned to current user.
	 * @return assoc array [id, name]
	 */
	public static function getUserExpenseCats() {
		$table = 'expenses_category_assigned_to_users'; 
		return static::getUserCategoriesExtended($table);
	}
	/** Load payment categories assigned to current user.
	 * @return assoc array [id, name]
	 */
	public static function getUserPaymentCats() {
		$table = 'payment_methods_assigned_to_users';
		return static::getUserCategories($table);
	}
	
	/** Get the categories of the current logged-in user 
     * @param string $table The name of searched table
	 * @return assoc array [id, name] or null if not logged in
	 */
	public static function getUserCategories($table) {
		$db = static::getDB();
		$query = $db->prepare("SELECT id, name FROM $table WHERE user_id=:id");
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll();
	}
	
	public static function getUserCategoriesExtended($table) {
		$db = static::getDB();
		$query = $db->prepare("SELECT id, name, limited, limit_value FROM $table WHERE user_id=:id");
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
		$query = $db->prepare("SELECT name FROM $table WHERE id=:id AND user_id=:user_id");
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		$name=$query->fetch();
		return $name[0];
	}
	
	/**  Return searched id of category_name
	 * @param string $table Table name with searched category
	 * @param string $name Searched category name
	 * @return int $id Id of searched category
	 */
	public static function getCategoryId($table, $name) {
		$db = static::getDB();
		$query = $db->prepare("SELECT id FROM $table WHERE name=:name AND user_id=:user_id");
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		$id=$query->fetch();
		return $id[0];
	}
	
	/** Query execution based on id and period
	 * @param $sql, string, user SQL query
	 * @param $id, integer
	 * @param $period, assoc array [start, end]
	 * @return result of SQL query
	 */
	public static function dbQuery($sql, $id, $period) {
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		$query->bindValue(':start', $period['start'], PDO::PARAM_STR);
		$query->bindValue(':end', $period['end'], PDO::PARAM_STR);
		$query->execute();
		return $query->fetchAll();
	}
	
	/** 
	 * @param $cat_id, integer Expense Category id
	 * @param $period, assoc array [start, end]
	 * @return string $sum Sum of category expenses in period
	 */
	public static function getExpenseSum($cat_id, $period) {
		$sql = "SELECT SUM(ex.amount) FROM expenses ex 
		WHERE ex.user_id = :user_id 
		AND ex.expense_category_assigned_to_user_id = :cat_id 
		AND (ex.date_of_expense BETWEEN :start AND :end);";
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		$query->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
		$query->bindValue(':start', $period['start'], PDO::PARAM_STR);
		$query->bindValue(':end', $period['end'], PDO::PARAM_STR);
		$query->execute();
		$sum=$query->fetch();
		return $sum[0];
	}
	
	/** 
	 * @param $cat_id, integer Expense Category id
	 * @return string $limit_value Expense Category limit value
	 */
	public static function getCategoryLimit($cat_id) {
		$sql = "SELECT limit_value FROM expenses_category_assigned_to_users 
		WHERE user_id = :user_id 
		AND id = :cat_id;";
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		$query->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
		$query->execute();
		$limit_value=$query->fetch();
		return $limit_value[0];
	}
}
