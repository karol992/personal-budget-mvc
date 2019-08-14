<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\DataProperties\ShowBalance;

/**
 * Balance controller
 * PHP version 7.0
 */
class Balance extends Authenticated
{

    /**
     * Show the Balance page
     * @return void
     */
    public function indexAction() {
	$args=[];
		if(isset($_POST['balance_period'])) {
			$args['balance_period']=$_POST['balance_period'];
		} else {
			$args['balance_period']='current_month';
		}
        View::renderTemplate('Balance/index.html', $args);
    }

}
