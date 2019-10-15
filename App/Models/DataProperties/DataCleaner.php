<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Flash;
use \App\Auth;

/**
 * DataCleaner model
 * PHP version 7.0
 */
class DataCleaner extends \Core\Model
{
	/** Delete income
     * @return boolean
     */
	public static function incomeRecord($incomeId) {
		$db = static::getDB();
		$query = $db->prepare("DELETE FROM incomes WHERE incomes.id = :id AND incomes.user_id = :user_id;");
		$query->bindValue(':id', $incomeId, PDO::PARAM_INT);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		return $query->execute();
	}
	
	/** Delete expense
     * @return boolean
     */
	public static function expenseRecord($expenseId) {
		$db = static::getDB();
		$query = $db->prepare("DELETE FROM expenses WHERE expenses.id = :id AND expenses.user_id = :user_id;");
		$query->bindValue(':id', $expenseId, PDO::PARAM_INT);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		return $query->execute();
	}
	
	/** Remove all data connected with logged user
     * @return boolean
     */
	public static function removeAccount() {
		if (static::removeRecords("incomes") && 
		static::removeRecords("expenses") && 
		static::removeRecords("incomes_category_assigned_to_users") && 
		static::removeRecords("expenses_category_assigned_to_users") && 
		static::removeRecords("payment_methods_assigned_to_users")) {
			return static::removeUser();
		} else {
			return false;
		}
	}
	
	/** Remove all data connected with logged user
     * @return boolean
     */
	protected static function removeRecords($table) {
		$db = static::getDB();
		$sql = "DELETE FROM ".$table." WHERE ".$table.".user_id = :id;";
		$query = $db->prepare($sql);
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		return $query->execute();
	}
	
	/** Remove all data connected with logged user
     * @return boolean
     */
	protected static function removeUser() {
		$db = static::getDB();
		$sql = "DELETE FROM users WHERE users.id = :id;";
		$query = $db->prepare($sql);
		$query->bindValue(':id', Auth::getUserId(), PDO::PARAM_INT);
		return $query->execute();
	}

}
