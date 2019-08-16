<?php

namespace App\Models\DataProperties;

use \App\Auth;
use \App\Models\Data;

/**
 * User model
 * PHP version 7.0
 */
class ShowBalance extends \Core\Model
{	
	//public $incomeSums;
	//public $expenseSums;
	//public $pieChartExpenseSums;
	//public $balanceValue;
	//public $motivationInfo;
	/**
     * Class constructor
     * @param assoc array $period [start, end]
     * @return void
     */
    public function __construct($period) {
		$this->balanceValue = 0;
		$this->incomeSums = $this->loadIncomeSums($period);
		$this->expenseSums = $this->loadExpenseSums($period);
		$this->pieChartExpenseSums = Data::getExpenseSums($period);
		$this->motivationInfo = $this->loadMotivationText($this->balanceValue);
    }
	
	
	/** Create table with all user income categories sums also empty ones
	 * @return assoc array $allIncomeSums [name, id, sum]
	 */
	protected function loadIncomeSums($period) {
		$incomeCategories = Data::getUserIncomeCats();
		//$allIncomeSums = Data::getIncomeSums($period);
		$allIncomeSums = Data::createIncomeArray($period);
		foreach ($allIncomeSums as $iSums) {
			$this->balanceValue += $iSums['sum'];
		}
		return $this->addZeroSums($incomeCategories, $allIncomeSums);
	}
	
	/** Create table with all user expense categories sums also empty ones
	 * @return assoc array $allExpenseSums [name, id, sum]
	 */
	protected function loadExpenseSums($period) {
		$expenseCategories = Data::getUserExpenseCats();
		$allExpenseSums = Data::getExpenseSums($period);
		foreach ($allExpenseSums as $eSums) {
			$this->balanceValue -= $eSums['sum'];
		}
		return $this->addZeroSums($expenseCategories, $allExpenseSums);
	}
	
	/** Fill array with user categories not occuring in $sumsArray
	 * @return assoc array $sumsArray [name, id, sum]
	 */
	protected function addZeroSums($categoriesArray, $sumsArray) {
		foreach($categoriesArray as $ca) {
			//search the $categoriesArray for a every category assigned to users
			$key = array_search($ca['name'], array_column($sumsArray, 'name'));
			//that way below, because of [0] in array; isset, isnull, empty was useless here
			if(strlen((string)$key)==0) { 
				$temp_array=array( 'name' => $ca['name'], 'id' => $ca['id'], 'sum' => 0.00, 'list' => NULL);
				array_push($sumsArray, $temp_array);
			}
			unset($key);
		}
		return $sumsArray;
	}
	
	/** Specify the motivation div
	 * @return assoc array $text ([0] - style, [1] - first span, [2] - second span)
	 */
	protected function loadMotivationText($value) {
		$text=[];
		if ($value >= 0) {
			$text[0]='';
			$text[1]='Gratulacje.';
			$text[2]='Świetnie zarządzasz finansami!';
		} else {
			$text[0]='color:red';
			$text[1]='Uważaj,';
			$text[2]='wpadasz w długi!';
		}
		return $text;
	}
	
	/** 
	 * @return 
	 *
	protected function addIncomeLists($allIncomeSums) {
		foreach($allIncomeSums as $key => $value) {
			$allIncomeSums[$key]['list']='existIncomeList';
		}
		return $allIncomeSums;
	}
	
	/** 
	 * @return 
	 *
	protected function addExpenseLists($allExpenseSums) {
		foreach($allExpenseSums as $key => $value) {
			$allExpenseSums[$key]['list']='existExpenseList';
		}
		return $allExpenseSums;
	}*/
}
