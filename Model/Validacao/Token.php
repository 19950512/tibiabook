<?php


namespace Model\Validacao;
USE Model\Core\De AS de;

class Token {

	public $token;

	function __construct(){
	}

	function generator($nome_formulario){
		$this->token = substr(base64_encode($nome_formulario.time().$nome_formulario), 10, 40);
		$_SESSION[SESSION_TOKEN][$nome_formulario] = $this->token;
	}

	function get($nome_formulario){
		return $_SESSION[SESSION_TOKEN][$nome_formulario] ?? false;
	}
}