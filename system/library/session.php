<?php
class Session {
	public $data = array();

	public function __construct($session_id = '',  $key = 'default') {
		if (!session_id()) {
			/*
			ini_set('session.use_only_cookies', 'On');
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			ini_set('session.cookie_httponly', 'On');
*/
			
			//$HTTP_SERVER = str_replace('https://','',HTTP_SERVER);
			//$HTTP_SERVER = str_replace('http://','',$HTTP_SERVER);
			//$HTTP_SERVER_COOKIE = '.'.str_replace('/','',$HTTP_SERVER);
			//session_set_cookie_params(2*7*24*60*60, "/", $HTTP_SERVER_COOKIE);
			session_set_cookie_params(2*7*24*60*60*100, "/");
			
			/*
			if (isset($_COOKIE[session_name()]) && !preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $_COOKIE[session_name()])) {
				exit();
			}

			if ($session_id) {
				session_id($session_id);
			}

			session_set_cookie_params(0, '/');
			*/
			
			session_start();
		}

		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = array();
		}

		$this->data =& $_SESSION[$key];
	}

	public function getId() {
		return session_id();
	}

	public function start() {
		return session_start();
	}

	public function destroy() {
		return session_destroy();
	}
}
