<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Timer;

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
		$date_error = Timer::dateValidation($date);
		if($date_error) {
			$this->errors[] = $date_error;
		}
		//Comment
		if (strlen($comment) > 100) {
            $this->errors[] = 'Komentarz może zawierać maksymalnie 100 znaków.';
        }
		
	}
}
