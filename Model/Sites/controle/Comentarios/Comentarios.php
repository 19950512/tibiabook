<?php

namespace Model\Sites\Admin\Comentarios;

use Model\Model;
use PDO;
use PDOException;

use Model\Core\Core;
use Model\Core\View;
use Model\Core\De as de;

class Comentarios extends Model{

	public $total = 0;

	function __construct(){

		parent::__construct();

		$this->view = new View();
	}


	public function getComentarios($pub_codigo, $mascara){

		$comentarios = $this->_comentarios($pub_codigo);

		$html = '';
		foreach ($comentarios as $com_codigo => $arr){

			$mustache = [
				'{{vis_nome}}' => $arr['vis_nome'] ?? '',
				'{{com_codigo}}' => $arr['com_codigo'] ?? '',
				'{{com_texto}}' => $arr['com_texto'] ?? '',
				'{{com_autodata}}' => $arr['com_autodata'] ?? '',
			];

			$html .= Core::mustache($mustache, $mascara);
		}

		return $html;
	}

	public function salvar($data = []){

		$com_ip = $_SERVER['REMOTE_ADDR'];

		$data['com_texto'] =  str_replace("'", "\'", $data['com_texto']);
		$com_texto = $data['com_texto'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			INSERT INTO comentarios (
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