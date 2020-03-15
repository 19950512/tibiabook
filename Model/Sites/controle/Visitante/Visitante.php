<?php

namespace Model\Sites\Admin\Visitante;

use Model\Model;
use PDO;
use Model\Core\De as de;
use Model\Core\Core;

class Visitante extends Model{

	/**
	**	@see LÓGICA para adicionar um novo visitante.
	** - Verificar se na session['vis_nome'] || session['vis_email'] contém algum nome, quando houver é porque o usuário preencheu algum formulário e se identificou
	** - Consultar no DB se existe alguem com esse nome ou email.
	**		- se tiver, atualizar os dados
	**		- se não tiver, cadastrar um novo visitante
	**/

	function __construct(){
		parent::__construct();

		// Suposto Novo visitante
		if(isset($_SESSION[SESSION_VISITANTE]['vis_email']) and !empty($_SESSION[SESSION_VISITANTE]['vis_email'])){

			// Sincronizar as parada
			$this->sync();
		}
	}

	public function sync(){
		// Verificar se existe alguem já com esse email
		$visitante = $this->_getVisitante($_SESSION[SESSION_VISITANTE]['vis_email']);

		// Adiciona novo visitante
		if($visitante['r'] == 'no'){
		
			return $this->_putVisitante();
		
		// Atualiza o visitante
		}elseif($visitante['r'] == 'ok'){

			return $this->_updateVisitante($_SESSION[SESSION_VISITANTE]['vis_email']);
		}
	}

	public function setAvatar($vis_email = '', $vis_avatar = ''){
		return $this->_updateAvatar($vis_email, $vis_avatar);
	}

	// GETTERS
	public function getVisitante($vis_email){
		return $this->_getVisitante($vis_email);
	}
	public function getVisitantes(){
		return $this->_getVisitantes();
	}
	public function getData($vis_email = ''){
		return $this->_getData($vis_email);
	}
	public function getAuthentica($vis_email = '', $vis_senha = ''){
		return $this->_authentica($vis_email, $vis_senha);
	}


	private function _getData($vis_email){

		// vis_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				*
			FROM visitante AS vis
			WHERE vis_email = :vis_email
		');
		$sql->bindParam(':vis_email', $vis_email);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não encontrado (então é um novo visitante)
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, o visitante não foi encontrado.'];
		}

		return ['r' => 'ok', 'data' => $temp];
	}

	private function _authentica($vis_email, $vis_senha){

		// vis_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				*
			FROM visitante AS vis
			WHERE vis_email = :vis_email AND vis_senha = :vis_senha
		');
		$sql->bindParam(':vis_email', $vis_email);
		$sql->bindParam(':vis_senha', $vis_senha);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não encontrado (então é um novo visitante)
		if($temp === false){
			sleep(2);
			return ['r' => 'no', 'data' => 'Ops, você errou sua senha ou seu e-mail.'];
		}

		$_SESSION[SESSION_VISITANTE]['vis_codigo'] = $temp['vis_codigo'];
		$_SESSION[SESSION_VISITANTE]['vis_email'] = $temp['vis_email'];
		$_SESSION[SESSION_VISITANTE]['vis_nome'] = $temp['vis_nome'];
		$_SESSION[SESSION_VISITANTE]['vis_tel'] = $temp['vis_tel'];
		$_SESSION[SESSION_VISITANTE]['vis_cel'] = $temp['vis_cel'];
		$_SESSION[SESSION_VISITANTE]['vis_senha'] = $temp['vis_senha'];
		$_SESSION[SESSION_VISITANTE]['vis_avatar'] = $temp['vis_avatar'];

		return ['r' => 'ok', 'data' => 'Cliente identificado, logando..'];
	}


	// Insere um novo visitante
	// return 'no' => 'Não cadastrado'
	// return 'ok' => 'Cadastrado com sucesso'
	private function _putVisitante(){

		$vis_email = $_SESSION[SESSION_VISITANTE]['vis_email'];
		$vis_senha = $_SESSION[SESSION_VISITANTE]['vis_senha'];

		// Check se exist visitante com esse email
		$sql = $this->conexao->prepare('
			SELECT vis_email FROM visitante WHERE vis_email = :vis_email
		');
		$sql->bindParam(':vis_email', $vis_email);
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não cadastrado
		if($temp){     
			return ['r' => 'no', 'data' => 'Irmão, já existe um cadastro com esse e-mail.'];
		}

		$sql = null;

		$vis_ip = Core::ip();

		$sql = $this->conexao->prepare('
			INSERT INTO visitante (
				vis_nome,
				vis_tel,
				vis_cel,
				vis_email,
				vis_senha,
				vis_ip
			) VALUES (
				:vis_nome,
				:vis_tel,
				:vis_cel,
				:vis_email,
				:vis_senha,
				:vis_ip
			)
		');
		$sql->bindParam(':vis_nome', $_SESSION[SESSION_VISITANTE]['vis_nome']);
		$sql->bindParam(':vis_tel', $_SESSION[SESSION_VISITANTE]['vis_tel']);
		$sql->bindParam(':vis_cel', $_SESSION[SESSION_VISITANTE]['vis_cel']);
		$sql->bindParam(':vis_email', $vis_email);
		$sql->bindParam(':vis_senha', $vis_senha);
		$sql->bindParam(':vis_ip', $vis_ip);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não cadastrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Visitante não cadastrado.'];
		}

		return ['r' => 'ok', 'data' => 'Visitante cadastrado com sucesso.'];
	}

	// Busca informações de um visitante apartir de um e-mail
	// return 'no' => 'Não encontrado'
	// return 'ok' => 'Encontrado'
	private function _getVisitante($vis_email){

		// vis_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT 
				*
			FROM visitante AS vis
			WHERE vis_email = :vis_email
		');
		$sql->bindParam(':vis_email', $vis_email);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não encontrado (então é um novo visitante)
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Visitante não encontrado (então é um novo visitante)'];
		}

		return ['r' => 'ok', 'data' => 'Visitante encontrato, chama-se '. $temp['vis_nome']];
	}

	// Busca informações de todos os visitantes
	// return 'no' => 'Não encontrado'
	// return 'ok' => 'Encontrado'
	private function _getVisitantes(){

		// vis_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				*
			FROM visitante AS vis
			ORDER BY vis.vis_codigo DESC, vis.vis_nome ASC
		');
		$sql->execute();
		$temp = $sql->fetchAll(PDO::FETCH_ASSOC);

		// Visitante não encontrado (então é um novo visitante)
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Visitante não encontrado (então é um novo visitante)'];
		}

		return ['r' => 'ok', 'data' => $temp];
	}

	// Atualizar visitante (ultima visita)
	// return 'no' => 'Não atualizou'
	// return 'ok' => 'Atualizou com sucesso'
	private function _updateVisitante($vis_email){

		$vis_ip = Core::ip();
		$sql = $this->conexao->prepare("
			UPDATE visitante SET 
				vis_atualizacao = 'now()',
				vis_ip = :vis_ip,
				vis_nome = :vis_nome,
				vis_tel = :vis_tel,
				vis_cel = :vis_cel
			WHERE vis_email = :vis_email 
		");
		$sql->bindParam(':vis_email', $_SESSION[SESSION_VISITANTE]['vis_email']);
		$sql->bindParam(':vis_ip', $vis_ip);
		$sql->bindParam(':vis_nome', $_SESSION[SESSION_VISITANTE]['vis_nome']);
		$sql->bindParam(':vis_tel', $_SESSION[SESSION_VISITANTE]['vis_tel']);
		$sql->bindParam(':vis_cel', $_SESSION[SESSION_VISITANTE]['vis_cel']);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não cadastrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Visitante não cadastrado.'];
		}

		return ['r' => 'ok', 'data' => 'Visitante cadastrado com sucesso.'];
	}

	private function _updateAvatar($vis_email = '', $vis_avatar = ''){

		$vis_ip = Core::ip();
		$sql = $this->conexao->prepare("
			UPDATE visitante SET 
				vis_atualizacao = 'now()',
				vis_avatar = :vis_avatar
			WHERE vis_email = :vis_email
		");
		$sql->bindParam(':vis_avatar', $vis_avatar);
		$sql->bindParam(':vis_email', $vis_email);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Visitante não cadastrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não deu para atualizar o avatar.'];
		}

		$_SESSION[SESSION_VISITANTE]['vis_avatar'] = $vis_avatar;

		return ['r' => 'ok', 'data' => 'Imagem de perfil alterada com sucesso.'];
	}
}