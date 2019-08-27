<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;
use \App\Flash;
use \App\Auth;

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
     * @param assoc array $data  Initial property values
     * @return void
     */
    public function __construct($data = [])
    {
		$this->table = [];
		foreach ($data as $id => $key) {
            foreach ($key as $key=> $value) {
				$this->table[$id][$key]=$value;
			};
        };
    }
	
	/**
     * Validate name
     * @set $errors[] Strings
     * @return void
     */
	protected function checkName($record) {
		$validation = true;
		
		$userCategories = [];
		$userCategories = Data::getUserCategories($this->tableName);
		foreach($userCategories as $category) {
			if (strtolower($category['name']) == strtolower($record['name'])) {
				if ($category['id'] != $record['id']) {
					$this->errors[] = 'Błąd ('.$category['name'].'): istnieje już taka kategoria.';
					$validation = false;
				} else if ($category['name'] == $record['name']) {
					return false; //Do not update because of no change
				}
			}
		};
		
		if (strlen($record['name']) > 50) {
            $this->errors[] = 'Błąd ('.substr($record['name'], 0, 15).'...): nazwa może zawierać maksymalnie 50 znaków.';
			$validation = false;
        } else if (strlen($record['name']) < 3) {
            $this->errors[] = 'Błąd ('.$record['name'].'): nazwa musi zawierać przynajmniej 3 znaki. ';
			$validation = false;
        }
		if (preg_match('/^[a-z ]+$/i', $record['name']) == 0) {
            $this->errors[] = 'Błąd ('.$record['name'].'): nazwa może zawierać tylko litery i spacje.';
			$validation = false;
        }
		return $validation;
	}
	
	/** 
     * @return boolean True if category is updated
     */
	protected function updateCategoryRecords() {
		foreach($this->table as $table=>$id) {
			if ($this->checkName($id)) {
				$db = static::getDB();
				$query = $db->prepare("UPDATE ".$this->tableName." tb SET name = :name WHERE tb.id = :id;");
				$query->bindValue(':name', $id['name'], PDO::PARAM_STR);
				$query->bindValue(':id', $id['id'], PDO::PARAM_INT);
				$query->execute();
			}
		};
		foreach ($this->errors as $error) {
			Flash::addMessage($error, 'warning');
		};
	}
	
	public function updateIncomeCategories() {
		$this->tableName = 'incomes_category_assigned_to_users';
		return $this->updateCategoryRecords();
	}
	
	public function updateExpenseCategories() {
		$this->tableName = 'expenses_category_assigned_to_users';
		return $this->updateCategoryRecords();
	}
	
	public function updatePaymentCategories() {
		$this->tableName = 'payment_methods_assigned_to_users';
		return $this->updateCategoryRecords();
	}
	
	/** Add new category
	*
	protected function addCategory() {
		$this->validateName();
		if(empty($this->errors)) {
			$sql = "INSERT INTO ".$this->tableName." (id, user_id, name) VALUES (NULL, :user_id, :name)";
			$db = static::getDB();
			$query = $db->prepare($sql);
			$query->bindValue(':user_id', Auth::getUserId(), PDO::PARAM_INT);
			$query->bindValue(':name', $this->name, PDO::PARAM_STR);
			$query->execute();
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
	*/
	
}
