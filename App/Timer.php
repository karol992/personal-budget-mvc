<?php

namespace App;


/**
 * 
 * PHP version 7.0
 */
class Timer {
	/**
     * Get Current Date
     * @return string Current date Y-m-d
     */
    public static function getCurrentDate() {
		$now=new \DateTime();
		return $now->format('Y-m-d');
    }
}
