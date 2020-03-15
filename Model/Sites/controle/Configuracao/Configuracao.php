<?php

namespace Model\Sites\Admin\Configuracao;

use Model\Model;
use PDO;

use Model\Core\Core;
use Model\Core\View;
use Model\Core\De as de;
use Model\Validacao\Validacao;

class Configuracao extends Model{

	function __construct(){

		parent::__construct();

		$this->view = new View();
	}

	private function _Configuracao(){

		$sql = $this->conexao->prepare('
			SELECT
			*
			FROM empresa
			WHERE emp_codigo = 1
		');
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			// Manda para o Devlogs
			new Devlogs($sql, '87321', __METHOD__);
			return false;
		}

		// Não contem erro, segue com o script
		$sql = null;

		return $temp;
	}

	private function _update($data){

		if(!is_array($data)){
			return ['r' => 'no', 'data' => 'Porfavor, informe os dados corretamente.'];
		}

		$now = 'now()';

		$sql = $this->conexao->prepare('
			UPDATE empresa SET
				emp_cep = :emp_cep,
				emp_nome = :emp_nome,
				emp_cnpj = :emp_cnpj,
				emp_email = :emp_email,
				emp_cidade = :emp_cidade,
				emp_bairro = :emp_bairro,
				emp_twitter = :emp_twitter,
				emp_celular = :emp_celular,
				emp_telefone = :emp_telefone,
				emp_facebook = :emp_facebook,
				emp_linkedin = :emp_linkedin,
				emp_whatsapp = :emp_whatsapp,
				emp_endereco = :emp_endereco,
				emp_instagram = :emp_instagram,
				emp_codigo = :emp_codigo,
				emp_atualizacao = :emp_atualizacao
			WHERE emp_codigo = :emp_codigo
		');
		$sql->bindParam(':emp_cep', $data['emp_cep']);
		$sql->bindParam(':emp_nome', $data['emp_nome']);
		$sql->bindParam(':emp_cnpj', $data['emp_cnpj']);
		$sql->bindParam(':emp_email', $data['emp_email']);
		$sql->bindParam(':emp_cidade', $data['emp_cidade']);
		$sql->bindParam(':emp_bairro', $data['emp_bairro']);
		$sql->bindParam(':emp_twitter', $data['emp_twitter']);
		$sql->bindParam(':emp_celular', $data['emp_celular']);
		$sql->bindParam(':emp_telefone', $data['emp_telefone']);
		$sql->bindParam(':emp_facebook', $data['emp_facebook']);
		$sql->bindParam(':emp_linkedin', $data['emp_linkedin']);
		$sql->bindParam(':emp_whatsapp', $data['emp_whatsapp']);
		$sql->bindParam(':emp_endereco', $data['emp_endereco']);
		$sql->bindParam(':emp_instagram', $data['emp_instagram']);
		$sql->bindParam(':emp_codigo', $data['emp_codigo']);
		$sql->bindParam(':emp_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Falha ao atualizar os dados
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível alterar os dados no momento, tente novamente mais tarde.'];
		}

		return ['r' => 'ok', 'data' => 'Dados da empresa atualizados com sucesso.'];
	}

	public function update($data){
		return $this->_update($data);
	}

	function valida($data){

		$erro = 'ok';
		$mensagem = '';

		// Valida o E-mail
		if(!Validacao::is_email($data['emp_email'] ?? '')){
			$erro = 'no';
			$mensagem = 'E-mail inválido, tente outro.';
		}

		return ['r' => $erro, 'data' => $mensagem];
	}

	// GETTERS
	public function getConfiguracao(){
		return $this->_Configuracao();
	}
}