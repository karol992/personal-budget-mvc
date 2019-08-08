<?php

namespace App\Controllers;

use \Core\View;

/**
 * Income controller
 * PHP version 7.0
 */
class Income extends Authenticated
{

    /**
     * Show the Add-Income page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Income/index.html');
    }
	
	/** Add an income to the database
	 * @return void*/
	public function addIncomeAction() {
		//przenieść do modelu
		/*$_SESSION['income_date']=$_POST['income_date'];
		$db = static::getDB();
		$queryIncome = $db->prepare("INSERT INTO incomes (id, user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
		VALUES (NULL, :user_id, :category_id, :value, :date, :comment)");
		$queryIncome->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$queryIncome->bindValue(':category_id', $_POST['income_category'], PDO::PARAM_INT);
		$queryIncome->bindValue(':value', $_POST['income_value'], PDO::PARAM_STR);
		$queryIncome->bindValue(':date', $_POST['income_date'], PDO::PARAM_STR);
		$queryIncome->bindValue(':comment', $_POST['income_note'], PDO::PARAM_STR);
		$queryIncome->execute();*/
		View::renderTemplate('Home/index.html');
	}
}
