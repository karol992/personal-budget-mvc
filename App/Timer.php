<?php

namespace App;


/**
 * 
 * PHP version 7.0
 */
class Timer {
	/** Get Current Date
     * @return string (Y-m-d)
     */
    public static function getCurrentDate() {
		$now=new \DateTime();
		return $now->format('Y-m-d');
    }
	
	/** 
     * @return array $period with two dates: first day and last day of current month
     */
	public static function currentMonthPeriod() {
		$period=[];
		$now=new \DateTime();
		$period=$now->format('Y-m-01');
		$period=$now->format('Y-m-t');
		return $period;
	}
	
	/** 
     * @return array $period with two dates: first day and last day of last month
     */
	public static function lastMonthPeriod() {
		$period=[];
		$now=new \DateTime();
		$now->modify('-1 month');
		$period=$now->format('Y-m-01');
		$period=$now->format('Y-m-t');
		return $period;
	}
	
	/** 
     * @return array $period with two dates: first day and last day of current year
     */
	public static function currentYearPeriod() {
		$period=[];
		$now=new \DateTime();
		$period=$now->format('Y-01-01');
		$period=$now->format('Y-12-31');
		return $period;
	}
	
	/**
	 * @param array $inPeriod, with two string dates in format('Y-m-d')
     * @return array $outPeriod, with two string dates in format('d.m.Y')
     */
	//convert period date for ribbon content
	public static function dottedDate($inPeriod) {
		$startDate = new \DateTime($inPeriod[0]);
		$endDate = new \DateTime($inPeriod[1]);
		$outPeriod = [];
		$outPeriod = $startDate->format('d.m.Y');
		$outPeriod = $endDate->format('d.m.Y');
		return $outPeriod;
	}
}
