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
	/**
     * Error messages
     * @var array
     */
    public $errors = [];
	
	
	
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
	
	/* Functions addIncome, addExpense are similar but didn't inherits from any function cause of code simplicity. */
	/** Save income in database
	 * @return void
	 */
	/*public static function addIncome() {
		$this->validate($_POST['income_value'], $_POST['income_date'], $_POST['income_note']);
		if (empty($this->errors)) {
			$userId = Auth::getUserId();
			if($userId) {
				$db = static::getDB();
				$categoryName = static::getCategoryName('incomes_category_assigned_to_users', $_POST['income_category']);
				if($categoryName) {
					$queryIncome = $db->prepare("INSERT INTO incomes (id, user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
					VALUES (NULL, :user_id, :category_id, :value, :date, :comment)");
					$queryIncome->bindValue(':user_id', $userId, PDO::PARAM_INT);
					$queryIncome->bindValue(':category_id', $_POST['income_category'], PDO::PARAM_INT);
					$queryIncome->bindValue(':value', $_POST['income_value'], PDO::PARAM_STR);
					$queryIncome->bindValue(':date', $_POST['income_date'], PDO::PARAM_STR);
					$queryIncome->bindValue(':comment', $_POST['income_note'], PDO::PARAM_STR);
					if ($queryIncome->execute()) {
						Flash::addMessage('Dodano '.$categoryName.' +'.$_POST['income_value'].'');
						$_SESSION['transaction_date'] = $_POST['income_date'];
						return true;
					}
				}
			}
		}
		return false;
	}*/

	/** Save expense in database
	 * @return void
	 */
	public static function addExpense() {
		$this->validate($_POST['expense_value'], $_POST['expense_date'], $_POST['expense_note']);
		if (empty($this->errors)) {
			$userId = Auth::getUserId();
			if($userId) {
				$db = static::getDB();
				$categoryName = static::getCategoryName('expenses_category_assigned_to_users', $_POST['expense_category']);
				if($categoryName) {
					$queryExpense = $db->prepare("INSERT INTO expenses (id, user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
					VALUES (NULL, :user_id, :category_id, :payment_cat_id, :value, :date, :comment)");
					$queryExpense->bindValue(':user_id', $userId, PDO::PARAM_INT);
					$queryExpense->bindValue(':category_id', $_POST['expense_category'], PDO::PARAM_INT);
					$queryExpense->bindValue(':payment_cat_id', $_POST['payment_category'], PDO::PARAM_INT);
					$queryExpense->bindValue(':value', $_POST['expense_value'], PDO::PARAM_STR);
					$queryExpense->bindValue(':date', $_POST['expense_date'], PDO::PARAM_STR);
					$queryExpense->bindValue(':comment', $_POST['expense_note'], PDO::PARAM_STR);
					if ($queryExpense->execute()) {
						Flash::addMessage('Dodano '.$categoryName.' -'.$_POST['expense_value'].'');
						$_SESSION['transaction_date'] = $_POST['expense_date'];
						return true;
					}
				}
			}
		}
		return false;
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
	

}
