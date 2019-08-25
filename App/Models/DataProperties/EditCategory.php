<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;
use \App\Flash;

/**
 * UpdateIncome model
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
     * @param array $data  Initial property values
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
	protected function validateName() {
		if (strlen($this->name) > 50) {
            $this->errors[] = 'Błąd: nazwa może zawierać maksymalnie 50 znaków.';
        } else if (strlen($this->name) < 3) {
            $this->errors[] = 'Błąd: nazwa musi zawierać przynajmniej 3 znaki.';
        }
		
		$userCategories = [];
		$userCategories = Data::getUserCategories($this->table);
		foreach($userCategories as $category) {
			if (strtolower($category['name']) == strtolower($this->name)) {
				if ($category['id'] != $this->id)
					$this->errors[] = 'Błąd: istnieje już kategoria '.$category['name'].'.';
			}
		}
		
		if (preg_match('/^[a-z]+$/i', $this->name) == 0) {
            $this->errors[] = 'Błąd: nazwa może zawierać tylko litery.';
        }
		
		foreach ($this->errors as $error) {
			Flash::addMessage($error, 'warning');
		}
	}
	
	/** 
     * @return boolean True if category is updated
     */
	protected function updateCategoryRecord() {
		$this->validateName();
		if(empty($this->errors)) {
			$db = static::getDB();
			$query = $db->prepare("UPDATE ".$this->table." tb SET name = :name WHERE tb.id = :id;");
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			$query->execute();
		}
	}
	
	public function updateIncomeCategory() {
		$this->table = 'incomes_category_assigned_to_users';
		return $this->updateCategoryRecord();
	}
	
	public function updateExpenseCategory() {
		$this->table = 'expenses_category_assigned_to_users';
		return $this->updateCategoryRecord();
	}
	
	public function updatePaymentCategory() {
		$this->table = 'payment_methods_assigned_to_users';
		return $this->updateCategoryRecord();
	}
	
	/** Add new category
	*/
	/*protected function addCategory($table, $name) {
		//$table = "expenses_category_assigned_to_users";
		//$table = "payment_methods_assigned_to_users";
		$this->validateName();
		$sql = "INSERT INTO ".$table." (id, user_id, name) VALUES (NULL, :user_id, :name)";
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
		$query->bindValue(':name', $name, PDO::PARAM_STR);
		return $query->execute();
	}
	
	public function addIncomeCategory($name) {
		$table = "incomes_category_assigned_to_users";
		
		
		return $this->addCategory($table, $name);
	}*/

}
