<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;
use \App\Flash;
use \App\Auth;

/**
 * Add Category model
 * PHP version 7.0
 */
class AddCategory extends \Core\Model
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
    }
	
	/**
     * Validate name
     * @set $errors[] Strings
     * @return void
     */
	protected function validation() {
		$userCategories = [];
		$userCategories = Data::getUserCategories($this->tableName);
		foreach($userCategories as $category) {
			if (strtolower($category['name']) == strtolower($this->name)) {
				$this->errors[] = 'Błąd ('.$category['name'].'): istnieje już taka kategoria.';
			} 
		};
		if (mb_strlen($this->name) > 50) {
            $this->errors[] = 'Błąd ('.substr($this->name, 0, 15).'...): nazwa może zawierać maksymalnie 50 znaków.';
        } else if (mb_strlen($this->name) < 3) {
            $this->errors[] = 'Błąd ('.$this->name.'): nazwa musi zawierać przynajmniej 3 znaki. ';
        }
		if (preg_match('/^[a-z ąęćżźńłóś]+$/i', $this->name) == 0) {
            $this->errors[] = 'Błąd ('.$this->name.'): nazwa może zawierać tylko litery i spacje.';
        }
	}
	
	/** Add new category
	*/
	protected function addCategory() {
		$this->validation();
		if(empty($this->errors)) {
			$sql = "INSERT INTO ".$this->tableName." (id, user_id, name) VALUES (NULL, :user_id, :name)";
			$db = static::getDB();
			$query = $db->prepare($sql);
			$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			$query->execute();
		} else {
			foreach ($this->errors as $error) {
				Flash::addMessage($error, 'warning');
			};
		}
	}
	
	public function addIncomeCategory() {
		$this->tableName = "incomes_category_assigned_to_users";
		return $this->addCategory();
	}
	
	public function addExpenseCategory() {
		$this->tableName = "expenses_category_assigned_to_users";
		return $this->addCategory();
	}
	
	public function addPaymentCategory() {
		$this->tableName = "payment_methods_assigned_to_users";
		return $this->addCategory();
	}

}
