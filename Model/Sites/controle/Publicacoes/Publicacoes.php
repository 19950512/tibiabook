<?php

namespace Model\Sites\Admin\Publicacoes;

use Model\Model;
use PDO;
use PDOException;

use Model\Core\Core;
use Model\Core\View;
use Model\Core\De as de;

class Publicacoes extends Model{

	public $total = 0;

	function __construct(){

		parent::__construct();

		$this->total = count($this->_publicacoes());

		$this->view = new View();
	}

	private function _publicacoes($pub_codigo = ''){

		$where = 'pub_status = 1';
		if($pub_codigo !== ''){
			$where = 'pub_status = 1 AND pub_codigo = :pub_codigo';
		}

		try {

			$sql = $this->conexao->prepare('
				SELECT
				*
				FROM Publicacoes
				WHERE '.$where);

			if($pub_codigo !== ''){
				$sql->bindParam(':pub_codigo', $pub_codigo);
			}

			$sql->execute();
			$temp = $sql->fetchAll(PDO::FETCH_ASSOC);

		}catch(PDOException $e){
			return false;
		}

		// Contém erro
		if($temp === false){     
			// Manda para o Devlogs
			new Devlogs($sql, '87321', __METHOD__);
			return false;
		}

		// Não contem erro, segue com o script
		$sql = null;

		$fetch = [];
		foreach ($temp as $key => $arr){
			$fetch[$arr['pub_codigo']] = $arr;
		}

		return $fetch;
	}

	public function getPublicacoes(){

		$publicacoes = $this->_publicacoes();

		$html = '';
		$miniatura = $this->view->getView('Publicacao', 'Miniatura-publicacoes');
		foreach ($publicacoes as $pub_codigo => $arr){

			$mustache = [
				'{{controlador}}' => 'publicacao',
				'{{pub_codigo}}' => $arr['pub_codigo'] ?? '',
				'{{pub_titulo}}' => $arr['pub_titulo'] ?? '',
				'{{pub_subtitulo}}' => $arr['pub_subtitulo'] ?? '',
				'{{pub_texto}}' => $arr['pub_texto'] ?? '',
				'{{pub_autodata}}' => $arr['pub_autodata'] ?? '',
			];

			$html .= Core::mustache($mustache, $miniatura);
		}

		return $html;
	}

	public function getPublicacao($pub_codigo){
		
		$publicacao = $this->_publicacoes($pub_codigo);
		
		return $publicacao;
	}

	public function salvar($data = []){

		$pub_ip = $_SERVER['REMOTE_ADDR'];

		$data['pub_texto'] =  str_replace("'", "\'", $data['pub_texto']);
		$pub_texto = $data['pub_texto'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			INSERT INTO publicacoes (
				pub_titulo,
				pub_subtitulo,
				pub_status,
				pub_comentar,
				pub_autodata,
				pub_atualizacao,
				pub_ip,
				pub_texto
			) VALUES (
				:pub_titulo,
				:pub_subtitulo,
				:pub_status,
				:pub_comentar,
				:pub_autodata,
				:pub_atualizacao,
				:pub_ip,
				E'$pub_texto'
			)
		");
		$sql->bindParam(':pub_titulo', $data['pub_titulo']);
		$sql->bindParam(':pub_subtitulo', $data['pub_subtitulo']);
		$sql->bindParam(':pub_status', $data['pub_status']);
		$sql->bindParam(':pub_ip', $pub_ip);
		$sql->bindParam(':pub_comentar', $data['pub_comentar']);
		$sql->bindParam(':pub_autodata', $now);
		$sql->bindParam(':pub_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar a publicação, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Publicação salva com sucesso.'];
	}

	public function update($data = []){

		$pub_ip = $_SERVER['REMOTE_ADDR'];

		$data['pub_texto'] =  str_replace("'", "\'", $data['pub_texto']);
		$pub_texto = $data['pub_texto'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE publicacoes SET
				pub_titulo = :pub_titulo,
				pub_subtitulo = :pub_subtitulo,
				pub_status = :pub_status,
				pub_ip = :pub_ip,
				pub_comentar = :pub_comentar,
				pub_atualizacao = :pub_atualizacao,
				pub_texto = :pub_texto
			WHERE pub_codigo = :pub_codigo
		");
		$sql->bindParam(':pub_titulo', $data['pub_titulo']);
		$sql->bindParam(':pub_subtitulo', $data['pub_subtitulo']);
		$sql->bindParam(':pub_texto', $pub_texto);
		$sql->bindParam(':pub_status', $data['pub_status']);
		$sql->bindParam(':pub_ip', $pub_ip);
		$sql->bindParam(':pub_comentar', $data['pub_comentar']);
		$sql->bindParam(':pub_codigo', $data['pub_codigo']);
		$sql->bindParam(':pub_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar a publicação, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Publicação alterada com sucesso.'];
	}
}