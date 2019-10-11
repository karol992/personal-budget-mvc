<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;

/**
 * UpdateIncome model
 * PHP version 7.0
 */
class UpdateIncome extends Transaction
{
	/** Body of public function send()
     * @_POST [income_id, qamount, date, comment]
     * @return boolean True if income is updated
     */
	protected function transactionQuery() {
		$db = static::getDB();
		$queryIncome = $db->prepare(
		"UPDATE incomes SET amount = :amount, date_of_income = :date, income_comment = :comment 
		WHERE incomes.id = :id;");
		$queryIncome->bindValue(':amount', $this->amount, PDO::PARAM_STR);
		$queryIncome->bindValue(':date', $this->income_date, PDO::PARAM_STR);
		$queryIncome->bindValue(':comment', $this->comment, PDO::PARAM_STR);
		$queryIncome->bindValue(':id', $this->income_id, PDO::PARAM_INT);
		return $queryIncome->execute();
	}
}
