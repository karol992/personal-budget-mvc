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
	 * @_POST [expense_category, payment_category, expense_value, expense_date, expense_note]
	 * @param $userId Integer
     * @set $successMessage String
     * @return boolean True if expense is added, false otherwise
     */
	protected function transactionQuery($userId) {
		$db = static::getDB();
		$categoryName = Data::getCategoryName('expenses_category_assigned_to_users', $_POST['expense_category']);
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
				$this->successMessage = 'Dodano: '.$categoryName.' - '.number_format($_POST['expense_value'], 2, ',', ' ').'';
				return true;
			}
		}
		return false;
	}
}
