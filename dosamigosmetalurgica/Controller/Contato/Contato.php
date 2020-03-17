<?php


namespace dosamigosmetalurgica\Controller\Contato;

use dosamigosmetalurgica\Controller\Controller;
use Model\Core\De as de;
use Model\Core\Core;
use Model\Email\Email;

class Contato extends Controller
{

	protected $controller = 'Contato';

	public $visitante;

	public function __construct()
	{
		parent::__construct();

		//$this->visitante = new Visitante();
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

			$vis_nome		 	= Core::strip_tags($_POST['nome'] ?? '');
			$vis_telefone	 	= Core::strip_tags($_POST['telefone'] ?? '');
			$vis_celular	  	= Core::strip_tags($_POST['celular'] ?? '');
			$vis_whats	  		= Core::strip_tags($_POST['whatsapp'] ?? '');
			$vis_email			= Core::strip_tags($_POST['email'] ?? '');
			$vis_cidade			= Core::strip_tags($_POST['cidade'] ?? '');
			$vis_mensagem	 	= Core::strip_tags($_POST['mensagem'] ?? '');

			// Seta novos dados do visitante
			$_SESSION[SESSION_VISITANTE] = [
				'vis_nome' => $vis_nome,
				'vis_tel' => $vis_telefone,
				'vis_cel' => $vis_celular,
				'vis_whats' => $vis_whats,
				'vis_email' => $vis_email,
				'vis_cidade' => $vis_cidade,
				'vis_ip' => Core::ip(),
			];

			// Sincroniza o visitante, atualiza se já existe ou registra um novo
			$this->visitante->sync();
			
			$Email = new Email(CONFIG_EMAIL);

			$enviar['from'] = CONFIG_EMAIL['from'];
			$enviar['nome'] = 'Matheus Maydana';
			$enviar['para'] = ['mattmaydana@gmail.com' => 'Jamais'];
			$enviar['comoCopiaOculta'] = ['developer.web42@gmail.com' => 'DevNux'];
			$enviar['assunto'] = $vis_nome.', deixou uma mensagem';

			$mustache = [
				'{{titulo}}' 		=> $vis_nome.', deixou uma mensagem',
				'{{mensagem}}' 		=> $vis_mensagem,
				'{{vis_nome}}' 		=> $vis_nome,
				'{{vis_email}}' 	=> $vis_email,
				'{{vis_whats}}' 	=> $vis_whats,
				'{{vis_cidade}}' 	=> $vis_cidade,
				'{{ano}}' 			=> Core::date('Y'),
				'{{momento}}' 		=> Core::date('d/m/Y').' às '.Core::date('h:i:s'),
				'{{ip}}' 			=> Core::ip()
			];

			$enviar['body'] = Core::mustache($mustache, $this->view->getLayout('email'));

			$enviarEmail = $Email->enviar($enviar); 

			$enviarEmail = true;
			$resposta = ($enviarEmail) ? ['r' => 'ok', 'data' => 'Feito, mensagem enviada com sucesso.'] : ['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.'];

			echo json_encode($resposta);
			exit;
		}

		echo json_encode(['r' => 'no', 'data' => 'Ops, tente novamente mais tarde.']);
		exit;
	}
}