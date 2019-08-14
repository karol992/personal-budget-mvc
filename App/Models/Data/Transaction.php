<?php

namespace App\Models\Data;

use \App\Auth;

/**
 * User model
 * PHP version 7.0
 */
abstract class Transaction extends \Core\Model
{
	/**
     * Error messages
     * @var array
     */
    public $errors = [];
	
	public function send($value, $date, $note) {
		$this->validate($value, $date, $note);
		if (empty($this->errors)) {
			$userId = Auth::getUserId();
			if($userId) {
				return $this->transactionQuery($userId);
			}
		}
		return false;
	}
	
	/** Body of public function send()
     * @return boolean
     */
	protected function transactionQuery($userId) {
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
