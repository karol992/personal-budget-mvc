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
     * @_POST [income_id, amount, date, comment]
     * @return boolean True if income is updated
     */
	/*protected function transactionQuery($userId) {
		$db = static::getDB();
		$queryIncome = $db->prepare(
		"UPDATE incomes 
		SET amount = :amount, date_of_income = :date, income_comment = :comment 
		WHERE incomes.id = :id;");
		$queryIncome->bindValue(':amount', $_POST['income_category'], PDO::PARAM_INT);
		$queryIncome->bindValue(':date_of_income', $_POST['income_category'], PDO::PARAM_INT);
		$queryIncome->bindValue(':income_comment', $_POST['income_value'], PDO::PARAM_STR);
		$queryIncome->bindValue(':id', $_POST['income_date'], PDO::PARAM_STR);
		return $queryIncome->execute();
}*/
}
