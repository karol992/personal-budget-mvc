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
	/**
     * Class constructor
     * @param assoc array $period [start, end]
     * @return void
     */
    public function __construct($period) {
        $this->period = $period;
    }
	
	
	/** Create table with all user income categories sums also empty ones
	 * @return assoc array $allIncomeSums [name, id, sum]
	 */
	public function loadIncomeSums() {
		$incomeCategories = Data::getUserIncomeCats();
		$allIncomeSums = Data::getIncomeSums($this->period);
		return $this->addZeroSums($incomeCategories, $allIncomeSums);
	}
	
	/** Create table with all user expense categories sums also empty ones
	 * @return assoc array $allExpenseSums [name, id, sum]
	 */
	public function loadExpenseSums() {
		$expenseCategories = Data::getUserExpenseCats();
		$allExpenseSums = Data::getExpenseSums($this->period);
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
				$temp_array=array( 'name' => $ca['name'], 0=> $ca['name'],'id' => $ca['id'], 1=> $ca['id'], 'sum' => 0.00,  2=> 0.00 );
				array_push($sumsArray, $temp_array);
			}
			unset($key);
		}
		return $sumsArray;
	}
}
