<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Flash;
use \App\Models\Data;

/**
 * User model
 * PHP version 7.0
 */
class AddIncome extends Transaction
{
	/** Body of public function send()
     * @return boolean True if income is added
     */
	protected function transactionQuery($userId) {
		$db = static::getDB();
		$categoryName = Data::getCategoryName('incomes_category_assigned_to_users', $_POST['income_category']);
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
				return true;
			}
		}
		return false;
	}
}
