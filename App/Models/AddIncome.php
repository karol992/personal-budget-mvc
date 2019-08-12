<?php

namespace App\Models;

use PDO;
use \App\Auth;
use \App\Flash;
use \App\Models\Data;

/**
 * User model
 * PHP version 7.0
 */
class AddIncome extends \Core\Model
{
	/**
     * Error messages
     * @var array
     */
    public $errors = [];
	
	public function send() {
		$this->validate($_POST['income_value'], $_POST['income_date'], $_POST['income_note']);
		if (empty($this->errors)) {
			$userId = Auth::getUserId();
			if($userId) {
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
						$_SESSION['transaction_date'] = $_POST['income_date'];
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
     * Validate current property values, adding valiation error messages to the errors array property
     * @return void
     */
	protected function validate($value, $date, $comment) {
		//Value
        if ($value <= 0 ) {
            $this->errors[] = 'Kwota musi być większa od zera.';
        }
		if ($value >= 1000000 ) {
            $this->errors[] = 'Kwota musi być mniejsza niż milion.';
        }
		//Date
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) == 0) {
            $this->errors[] = 'Poprawny format daty to YYYY-MM-DD';
        } else if (!$this->validateDate($date, 'Y-m-d')) {
			$this->errors[] = 'Data nie istnieje.';
		}
		//Comment
		if (strlen($comment) > 100) {
            $this->errors[] = 'Komentarz może zawierać maksymalnie 100 znaków.';
        }
		
	}
	
	/** Check if date exist
	 * Source : https://www.php.net/manual/en/function.checkdate.php#113205
	 * @return bolean True when date is correct, false otherwise
	 */
	protected function validateDate($date, $format)
	{
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}
