<?php

namespace Model;

use Model\Core\De AS de;

class Sessions {
	
	public function __construct($dominio = 'tibiabook.local'){

		session_save_path(DIR.'/Sessions/');
		session_set_cookie_params(99999999, '/', $dominio);
		ini_set('session.cookie_domain', '.'.$dominio);
		session_start();
	}
}