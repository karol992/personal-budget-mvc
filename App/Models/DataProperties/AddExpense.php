<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;

/**
 * AddExpense model
 * PHP version 7.0
 */
class AddExpense extends Transaction
{	
	
	
	
	/** Body of public function send()
	 * @param(this) $expense_category, $payment_category, $expense_value, $expense_date, $expense_note
	 * @param $userId Integer
     * @set $successMessage String
     * @return boolean True if expense is added, false otherwise
     */
	protected function transactionQuery($userId) {
		$db = static::getDB();
		$categoryName = Data::getCategoryName('expenses_category_assigned_to_users', $this->expense_category);
		if($categoryName) {
			$queryExpense = $db->prepare("INSERT INTO expenses (id, user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
			VALUES (NULL, :user_id, :category_id, :payment_cat_id, :value, :date, :comment)");
			$queryExpense->bindValue(':user_id', $userId, PDO::PARAM_INT);
			$queryExpense->bindValue(':category_id', $this->expense_category, PDO::PARAM_INT);
			$queryExpense->bindValue(':payment_cat_id', $this->payment_category, PDO::PARAM_INT);
			$queryExpense->bindValue(':value', $this->expense_value, PDO::PARAM_STR);
			$queryExpense->bindValue(':date', $this->expense_date, PDO::PARAM_STR);
			$queryExpense->bindValue(':comment', $this->expense_note, PDO::PARAM_STR);
			if ($queryExpense->execute()) {
				$this->successMessage = 'Dodano: '.$categoryName.' - '.number_format($this->expense_value, 2, ',', ' ').'';
				return true;
			}
		}
		return false;
	}
}
