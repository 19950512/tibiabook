<?php

namespace Model\Plugins;

use Model\Core\De as de;

class Youtube {

	// $data = dados informações do vídeo
	public $data = [];

	// $videos = array/lista de vídeos
	public $videos = [];

	private $api_key = 'YOUR_API_KEY';

	private $api_url = 'https://www.googleapis.com/youtube/v3/videos';

	private $youtube_domain = 'https://youtube.com/';

	function __construct(){

		if(defined('API_KEY')){
			$this->api_key = API_KEY;
		}
	}

	public function get($urlVideo = ''){
		return $this->_get($urlVideo);
	}
	public function getID($url = ''){
		return $this->_getID($url);
	}

	public function add($urlVideo = ''){

		$data = $this->_get($urlVideo);

		if(isset($data['items']) and count($data['items']) > 0){

			foreach($data['items'] as $arr){

				$miniatura = $arr['snippet']['thumbnails']['standard']['url'] ?? '';

				if($miniatura == ''){
					$miniatura = $this->getMiniatura($this->youtube_domain.'watch?v='.$arr['id'] ?? '');
				}

				$embed = '<iframe width="560" height="315" src="'.$this->youtube_domain.'embed/'.$arr['id'].'?autoplay=1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
				$this->videos[$arr['id']]['miniatura'] 		= $miniatura;
				$this->videos[$arr['id']]['url'] 			= $this->youtube_domain.'watch?v='.$arr['id'] ?? '';
				$this->videos[$arr['id']]['titulo'] 		= $arr['snippet']['title'] ?? '';
				$this->videos[$arr['id']]['id'] 			= $arr['id'] ?? '';
				$this->videos[$arr['id']]['descricao'] 		= $arr['snippet']['description'] ?? '';
				$this->videos[$arr['id']]['publicado'] 		= $arr['snippet']['publishedAt'] ?? '';
				$this->videos[$arr['id']]['embed'] 			= $embed;
				$this->videos[$arr['id']]['duracao'] 		= $arr['contentDetails']['duration'] ?? '';
				$this->videos[$arr['id']]['visualizacoes'] 	= $arr['statistics']['viewCount'] ?? 0;
				$this->videos[$arr['id']]['like'] 			= $arr['statistics']['likeCount'] ?? 0;
				$this->videos[$arr['id']]['dislike'] 		= $arr['statistics']['dislikeCount'] ?? 0;
				$this->videos[$arr['id']]['favorito'] 		= $arr['statistics']['favoriteCount'] ?? 0;
				$this->videos[$arr['id']]['comentarios'] 	= $arr['statistics']['commentCount'] ?? 0;
			}
		}

		return $this;
	}

	private function _get($urlVideo = '', $tentativa = false){

	 	$api_url = $this->api_url.'?part=snippet%2CcontentDetails%2Cstatistics&id=' . $this->_getID($urlVideo) . '&key=' . $this->api_key;

	 	$content = file_get_contents($api_url);
	 	$data = json_decode($content, true);

	 	return $data;
	}

	private function _getID($url = ''){

		/* LINK COMPLETO DO NAVEGADOR, ACESSO DIRETO AO VIDEO*/
		$queryString = parse_url($url, PHP_URL_QUERY);
		parse_str($queryString, $params);

		if(isset($params['v']) && strlen($params['v']) > 0){
			return $params['v'];
		}

		/* SE FOR POR URL "COMPARTILHADA" DO YOUTUBE, AQUELA MAIS CURTA.. */
		$url = explode('/', $url);
		$id_video = $url[3] ?? '';

		return $id_video;
	}

	static function getMiniatura($url = ''){

		$url_imagem = parse_url($url);
		if($url_imagem['host'] == 'www.youtube.com' || $url_imagem['host'] == 'youtube.com'){

			$array = explode("&", $url_imagem['query']);

			return "http://img.youtube.com/vi/".substr($array[0], 2)."/0.jpg";

		}elseif($url_imagem['host'] == 'www.vimeo.com' || $url_imagem['host'] == 'vimeo.com'){

			$link = "http://vimeo.com/api/v2/video/".substr($url_imagem['path'], 1).".php";
			$hash = unserialize(file_get_contents($link));

		}

		return $hash[0]["thumbnail_small"];
	}
}
