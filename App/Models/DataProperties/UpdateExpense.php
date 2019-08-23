<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;

/**
 * UpdateIncome model
 * PHP version 7.0
 */
class UpdateExpense extends Transaction
{
	/** Body of public function send()
     * @_POST [expense_id, amount, date, comment]
     * @return boolean True if income is updated
     */
	protected function transactionQuery() {
		$db = static::getDB();
		$queryExpense = $db->prepare(
		"UPDATE expenses SET amount = :amount, date_of_expense = :date, expense_comment = :comment , payment_method_assigned_to_user_id = :payment
		WHERE expenses.id = :id;");
		$queryExpense->bindValue(':amount', $this->amount, PDO::PARAM_STR);
		$queryExpense->bindValue(':date', $this->expense_date, PDO::PARAM_STR);
		$queryExpense->bindValue(':comment', $this->comment, PDO::PARAM_STR);
		$queryExpense->bindValue(':id', $this->expense_id, PDO::PARAM_INT);
		$queryExpense->bindValue(':payment', $this->payment, PDO::PARAM_INT);
		return $queryExpense->execute();
	}
}
