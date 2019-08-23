<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Flash;

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
		$query = $db->prepare("DELETE FROM incomes WHERE incomes.id = :id;");
		$query->bindValue(':id', $incomeId, PDO::PARAM_INT);
		return $query->execute();
	}
	/** Delete expense
     * @return boolean
     */
	public static function expenseRecord($expenseId) {
		$db = static::getDB();
		$query = $db->prepare("DELETE FROM expenses WHERE expenses.id = :id;");
		$query->bindValue(':id', $expenseId, PDO::PARAM_INT);
		return $query->execute();
	}
	
	
}
