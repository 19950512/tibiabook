<?php

namespace tibiabook\Controller\Login;

use tibiabook\Controller\Controller;
use Model\Core\View as View;
use Model\Core\De as De;
use Model\Core\Core;
use Model\Validacao\Token;
use Model\Login\Login as ModelLogin;

class Login extends Controller {

	protected $controller = 'Login';

	public function __construct(){
		parent::__construct();
	}

	public function index(){

		$this->somenteLogin();

		$this->viewName = 'Login';

		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'DevNux'],
		]);

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}


	public function criar(){

		$this->somenteLogin();

		$this->viewName = 'Criar';

		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'DevNux'],
		]);

		$token = new Token;
		$token->generator('Criar');

		$mustache = [
			'{{token}}' => $token->token,
		];

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	public function recuperar(){

		$this->somenteLogin();

		$this->viewName = 'Recuperar';

		$this->view->setHeader([
			['name' => 'robots', 'content' => 'noindex, nofollow'],
			['name' => 'author', 'content' => 'DevNux'],
		]);

		$mustache = array();

		// Render View
		$this->render($mustache, $this->controller, $this->viewName);
	}

	private function somenteLogin(){
		if(isset($_SESSION[SESSION_LOGIN]['co_codigo']) and is_numeric($_SESSION[SESSION_LOGIN]['co_codigo'])){
			header('location: /');
		}
	}


	function logout(){
		if(isset($_SESSION[SESSION_LOGIN]['co_codigo']) and is_numeric($_SESSION[SESSION_LOGIN]['co_codigo'])){
			unset($_SESSION[SESSION_LOGIN]);

			echo json_encode(['res' => 'ok', 'data' => 'Pronto, deslogado com sucesso.']);
			exit;
		}

		echo json_encode(['res' => 'no', 'data' => 'Ops, parece que você não está logado.']);
		exit;
	}

	function create(){

		$token = new Token;

		if(isset($_POST['tokenAuth']) and $_POST['tokenAuth'] == $token->get('Criar')){

			$co_email 		= $_POST['co_email'] ?? '';
			$co_senha 		= $_POST['co_senha'] ?? '';
			
			// Verifica se preencheu o ID e a senha
			if(empty($co_email) || empty($co_senha)){
				echo json_encode(['res' => 'no', 'data' => 'Ops, você precisa preencher o ID e Senha.']);
				exit;
			}

			if(!Core::is_email($co_email)){
				echo json_encode(['res' => 'no', 'data' => 'Ops, você precisa informar um e-mail válido.']);
				exit;
			}

			$login = new ModelLogin();

			$create = $login->create([
				'co_email' => $co_email,
				'co_senha' => $co_senha,
			]);

			echo json_encode($create);
			exit;
		}

		echo json_encode(['res' => 'no', 'data' => 'Ops, parece que você não deveria estar tentando fazer isso.']);
		exit;
	}
}