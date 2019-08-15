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
    public function __construct($period)
    {
        $this->period = $period;
    }
	
	
	/** Create table with all user categories sums also empty ones
	 * @return assoc array $allIncomesSums [name, id, iSum]
	 */
	public function loadIncomeSums()
	{
		$incomeCategories = Data::getUserIncomeCats();
		$allIncomesSums = Data::getIncomesSums($this->period);
		
		foreach($incomeCategories as $ic) {
			//search the $incomes for a every incomes_category_assigned_to_users
			$key = array_search($ic['name'], array_column($allIncomesSums, 'name'));
			//that way below, because of [0] in array; isset, isnull, empty was useless here
			if(strlen((string)$key)==0) { 
				$temp_array=array( 'name' => $ic['name'], 0=> $ic['name'],'id' => $ic['id'], 1=> $ic['id'], 'iSum' => 0.00,  2=> 0.00 );
				array_push($allIncomesSums, $temp_array);
			}
			unset($key);
		}
		return $allIncomesSums;
	}
}
