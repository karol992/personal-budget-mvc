<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\DataProperties\AddCategory;
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
			'incomeCategories' => Data::getUserIncomeCats(),
			'expenseCategories' => Data::getUserExpenseCats(),
			'paymentCategories' => Data::getUserPaymentCats()
		]);
    }

    /**
     * Edit income category
     * @return void
     */
    public function editIncomeCategoryAction() {
		$edit = new EditCategory($_POST);
		$edit->updateIncomeCategories();
		$this->redirect('/settings/index');
    }
	
	/**
     * Edit expense category
     * @return void
     */
    public function editExpenseCategoryAction() {
        $edit = new EditCategory($_POST);
		$edit->updateExpenseCategories();
		$this->redirect('/settings/index');
    }
	
	/**
     * Edit payment category
     * @return void
     */
    public function editPaymentCategoryAction() {
        $edit = new EditCategory($_POST);
		$edit->updatePaymentCategories();
		$this->redirect('/settings/index');
    }
	
	/**
     * Add income category
     * @return void
     */
    public function addIncomeCategoryAction() {
        $edit = new AddCategory($_POST);
		$edit->addIncomeCategory();
		$this->redirect('/settings/index');
    }

	/**
     * Add expense category
     * @return void
     */
    public function addExpenseCategoryAction() {
        $edit = new AddCategory($_POST);
		$edit->addExpenseCategory();
		$this->redirect('/settings/index');
    }
	
	/**
     * Add payment category
     * @return void
     */
    public function addPaymentCategoryAction() {
        $edit = new AddCategory($_POST);
		$edit->addPaymentCategory();
		$this->redirect('/settings/index');
    }
}
