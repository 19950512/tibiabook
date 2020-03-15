<?php


namespace dosamigosmetalurgica\Controller\Contato;

use dosamigosmetalurgica\Controller\Controller;
use Model\Core\De as de;
use Model\Email\Email;

class Contato extends Controller
{

	protected $controller = 'Contato';

	public function __construct()
	{
		parent::__construct();
	}

	public function index(){

		$this->viewName = 'Contato';
		
		$this->view->setTitle('Titulo do Contato');
		$this->view->setHeader([
			['name' => 'robots', 'content' => 'follow, index'],
			['name' => 'description', 'content' => 'Contato']
		]);

		$mustache = array(
			'{{pushResposta}}' => ' OK, Maestro'
		);
		
		// Render View
		$this->render($mustache, $this->controller, $this->viewName, $this->view->header);
	}

	public function enviar(){

		if(isset($_POST['token']) and $_POST['token'] === 'teste'){

			$vis_nome		 	= $_POST['nome'] ?? '';
			$vis_telefone	 	= $_POST['telefone'] ?? '';
			$vis_celular	  	= $_POST['celular'] ?? '';
			$vis_email			= $_POST['email'] ?? '';
			$vis_mensagem	 	= $_POST['mensagem'] ?? '';

			// Seta novos dados do visitante
			$_SESSION[SESSION_VISITANTE] = [
				'vis_nome' => $vis_nome,
				'vis_tel' => $vis_telefone,
				'vis_cel' => $vis_celular,
				'vis_email' => $vis_email,
				'vis_ip' => $_SERVER['REMOTE_ADDR'],
			];

			// Sincroniza o visitante, atualiza se já existe ou registra um novo
			//$this->visitante->sync();
			
			$Email = new Email(CONFIG_EMAIL);

			$enviar['from'] = CONFIG_EMAIL['from'];
			$enviar['nome'] = 'Matheus Maydana';
			$enviar['para'] = ['mattmaydana@gmail.com' => 'Jamais'];
			$enviar['comoCopiaOculta'] = ['developer.web42@gmail.com' => 'DevNux'];
			$enviar['assunto'] = 'Teste';
			$enviar['body'] = '<h1>Teste =D</h1>';

			$enviarEmail = $Email->enviar($enviar); 

			$resposta = ($enviarEmail) ? ['r' => 'ok', 'data' => 'Feito, mensagem enviada com sucesso.'] : ['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.'];

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}
}