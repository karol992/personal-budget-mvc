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
		$query = $db->prepare("SELECT id, name FROM ".$table." WHERE user_id=:id");
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		$query->execute();
		return $query->fetchAll();
	}
	
	/**  Return searched name of category_idgit s
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
