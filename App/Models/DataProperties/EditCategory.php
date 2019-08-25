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
		$this->validate();
    }
	
	
	/** 
     * @return boolean True if category is updated
     */
	protected function updateCategoryRecord() {
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
	
	/**
     * Validate name
     * @set $errors[] Strings
     * @return void
     */
	protected function validate() {
		//Name
		if (strlen($this->name) > 50) {
            $this->errors[] = 'Nazwa może zawierać maksymalnie 50 znaków.';
        }
	}
}
