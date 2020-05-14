<?php

namespace Model\Login;

use Model\Core\De as de;
use Model\Core\Core;
use Model\Model;
use PDO;
use PDOException;

class Login extends Model{

	function __construct(){
		parent::__construct();
	}

	/**
	** @see	
	** $data = [
	**		'co_id' => 'xxx',
	**		'co_senha' => 'xxx',
	**	];
	**/
	function login($data = []){

		if(!is_array($data)){
			return ['res' => 'no', 'data' => 'Vish, eu preciso de dados irmão...'];
		}

		$data['co_senha_hash'] = Core::base64_encode($data['co_senha']);

		// Verifica se já não existe alguma conta com esse email
		$sql = $this->conexao->prepare('SELECT * FROM conta WHERE co_email = :co_email AND co_senha = :co_senha');
		$sql->bindParam(':co_email', $data['co_email']);
		$sql->bindParam(':co_senha', $data['co_senha_hash']);
		$sql->execute();
		$fetch = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// Se errou a senha
		if($fetch == false){
			return ['res' => 'no', 'data' => 'Ops, e-mail ou senha incorreto.'];
		}

		if($fetch['co_status'] !== 1){
			return ['res' => 'no', 'data' => 'Ops, parece que sua conta não está ativa.'];
		}

		$ip = Core::ip();

		// Altera o momento que está logando
		$sql = $this->conexao->prepare("UPDATE conta SET co_atualizacao = 'now()', co_ip = :co_ip WHERE co_codigo = :co_codigo");
		$sql->bindParam(':co_codigo', $fetch['co_codigo']);
		$sql->bindParam(':co_ip', $ip);
		$temp = $sql->execute();

		// Salva na sessão todas informacoes do usuario logado
		$_SESSION[SESSION_LOGIN] = $fetch;
	}

	/**
	** @see	
	** $data = [
	**		'co_id' => 'xxx',
	**		'co_senha' => 'xxx',
	**	];
	**/
	function create($data = []){

		if(!is_array($data)){
			return ['res' => 'no', 'data' => 'Vish, eu preciso de dados irmão...'];
		}

		// Verifica se já não existe alguma conta com esse email
		$sql = $this->conexao->prepare('SELECT co_codigo FROM conta WHERE co_email = :co_email');
		$sql->bindParam(':co_email', $data['co_email']);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// Se já existir uma conta com esse email
		if(isset($temp['co_codigo']) and is_numeric($temp['co_codigo'])){
			return ['res' => 'no', 'data' => 'Ops, já existe uma conta com este e-mail, tente outro.'];
		}

		$data['co_senha_hash'] = Core::base64_encode($data['co_senha']);
		$ip = Core::ip();
		
		// Não existe, então vamos criar uma nova.
		$sql = $this->conexao->prepare('
			INSERT INTO conta (
				co_email,
				co_senha,
				co_ip
			) VALUES (
				:co_email,
				:co_senha,
				:co_ip
			) 
		');
		$sql->bindParam(':co_email', $data['co_email']);
		$sql->bindParam(':co_senha', $data['co_senha_hash']);
		$sql->bindParam(':co_ip', $ip);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// Erro
		if($temp === false){
			return ['res' => 'no', 'data' => 'Ops, tente novamente mais tarde.'];
		}

		// Parece que a conta foi criada com sucesso, então tenta já logar nela.
		$this->login($data);

		return ['res' => 'ok', 'data' => 'Pronto, sua conta foi criada com sucesso.'];
	}

	static public function encode($senha = ''){
		return Core::base64_encode($senha);
	}
	static public function decode($senha = ''){
		return Core::base64_decode($senha);
	}
}