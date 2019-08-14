<?php

namespace Core;

/** View
 * PHP version 7.0
 */
class View {
	/** Render a view file
	 * @param string $view The view file
	 * @return void
	 */
	public static function render($view, $args = []) {
		extract($args, EXTR_SKIP);
		$file = "../App/Views/$view"; // relative to Core directory
		if (is_readable($file)) {
			require $file;
		} else {
			throw new \Exception("$file not found.");
		}
	}
	
	/** Render a view template using Twig
	 * @param string $template The template file
	 * @param array $args Associative array of data to display in the view (optional)
	 * @return void
	 */
	public static function renderTemplate($template, $args = []) {
        static $twig = null;
        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
			/*$twig->addGlobal('income_cats', \App\Models\Data::getUserIncomeCats());
			$twig->addGlobal('expense_cats', \App\Models\Data::getUserExpenseCats());
			$twig->addGlobal('payment_cats', \App\Models\Data::getUserPaymentCats());
			$twig->addGlobal('income_date', $_POST['income_date'] ?? false);
			$twig->addGlobal('expense_date', $_POST['expense_date'] ?? false);
			$twig->addGlobal('transaction_date', $_SESSION['transaction_date'] ?? \App\Timer::getCurrentDate());*/
			$twig->addGlobal('flash_messages', \App\Flash::getMessages());
        }
        echo $twig->render($template, $args);
    }	
}