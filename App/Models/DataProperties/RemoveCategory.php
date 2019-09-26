<?php

namespace App\Models\DataProperties;

use PDO;
use \App\Models\Data;
use \App\Flash;
use \App\Auth;

/**
 * Remove Category model
 * PHP version 7.0
 */
class RemoveCategory extends \Core\Model
{
	/**
	@var deleteId
	@var transferId
	@message1
	@message2
	*/
	
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
			$this->$key=$value;
        };
	}
	
	protected function deleteCategory() {
		$sql = "DELETE FROM ".$this->categoryTable." WHERE ".$this->categoryTable.".id = :id";
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':id', $this->deleteId, PDO::PARAM_INT);
		return $query->execute();
	}
	
	protected function updateRecords() {
		$sql = "UPDATE ".$this->recordsTable.
			" SET ".$this->categoryKey." = :transferId 
			WHERE ".$this->recordsTable.".".$this->categoryKey." = :id";
		$db = static::getDB();
		$query = $db->prepare($sql);
		$query->bindValue(':transferId', $this->transferId, PDO::PARAM_INT);
		$query->bindValue(':id', $this->deleteId, PDO::PARAM_INT);
		return $query->execute();
	}
	
	protected function execute() {
		$this->validation();
		if(empty($this->errors)) {
			$transferCategoryName = Data::getCategoryName($this->categoryTable, $this->transferId);
			$deleteCategoryName = Data::getCategoryName($this->categoryTable, $this->deleteId);
			if (($this->updateRecords()) && ($this->deleteCategory())) {
				$this->successMessage = 'Kategorię pozycji '.$deleteCategoryName.' zmieniono na '.$transferCategoryName.'. '.
				'Usunięto kategorię: '.$deleteCategoryName.'. ';
				return true;
			}
		}
		return false;
	}
	
	protected function validation() {
		if (($this->deleteId == $this->transferId) && $this->deleteId) {
			$this->errors[] = "Podane kategorie są takie same.";
		}
		if (!$this->deleteId) {
			$this->errors[] = "Nie podano usuwanej kategorii.";
		}
		if (!$this->transferId) {
			$this->errors[] = "Nie podano kategorii zastępczej.";
		}
	}
	
	public function removeIncomeCategory() {
		$this->categoryTable = "incomes_category_assigned_to_users";
		$this->recordsTable = "incomes";
		$this->categoryKey = "income_category_assigned_to_user_id";
		return $this->execute();
	}
	
	public function removeExpenseCategory() {
		$this->categoryTable = "expenses_category_assigned_to_users";
		$this->recordsTable = "expenses";
		$this->categoryKey = "expense_category_assigned_to_user_id";
		return $this->execute();
	}
	
	public function removePaymentCategory() {
		$this->categoryTable = "payment_methods_assigned_to_users";
		$this->recordsTable = "expenses";
		$this->categoryKey = "payment_method_assigned_to_user_id";
		return $this->execute();
	}
}
