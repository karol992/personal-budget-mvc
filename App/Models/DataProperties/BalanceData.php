<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Models\Data;

/**
 * BalanceData model
 * PHP version 7.0
 */
class BalanceData extends \Core\Model
{
	/** Create table with user income records of specified category
	 * @param 
	 * @return
	 */
	public static function getIncomeRecords($category_id,$period){
		$sql ="SELECT id, amount, date_of_income, income_comment FROM incomes 
		WHERE user_id = :user_id
		AND income_category_assigned_to_user_id = :category_id
		AND (date_of_income BETWEEN :start AND :end)";
		$user_id = Auth::getUserId();
		return Data::dbCategoryQuery($sql, $user_id, $category_id, $period);
		//return "aaa";
	}
}
