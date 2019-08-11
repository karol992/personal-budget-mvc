<?php

namespace Core;

use \App\Auth;
use \App\Flash;

/**
 * Base controller
 * PHP version 7.0
 */
abstract class Controller {
    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     * @param array $route_params Parameters from the route
	 * @return void
     */
    public function __construct($route_params) {
		$this->route_params = $route_params;
	}
	
	/**
	  * Call
	  */
	public function __call($name, $args) {
		$method = $name.'Action';
		if(method_exists($this, $method)) {
			if ($this->before() !== false) {
				call_user_func_array([$this, $method], $args);
				$this->after();
			} else {
				//echo "Method $method not found in controller  ".get_class($this);
				throw new \Exception("Method $method not found in controller ".get_class($this));
			}
		}
	}
	  
	/**
	 * Before filter - called before an action method.
	 * @return void
	 */
	protected function before() {
	}
	  
	/**
	 * After filter - called after an action method.
	 * @return void
	 */
	protected function after() {
	}
	  
	/**
	 * Redirect to the different page
	 * @param string $url The relative URL
	 * @return void
	 */
	public function redirect($url) {
		header('Location: http://'.$_SERVER['HTTP_HOST'].$url, true, 303);
		exit;
	}
	
	/**
	 * Require the user to be logged in before giving access to the requested page.
	 * Remember the requested page for later, then redirect to the login page.
	 * @return void
	 */
	public function requireLogin() {
		if (! Auth::getUser()) {
			Flash::addMessage('Zaloguj się, aby uzyskać dostęp do strony.', Flash::INFO);
			Auth::rememberRequestedPage();
			$this->redirect('/login');
		}
	}
	
}
