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
	
	/** Current Month Interval
     * @return array $period with two dates: first day and last day of current month
     */
	public static function currentMonthPeriod() {
		$period=[];
		$now=new \DateTime();
		$period['start']=$now->format('Y-m-01');
		$period['end']=$now->format('Y-m-t');
		return $period;
	}
	
	/** Last Month Interval
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
	
	/** Custom Month Interval
     * @return array $period with two dates: first day and last day of custom month
     */
	public static function customMonthPeriod($date) {
		$period = [];
		$day = new \DateTime($date);
		$period['start'] = $day->format('Y-m-01');
		$period['end'] = $day->format('Y-m-t');
		return $period;
	}
	
	/** Current Year Interval
     * @return array $period with two dates: first day and last day of current year
     */
	public static function currentYearPeriod() {
		$period=[];
		$now=new \DateTime();
		$period['start']=$now->format('Y-01-01');
		$period['end']=$now->format('Y-12-31');
		return $period;
	}
	
	
	/** Set correct sequence of period dates
	 * @return assoc array $outPeriod [start, end] format("Y-m-d")
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
	
	/** Convert period date for ribbon content 
	 * @param array $inPeriod, with two string dates in format('Y-m-d')
     * @return array $outPeriod, with two string dates in format('d.m.Y')
     */
	public static function dottedDate($inPeriod) {
		$startDate = new \DateTime($inPeriod['start']);
		$endDate = new \DateTime($inPeriod['end']);
		$outPeriod = [];
		$outPeriod['start'] = $startDate->format('d.m.Y');
		$outPeriod['end'] = $endDate->format('d.m.Y');
		return $outPeriod;
	}
	
	/** Check if date is correct
	 * @param string $date
	 * @return mixed string with explanation of incorrectness or false if $date is correct
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
