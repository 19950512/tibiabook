<?php


namespace Model\Sites;
USE Model\Core\De AS de;

class Sites {

	public $sites = [];

	function __construct(){
		
		$pathSites = DIR . DS . MODEL . DS . PATH_SITES . DS . 'Sites.json';
		
		if(is_file($pathSites)){
			$this->sites = json_decode(file_get_contents($pathSites), true);
		}
	}
}