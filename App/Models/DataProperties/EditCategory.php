<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;
use \App\Flash;
use \App\Auth;

/**
 * Edit Category model
 * PHP version 7.0
 */
class EditCategory extends \Core\Model
{
	/**
     * Error messages
     * @var array
     */
    public $errors = [];
	
	/**
     * Class constructor
     * @param assoc array $data  Initial property values
     * @return void
     */
    public function __construct($data = [])
    {
		foreach ($data as $key => $value) {
            $this->$key = $value;
        };
		$this->user_id = Auth::getUserId();
		if(isset($this->limited)) {
			$this->limited = 1;
		} else {
			$this->limited = 0;
		}
    }
	
	/**
     * Validate name
     * @set $errors[] Strings
     * @return void
     */
	protected function checkName() {
		$validation = true;
		$userCategories = [];
		$userCategories = Data::getUserCategories($this->tableName);
		foreach($userCategories as $category) {
			if (strtolower($category['name']) == strtolower($this->name)) {
				if ($category['id'] != $this->id) {
					$this->errors[] = 'Błąd ('.$category['name'].'): istnieje już taka kategoria.';
					$validation = false;
				} else if ($category['name'] == $this->name) {
					return false; //Do not update because of no change
				}
			}
		};
		if (mb_strlen($this->name) > 50) {
            $this->errors[] = 'Błąd ('.substr($this->name, 0, 15).'...): nazwa może zawierać maksymalnie 50 znaków.';
			$validation = false;
        } else if (mb_strlen($this->name) < 3) {
            $this->errors[] = 'Błąd ('.$this->name.'): nazwa musi zawierać przynajmniej 3 znaki. ';
			$validation = false;
        }
		if (preg_match('/^[a-z ąęćżźńłóś]+$/i', $this->name) == 0) {
            $this->errors[] = 'Błąd ('.$this->name.'): nazwa może zawierać tylko litery i spacje.';
			$validation = false;
        }
		return $validation;
	}
	
	/** 
     * @return void
     */
	protected function validateLimit() {
		$value = $this->limit_value;
		$isbool = $this->limited;
		if (!is_numeric($value)) {
			$this->errors[] = 'Limit musi być liczbą.';
		} else if ($value <= 0.01 ) {
			$this->errors[] = 'Limit musi być dodatni.';
		} else	if ($value >= 1000000 ) {
			$this->errors[] = 'Limit musi być mniejszy niż milion.';
		}
		if(($isbool!=0)&&($bool!=1)) {
			$this->errors[] = 'Błąd limitu.';
		}
	}
	
	/** 
     * @return void
     */
	protected function updateCategoryRecord() {
		if ($this->checkName()) {
			$db = static::getDB();
			$query = $db->prepare("UPDATE ".$this->tableName." tb SET name = :name WHERE tb.id = :id AND tb.user_id = :user_id;");
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$query->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
			return $query->execute();
		}
		return false;
	}
	
	/** 
     * @return void
     */
	protected function updateCategoryRecordWithLimit() {
		$limitUpdate = false;
		$nameUpdate = false;
		//$this->validateLimit();
		if (empty($this->errors)) {
			$db = static::getDB();
			$query = $db->prepare("UPDATE ".$this->tableName." tb SET limited = :limited, limit_value = :limit_value WHERE tb.id = :id AND user_id = :user_id;");
			$query->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':limited', $this->limited, PDO::PARAM_INT);
			$query->bindValue(':limit_value', $this->limit_value, PDO::PARAM_STR);
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$limitUpdate = $query->execute();
		}
		if ($this->checkName()) {
			$db = static::getDB();
			$query = $db->prepare("UPDATE ".$this->tableName." tb SET name = :name WHERE tb.id = :id AND user_id = :user_id;");
			$query->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$nameUpdate = $query->execute();
		}
		return ($limitUpdate || $nameUpdate);
	}
	
	public function updateIncomeCategory() {
		$this->tableName = 'incomes_category_assigned_to_users';
		return $this->updateCategoryRecord();
	}
	
	public function updateExpenseCategory() {
		$this->tableName = 'expenses_category_assigned_to_users';
		return $this->updateCategoryRecordWithLimit();
	}
	
	public function updatePaymentCategory() {
		$this->tableName = 'payment_methods_assigned_to_users';
		return $this->updateCategoryRecord();
	}
	
}
