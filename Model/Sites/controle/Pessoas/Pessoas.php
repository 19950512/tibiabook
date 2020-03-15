<?php

namespace Model\Sites\Admin\Pessoas;

use Model\Core\De as de;
use Model\Model;
use PDO;

class Pessoas extends Model{

	function __construct(){
		parent::__construct();
	}

	// GETTERS
	public function getPessoa($pes_codigo){
		return $this->_getPessoa($pes_codigo);
	}
	public function getPessoas(){
		return $this->_getPessoas();
	}
	// SETTERS
	public function add($data){
		return $this->_add($data);
	}

	// Insere uma nova Pessoa
	// return 'no' => 'Não cadastrado'
	// return 'ok' => 'Cadastrado com sucesso'
	private function _add($data){

		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = $this->conexao->prepare('
			INSERT INTO pessoas (
				pes_nome,
				pes_telefone,
				pes_email,
				pes_ip,
				pes_sexo
			) VALUES (
				:pes_nome,
				:pes_telefone,
				:pes_email,
				:pes_ip,
				:pes_sexo
			)
		');
		$sql->bindParam(':pes_nome', $data['pes_nome']);
		$sql->bindParam(':pes_telefone', $data['pes_telefone']);
		$sql->bindParam(':pes_email', $data['pes_email']);
		$sql->bindParam(':pes_sexo', $data['pes_sexo']);
		$sql->bindParam(':pes_ip', $ip);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// Pessoa não cadastrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Pessoa não cadastrada.'];
		}

		return ['r' => 'ok', 'data' => 'Pessoa cadastrada com sucesso.'];
	}

	// Busca informações de uma pessoa apartir de um pes_codigo
	// return 'no' => 'Não encontrado'
	// return 'ok' => 'Encontrado'
	private function _getPessoa($pes_codigo){

		// pes_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				pes.pes_codigo,
				pes.pes_nome,
				pes.pes_email,
				pes.pes_telelefone,
				pes.pes_status,
				pes.pes_atualizacao,
				pes.pes_autodata
			FROM pessoas AS pes
			WHERE pes_codigo = :pes_codigo
		');
		$sql->bindParam(':pes_codigo', $pes_codigo);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// pessoa não encontrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Pessoa não encontrada.'];
		}

		return ['r' => 'ok', 'data' => 'Pessoa encontrada, chama-se '. $temp['pes_nome']];
	}

	// Busca informações de todas as pessoas
	// return 'no' => 'Não encontrado'
	// return 'ok' => 'Encontrado'
	private function _getPessoas(){

		// pes_ativo = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				pes.pes_codigo,
				pes.pes_nome,
				pes.pes_email,
				pes.pes_telefone,
				pes.pes_status,
				pes.pes_atualizacao,
				pes.pes_autodata
			FROM pessoas AS pes
			ORDER BY pes.pes_codigo DESC, pes.pes_nome ASC
		');
		$sql->execute();
		$temp = $sql->fetchAll(PDO::FETCH_ASSOC);
		$sql = null;

		// pessoa não encontrado
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Nenhuma pessoa encontrada.'];
		}

		return ['r' => 'ok', 'data' => $temp];
	}

	// Atualizar pessoa (ultima pesita)
	// return 'no' => 'Não atualizou'
	// return 'ok' => 'Atualizou com sucesso'
	private function _updatePessoa($data){

		$sql = $this->conexao->prepare("
			UPDATE pessoas SET 
				pes_atualizacao = 'now()',
				pes_nome = :pes_nome,
				pes_telefone = :pes_telefone,
				pes_email = :pes_email
			WHERE pes_codigo = :pes_codigo 
		");
		$sql->bindParam(':pes_nome', $data['pes_nome']);
		$sql->bindParam(':pes_telefone', $data['pes_telefone']);
		$sql->bindParam(':pes_email', $data['pes_email']);
		$sql->bindParam(':pes_codigo', $data['pes_codigo']);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// pessoa não cadastrada
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não encontramos essa pessoa.'];
		}

		return ['r' => 'ok', 'data' => 'Pessoa atualizada com sucesso.'];
	}
}
