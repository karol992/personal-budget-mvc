<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Auth;

/**
 * Account controller
 * PHP version 7.0
 */
class Account extends \Core\Controller
{

    /**
     * Validate if email is available (AJAX) for a new signup
     * @return void
     */
    public function validateEmailAction() {
        $is_valid = ! User::emailExists($_GET['email']);
		header('Content-Type: application/json');
		echo json_encode($is_valid);
    }
	
	/**
     * Validate if email is available (AJAX) for a change
     * @return void
     */
    public function validateChangingEmailAction() {
		$is_valid = true;
		if($_GET['email'] != Auth::getUserEmail()) {
			$is_valid = ! User::emailExists($_GET['email']);
		}
		header('Content-Type: application/json');
		echo json_encode($is_valid);
    }
	
	/**
     * Validate if password is correct (AJAX) for password change
     * @return void
     */
   /* public function passwordConfirmAction() {
		$is_valid = 0;
//		= ! User::authenticate(Auth::getUserEmail(),$_GET['oldpassword']);
		header('Content-Type: application/json');
		echo json_encode($is_valid);
    }*/
	
}
