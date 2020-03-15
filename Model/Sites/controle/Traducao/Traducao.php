<?php

namespace Model\Sites\Admin\Traducao;

use Model\Model;
use PDO;
use PDOException;

use Model\Core\Core;
use Model\Core\View;
use Model\Core\De as de;

class Traducao extends Model{

	function __construct(){

		parent::__construct();

		$this->view = new View();
	}

	/* Retorna uma tradução específica */
	public function getTraducao($trad_codigo){

		if(is_numeric($trad_codigo)){
			return $this->_getTraducoes()[$trad_codigo] ?? [];
		}

		return [];
	}

	/* Retorna todas as traduções */
	public function getTraducoes(){
		return $this->_getTraducoes();
	}

	/* Busca no DB as traduções */
	private function _getTraducoes(){

		$sql = $this->conexao->prepare('
			SELECT 
				trad.trad_codigo,
				trad.trad_br,
				trad.trad_en,
				trad.trad_it,
				trad.trad_excluir,
				trad.trad_atualizacao,
				trad.trad_ip
			FROM traducao AS trad
			ORDER BY trad.trad_codigo DESC
		');
		$sql->execute();
		$temp = $sql->fetchAll(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return false;
		}

		// Não contem erro, segue com o script
		$sql = null;

		$fetch = [];
		foreach ($temp as $key => $arr){
			$fetch[$arr['trad_codigo']] = $arr;
		}

		return $fetch;
	}

	public function salvar($data = []){

		$com_ip = $_SERVER['REMOTE_ADDR'];

		$data['com_texto'] =  str_replace("'", "\'", $data['com_texto']);
		$com_texto = $data['com_texto'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			INSERT INTO traducao (
				pub_codigo,
				vis_codigo,
				com_status,
				com_autodata,
				com_atualizacao,
				com_ip,
				com_texto
			) VALUES (
				:pub_codigo,
				:vis_codigo,
				:com_status,
				:com_autodata,
				:com_atualizacao,
				:com_ip,
				E'$com_texto'
			)
		");
		$sql->bindParam(':pub_codigo', $data['pub_codigo']);
		$sql->bindParam(':vis_codigo', $data['vis_codigo']);
		$sql->bindParam(':com_status', $data['com_status']);
		$sql->bindParam(':com_ip', $com_ip);
		$sql->bindParam(':com_autodata', $now);
		$sql->bindParam(':com_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar o comentário, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'comentário salvo com sucesso.'];
	}

	public function update($data = []){

		$com_ip = $_SERVER['REMOTE_ADDR'];

		$data['com_texto'] =  str_replace("'", "\'", $data['com_texto']);
		$com_texto = $data['com_texto'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE publicacoes SET
				com_status = :com_status,
				com_ip = :com_ip,
				com_atualizacao = :com_atualizacao,
				com_texto = :com_texto
			WHERE com_codigo = :com_codigo
		");
		$sql->bindParam(':com_texto', $com_texto);
		$sql->bindParam(':com_status', $data['com_status']);
		$sql->bindParam(':com_ip', $com_ip);
		$sql->bindParam(':com_codigo', $data['com_codigo']);
		$sql->bindParam(':com_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar o comentário, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Comentário alterada com sucesso.'];
	}
}