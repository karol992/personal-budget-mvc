<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\DataProperties\EditCategory;
use \App\Flash;

/**
 * Income controller
 * PHP version 7.0
 */
class Settings extends Authenticated
{

    /**
     * Show the Settings page
     * @return void
     */
    public function indexAction() {
        View::renderTemplate('Settings/index.html', [
			'incomeCategories' => Data::getUserIncomeCats()
		]);
    }

    /**
     * Edit income category
     * @return void
     */
    public function editIncomeCategoryAction() {
        $edit = new EditCategory($_POST);
		if ($edit->action == 'delete') {
			//$edit->deleteIncomeCategory();
		} else if ($edit->action == 'update') {
			$edit->updateIncomeCategory();
		}
    }
	
	/**
     * Edit expense category
     * @return void
     */
    public function editExpenseCategoryAction() {
        $edit = new EditCategory($_POST);
		if ($edit->action == 'delete') {
			//$edit->deleteExpenseCategory();
		} else if ($edit->action == 'update') {
			$edit->updateExpenseCategory();
		}
    }
	
	/**
     * Edit payment category
     * @return void
     */
    public function editPaymentCategoryAction() {
        $edit = new EditCategory($_POST);
		if ($edit->action == 'delete') {
			//$edit->deletePaymentCategory();
		} else if ($edit->action == 'update') {
			$edit->updatePaymentCategory();
		}
    }
	



}
