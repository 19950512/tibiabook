<?php

namespace Model\Sites\Admin\Tv;

use Model\Model;
use PDO;
use PDOException;

use Model\Core\Core;
use Model\Core\View;
use Model\Core\De as de;

class Tv extends Model{

	public $total = 0;

	function __construct(){

		parent::__construct();

		$this->total = count($this->_playLists());

		$this->view = new View();
	}

	private function _playLists($plist_codigo = ''){

		$where = '';
		if($plist_codigo !== ''){
			$where = 'WHERE /*plist.plist_status = 1 AND*/ plist.plist_codigo = :plist_codigo';
		}

		try {

			$sql = $this->conexao->prepare("
				SELECT
				*
				FROM tv_playlists AS plist
				$where
				ORDER BY plist_codigo DESC, plist_nome ASC 
			");

			if($plist_codigo !== ''){
				$sql->bindParam(':plist_codigo', $plist_codigo);
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
			$fetch[$arr['plist_codigo']] = $arr;
		}

		return $fetch;
	}

	private function _playListSongs($plist_codigo = ''){

		try {

			$sql = $this->conexao->prepare("
				SELECT
				( SELECT vis.vis_nome FROM visitante AS vis WHERE VIS.vis_codigo = tv.vis_codigo ) AS vis_nome,
				*
				FROM tv_playlists_video AS tv
				WHERE tv.plist_codigo = :plist_codigo
				ORDER BY tv.tv_codigo ASC
			");
			$sql->bindParam(':plist_codigo', $plist_codigo);
			$sql->execute();

			$temp = $sql->fetchAll(PDO::FETCH_ASSOC);

		}catch(PDOException $e){
			return false;
		}

		// Não contem erro, segue com o script
		$sql = null;

		$fetch = [];
		foreach ($temp as $key => $arr){
			unset($arr['tv_descricao']);
			$fetch[$arr['tv_codigo']] = $arr;
		}

		return $fetch;
	}

	public function getPlayLists(){

		$playlists = $this->_playLists();

		$html = '';
		$miniatura = $this->view->getView('Tv', 'Miniatura-playlist');

		foreach ($playlists as $plist_codigo => $arr){

			$mustache = [
				'{{controlador}}' 			=> 'tv',
				'{{plist_codigo}}' 			=> $arr['plist_codigo'] ?? '',
				'{{plist_nome}}' 			=> $arr['plist_nome'] ?? '',
				'{{plist_autodata_str}}' 	=> Core::datemask($arr['plist_autodata'], 'd/m/Y'),
				'{{plist_atualizacao_str}}' => Core::datemask($arr['plist_atualizacao'], 'd/m/Y'),
				'{{plist_status_str}}' 		=> ($arr['plist_status'] == '1') ? 'Ativo' : 'Inativo',
			];

			$html .= Core::mustache($mustache, $miniatura);
		}

		return ($html == '') ? '<p class="text-center">Nenhuma Play List encontrada.</p>' : $html;
	}

	public function getPlayList($plist_codigo){
		
		$playlist = $this->_playLists($plist_codigo);

		$playlistSongs = $this->_playListSongs($plist_codigo);

		$playlist[$plist_codigo]['videos'] = $playlistSongs;

		return $playlist;
	}

	public function salvar($data = []){

		$plist_ip = $_SERVER['REMOTE_ADDR'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			INSERT INTO tv_playlists (
				plist_nome,
				plist_status,
				plist_ip
			) VALUES (
				:plist_nome,
				:plist_status,
				:plist_ip
			)
		");
		$sql->bindParam(':plist_nome', $data['plist_nome']);
		$sql->bindParam(':plist_status', $data['plist_status']);
		$sql->bindParam(':plist_ip', $plist_ip);
		
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar a Play List, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Play List salva com sucesso.'];
	}

	public function remover($plist_codigo = 0){

		$sql = $this->conexao->prepare('
			DELETE FROM tv_playlists WHERE plist_codigo = :plist_codigo
		');
		$sql->bindParam(':plist_codigo', $plist_codigo);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível remover a Play List, tente novamente mais tarde.'];
		}

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Play List removida com sucesso.'];
	}

	public function nextSong($data = []){

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE tv_playlists SET
				next = '1',
				plist_atualizacao = :plist_atualizacao
			WHERE plist_codigo = :plist_codigo
		");
		$sql->bindParam(':plist_codigo', $data['plist_codigo']);
		$sql->bindParam(':plist_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível trocar de música, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Pronto, está tocando outram música.'];
	}

	public function resetControler($data = []){

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE tv_playlists SET
				next = '2',
				back = '2',
				settv_id = NULL,
				plist_atualizacao = :plist_atualizacao
			WHERE plist_codigo = :plist_codigo
		");
		$sql->bindParam(':plist_codigo', $data['plist_codigo']);
		$sql->bindParam(':plist_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível trocar de música, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Pronto, está tocando outram música.'];
	}

	public function updateTvCodigo($data = []){

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE tv_playlists SET
				tv_codigo = :tv_codigo,
				plist_atualizacao = :plist_atualizacao
			WHERE plist_codigo = :plist_codigo
		");
		$sql->bindParam(':tv_codigo', $data['tv_codigo']);
		$sql->bindParam(':plist_codigo', $data['plist_codigo']);
		$sql->bindParam(':plist_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível trocar de música, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Pronto, está tocando outram música.'];
	}

	public function update($data = []){

		$plist_ip = $_SERVER['REMOTE_ADDR'];

		$now = 'now()';
		$sql = $this->conexao->prepare("
			UPDATE tv_playlists SET
				plist_nome = :plist_nome,
				plist_status = :plist_status,
				plist_ip = :plist_ip,
				plist_atualizacao = :plist_atualizacao
			WHERE plist_codigo = :plist_codigo
		");
		$sql->bindParam(':plist_nome', $data['plist_nome']);
		$sql->bindParam(':plist_status', $data['plist_status']);
		$sql->bindParam(':plist_ip', $plist_ip);
		$sql->bindParam(':plist_codigo', $data['plist_codigo']);
		$sql->bindParam(':plist_atualizacao', $now);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar a Play List, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Salvo com sucesso
		return ['r' => 'ok', 'data' => 'Play List alterada com sucesso.'];
	}








	/* MÚSICAS PARA A PLAYLIST JÁ CRIADA */
	public function addmusica($data = []){

		$tv_ip = $_SERVER['REMOTE_ADDR'];

		$data['tv_descricao'] =  substr(str_replace("'", "\'", $data['tv_descricao']),0, 5000);
		$tv_descricao = $data['tv_descricao'];

		$data['tv_titulo'] =  substr(str_replace("'", "\'", $data['tv_titulo']),0, 200);
		$tv_titulo = $data['tv_titulo'];
		
		$data['tv_embed'] =  substr(str_replace("'", "\'", $data['tv_embed']),0, 300);
		$tv_embed = $data['tv_embed'];

		$sql = $this->conexao->prepare("
			INSERT INTO tv_playlists_video (
				tv_url,
				plist_codigo,
				vis_codigo,
				tv_ip,
				tv_id,
				tv_embed,
				tv_duracao,
				tv_miniatura,
				tv_visualizacoes,
				tv_publicado,
				tv_like,
				tv_dislike,
				tv_favorito,
				tv_comentarios,
				tv_descricao,
				tv_titulo
			) VALUES (
				:tv_url,
				:plist_codigo,
				:vis_codigo,
				:tv_ip,
				:tv_id,
				E'$tv_embed',
				:tv_duracao,
				:tv_miniatura,
				:tv_visualizacoes,
				:tv_publicado,
				:tv_like,
				:tv_dislike,
				:tv_favorito,
				:tv_comentarios,
				E'$tv_descricao',
				E'$tv_titulo'
			)
		");
		$sql->bindParam(':tv_url', $data['tv_url']);
		$sql->bindParam(':plist_codigo', $data['plist_codigo']);
		$sql->bindParam(':vis_codigo', $data['vis_codigo']);
		$sql->bindParam(':tv_ip', $tv_ip);
		$sql->bindParam(':tv_id', $data['tv_id']);
		$sql->bindParam(':tv_duracao', $data['tv_duracao']);
		$sql->bindParam(':tv_miniatura', $data['tv_miniatura']);
		$sql->bindParam(':tv_visualizacoes', $data['tv_visualizacoes']);
		$sql->bindParam(':tv_publicado', $data['tv_publicado']);
		$sql->bindParam(':tv_like', $data['tv_like']);
		$sql->bindParam(':tv_dislike', $data['tv_dislike']);
		$sql->bindParam(':tv_favorito', $data['tv_favorito']);
		$sql->bindParam(':tv_comentarios', $data['tv_comentarios']);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível salvar a música na Playlist, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Removido com sucesso
		return ['r' => 'ok', 'data' => 'Música salva na Play List com sucesso.'];
	}

	public function removermusica($tv_codigo = 0){

		// Descobrir a qual playlist a musica pertence

		$sql = $this->conexao->prepare('
			SELECT plist_codigo FROM tv_playlists_video WHERE tv_codigo = :tv_codigo
		');
		$sql->bindParam(':tv_codigo', $tv_codigo);
		$sql->execute();
		$data = $sql->fetch(PDO::FETCH_ASSOC);
		$sql = null;

		// Deletar a música
		$sql = $this->conexao->prepare('
			DELETE FROM tv_playlists_video WHERE tv_codigo = :tv_codigo
		');
		$sql->bindParam(':tv_codigo', $tv_codigo);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Contém erro
		if($temp === false){     
			return ['r' => 'no', 'data' => 'Ops, não foi possível remover a música da Playlist, tente novamente mais tarde.'];
		}

		// Sincroniza o long Polling
		$this->_syncPooling($data);

		// Removido com sucesso
		return ['r' => 'ok', 'data' => 'Música removida da Playlist com sucesso.'];
	}

	private function _syncPooling($data = []){

		// Verifica se a playlist é a mesma que está reproduzindo ( para alterar o .txt para o polling )
		// Ler o .TXT aonde fica a informação de qual playlist está tocando.
		$playlist = file_get_contents(POLLING .'/tv.txt');
		$playlist = json_decode($playlist, true);

		// Se for a mesma playlist que está sendo alterada, atualiza os dados.
		if($playlist['plist_codigo'] == $data['plist_codigo']){


			// Pega os dados da playlist atualizados
			$Tv = $this->getPlayList($data['plist_codigo'])[$data['plist_codigo']] ?? [];

			// E atualiza o .txt
			file_put_contents(POLLING .'/tv.txt', json_encode($Tv));
		}
	}
}