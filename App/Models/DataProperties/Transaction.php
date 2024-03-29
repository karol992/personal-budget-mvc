<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Timer;

/**
 * Transaction model
 * PHP version 7.0
 */
abstract class Transaction extends \Core\Model
{
	/**
     * Error messages
     * @var array
     */
    public $errors = [];
	
	/**
     * Class constructor
     * @param array $data  Initial property values
     * @return void
     */
    public function __construct($data=[])
    {
		foreach ($data as $key => $value) {
            $this->$key = $value;
        };
		$this->userId = Auth::getUserId();
    }
	
	/**
     * Add transaction to database 
     * @return boolean
     */
	public function send($value, $date, $note) {
		$this->validate($value, $date, $note);
		if (empty($this->errors)) {
			if($this->userId) {
				return $this->transactionQuery();
			}
		}
		return false;
	}
	
	/** Body of public function send()
     */
	protected function transactionQuery() {
	}
	
	/**
     * Validate current property values, adding valiation error messages to the errors array property
     * @set $errors[] Strings
     * @return void
     */
	protected function validate($value, $date, $comment) {
		//Value
        if (!is_numeric($value)) {
			$this->errors[] = 'Kwota musi być liczbą.';
		} else if ($value <= 0.01 ) {
            $this->errors[] = 'Kwota musi być dodatnia.';
        } else	if ($value >= 1000000 ) {
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
