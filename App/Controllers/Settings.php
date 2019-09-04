<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Data;
use \App\Models\DataProperties\AddCategory;
use \App\Models\DataProperties\EditCategory;
use \App\Models\DataProperties\RemoveCategory;
use \App\Models\DataProperties\DataCleaner;
use \App\Models\User;
use \App\Auth;
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
			'paymentCategories' => Data::getUserPaymentCats(),
			'user' => Auth::getUser()
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
	
	/**
     * Add payment category
     * @return void
     */
    public function removeIncomeCategoryAction() {
		$remove = new RemoveCategory($_POST);
		$remove->removeIncomeCategory();
		$this->redirect('/settings/index');
    }
	
	/**
     * Add payment category
     * @return void
     */
    public function removeExpenseCategoryAction() {
		$remove = new RemoveCategory($_POST);
		$remove->removeExpenseCategory();
		$this->redirect('/settings/index');
    }
	
	/**
     * Add payment category
     * @return void
     */
    public function removePaymentCategoryAction() {
		$remove = new RemoveCategory($_POST);
		$remove->removePaymentCategory();
		$this->redirect('/settings/index');
    }
	
	/**
     * Change name for logged user
     * @return void
     */
    public function changeNameAction() {
		$user = new User($_POST);
		if($user->updateName()) {
			Flash::addMessage('Imię zaktualizowane.');
		}
		$this->redirect('/settings/index');
    }
	
	/**
     * Change email for logged user
     * @return void
     */
    public function changeEmailAction() {
		$user = new User($_POST);
		if ($user->updateEmail()) {
			Flash::addMessage('Email zaktualizowany.');
		}
		$this->redirect('/settings/index');
    }
	
	/**
     * Change password for logged user
     * @return void
     */
    public function changePasswordAction() {
		$oldPasswordConfirm = User::authenticate(Auth::getUserEmail(),$_POST['oldpassword']);
		if($oldPasswordConfirm) {
			$user = new User($_POST);
			if ($user->updatePassword()) {
				Flash::addMessage('Hasło zaktualizowane.');
			} else {
				foreach($user->errors as $error) {
					Flash::addMessage($error);
				};
			}
		} else {
			Flash::addMessage('Podano złe hasło');
		}
		$this->redirect('/settings/index');
    }
	
	/**
     * Remove all user data and account
     * @return void
     */
	public function deleteAccountAction() {
		$passwordConfirm = User::authenticate(Auth::getUserEmail(),$_POST['password']);
		if($passwordConfirm) {
			DataCleaner::removeAccount();
			$this->redirect('/logout');
		} else {
			Flash::addMessage('Podano złe hasło.');
		}
		$this->redirect('/settings/index');
	}
}
