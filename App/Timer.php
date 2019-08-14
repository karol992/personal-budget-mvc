<?php

namespace App;

use \App\Flash;

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
		$period['start']=$now->format('Y-m-01');
		$period['end']=$now->format('Y-m-t');
		return $period;
	}
	
	/** 
     * @return array $period with two dates: first day and last day of last month
     */
	public static function lastMonthPeriod() {
		$period=[];
		$now=new \DateTime();
		$now->modify('-1 month');
		$period['start']=$now->format('Y-m-01');
		$period['end']=$now->format('Y-m-t');
		return $period;
	}
	
	/** 
     * @return array $period with two dates: first day and last day of current year
     */
	public static function currentYearPeriod() {
		$period=[];
		$now=new \DateTime();
		$period['start']=$now->format('Y-01-01');
		$period['end']=$now->format('Y-12-31');
		return $period;
	}
	
	
	/**
	 */
	 public static function bindUserPeriod($start, $end) {
		$outPeriod = [];
		$startDT = new \DateTime($start);
		$endDT = new \DateTime($end);
		if ($startDT->format("Y-m-d")  > $endDT->format("Y-m-d")) {
			$temp = $start;
			$start = $end;
			$end = $temp;
			Flash::addMessage('Odwrócono kolejność dat.', 'info');
		}
		$outPeriod['start'] = $start;
		$outPeriod['end'] = $end;
		return $outPeriod;
	 }
	
	/**
	 * @param array $inPeriod, with two string dates in format('Y-m-d')
     * @return array $outPeriod, with two string dates in format('d.m.Y')
     */
	//convert period date for ribbon content
	public static function dottedDate($inPeriod) {
		$startDate = new \DateTime($inPeriod['start']);
		$endDate = new \DateTime($inPeriod['end']);
		$outPeriod = [];
		$outPeriod['start'] = $startDate->format('d.m.Y');
		$outPeriod['end'] = $endDate->format('d.m.Y');
		return $outPeriod;
	}
	
	/**
	 */
	public static function dateValidation($date) {
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) == 0) {
            return 'Poprawny format daty to YYYY-MM-DD';
        } else if (!static::checkDateExist($date, 'Y-m-d')) {
			return 'Data nie istnieje.';
		}
		return false;
	}
	
		/** Check if date exist
	 * Source : https://www.php.net/manual/en/function.checkdate.php#113205
	 * @return bolean True when date is correct, false otherwise
	 */
	public static function checkDateExist($date, $format)
	{
		$d = \DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}
