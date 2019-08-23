<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;

/**
 * AddIncome model
 * PHP version 7.0
 */
class AddIncome extends Transaction
{
	/** Body of public function send()
     * @param(this) $userId, $income_category, $income_value, $income_date, $income_note
	 * @set $successMessage String
     * @return boolean True if income is added
     */
	protected function transactionQuery() {
		$db = static::getDB();
		$categoryName = Data::getCategoryName('incomes_category_assigned_to_users', $this->income_category);
		if($categoryName) {
			$queryIncome = $db->prepare("INSERT INTO incomes (id, user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
			VALUES (NULL, :user_id, :category_id, :value, :date, :comment)");
			$queryIncome->bindValue(':user_id', $this->userId, PDO::PARAM_INT);
			$queryIncome->bindValue(':category_id', $this->income_category, PDO::PARAM_INT);
			$queryIncome->bindValue(':value', $this->income_value, PDO::PARAM_STR);
			$queryIncome->bindValue(':date', $this->income_date, PDO::PARAM_STR);
			$queryIncome->bindValue(':comment', $this->income_note, PDO::PARAM_STR);
			if ($queryIncome->execute()) {
				$this->successMessage = 'Dodano: '.$categoryName.' + '.number_format(($this->income_value), 2, ',', ' ').'';
				return true;
			}
		}
		return false;
	}
}
