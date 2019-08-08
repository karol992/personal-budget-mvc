<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * User model
 * PHP version 7.0
 */
class Data extends \Core\Model
{
	/** Copy default categories to categories assigned to users
     * @param integer $userId The user id
	 * @return bolean True if categories was copied, false otherwise
	 */
	public static function copyDefaultCategories($userId) {
		$db = static::getDB();
		$copyPayments=$db->prepare("INSERT INTO payment_methods_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM payment_methods_default");
		$copyPayments->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		$copyIncomes=$db->prepare("INSERT INTO incomes_category_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM incomes_category_default");
		$copyIncomes->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		$copyExpenses=$db->prepare("INSERT INTO expenses_category_assigned_to_users (id, user_id, name) SELECT NULL, :newUserId, name FROM expenses_category_default");
		$copyExpenses->bindValue(':newUserId',$userId,PDO::PARAM_INT);
		return ($copyPayments->execute() && $copyIncomes->execute() && $copyExpenses->execute());
	}
	
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
		$query = $db->prepare("SELECT id, name FROM ".$table." WHERE user_id=:id");
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll();
	}
	
	/**
	 *
	 */
	public static function addIncome() {
		$userId = Auth::getUserId();
		if($userId) {
			$_SESSION['income_date'] = $_POST['income_date'];
			$db = static::getDB();
			$queryIncome = $db->prepare("INSERT INTO incomes (id, user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
			VALUES (NULL, :user_id, :category_id, :value, :date, :comment)");
			$queryIncome->bindValue(':user_id', $userId, PDO::PARAM_INT);
			$queryIncome->bindValue(':category_id', $_POST['income_category'], PDO::PARAM_INT);
			$queryIncome->bindValue(':value', $_POST['income_value'], PDO::PARAM_STR);
			$queryIncome->bindValue(':date', $_POST['income_date'], PDO::PARAM_STR);
			$queryIncome->bindValue(':comment', $_POST['income_note'], PDO::PARAM_STR);
			$queryIncome->execute();
		}
	}
	
}
