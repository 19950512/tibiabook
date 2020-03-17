<?php

namespace Model\Core;

use Model\Core\De as de;
use Model\Router\Router;
use Model\Sites\Sites;

class View {

	/* Metas por Default */
	public $header = array(
		array('name' => 'charset', 'content' => 'UTF-8'),
		array('name' => 'description', 'content' => ''),
		array('name' => 'author', 'content' => AUTHOR ),
		array('name' => 'robots', 'content' => 'noindex, nofollow',/* 'other' => 'sync="sync"'*/),
		array('name' => 'viewport', 'content' => 'width=device-width, user-scalable=no'),
	);

	public $title;
	public $description;

	private $Router;

	public function __construct(){

		$this->Router = new Router();

		$this->title = $this->Router->sites[$_SERVER['SERVER_NAME']]['nome'] ?? ''; 
		$this->description = $this->Router->sites[$_SERVER['SERVER_NAME']]['nome'] ?? ''; 
	}

	private function layout($layout = 'Layout'){

		$pathView = DIR . DS . $this->Router->sites[$_SERVER['SERVER_NAME']]['namespace'] . DS . 'View' . DS . LAYOUT . DS . $layout . EXTENSAO_VIEW;

		$layoutView = file_exists($pathView) ? file_get_contents($pathView) : '';

	   	/* AQUI, mostra o Menu conforme as configurações - permissoes */
	   	$configuracoes = $_SESSION[SESSION_CONFIGURACOES] ?? [];

		$model_options = '';
	   	foreach($configuracoes as $coluna => $valor){

	   		if($valor === 1){
				$label = explode('conf_', $coluna);
				$model_options .= '<a href="/'.$label[1].'"><li class="upper">'.$label[1].'</li></a>';
			}
		}

		$mustache = array(
			'{{site_nome}}' => $this->Router->sites[$_SERVER['SERVER_NAME']]['nome'],
			'{{language}}' => (empty($this->Router->language)) ? $this->Router->language : $this->Router->language.'/',
			'{{color_primary}}' => '#0E1428',
			'{{metas}}' => $this->_getHead(),
			'{{titulo_page}}' => $this->title,
			'{{time}}' => time(),
			'{{model_options}}' => $model_options,
			'{{domain_statics}}' => $this->Router->sites[$_SERVER['SERVER_NAME']]['statics']
		);

		$layout = str_replace(array_keys($mustache), array_values($mustache), $layoutView);
		return self::comprimeHTML($layout);
	}

	public function pushHistory($mustache = [], $view = ''){
		return str_replace(array_keys($mustache), array_values($mustache), $view);
	}

	public function mustache($mustache = [], $view = '', $layout = 'Layout'){
		$view = str_replace(array_keys($mustache), array_values($mustache), $view);

		return str_replace('{{view}}', $view, $this->layout($layout));;
	}

	public function getView($controlador = 'Index', $view = 'Index'){ 
		$pathView = DIR . DS . $this->Router->sites[$_SERVER['SERVER_NAME']]['namespace'] . DS . VIEW . DS . $controlador . DS . $view . EXTENSAO_VIEW;
		return self::comprimeHTML(file_exists($pathView) ? file_get_contents($pathView) : '');
	}

	public function getLayout($layout = 'Layout'){
		$pathView = DIR . DS . $this->Router->sites[$_SERVER['SERVER_NAME']]['namespace'] . DS . VIEW . DS . LAYOUT . DS . $layout . EXTENSAO_VIEW;
		return file_exists($pathView) ? file_get_contents($pathView) : '';
	}

	private function _getHead(){
		$headers = '';
		if(is_array($this->header)){
	
			foreach($this->header as $meta) {
		
				$other = '';
				if(isset($meta['other']) and !empty($meta['other'])){
					$other = $meta['other'];
				}
		
				$headers .= '<meta name="'.$meta['name'].'" content="'.$meta['content'].'" '.$other.'>';
			}
		}

		return $headers;
	}
	public static function comprimeHTML($html = ''){

		/*if(DEV === true){
			return $html;
		}*/

		$html = preg_replace(array("/\/\*(.*?)\*\//", "/<!--(.*?)-->/", "/\t+/"), ' ', $html);

		$mustache = array(
			"\t"		=> '',
			""			=> ' ',
			PHP_EOL		=> '',
			'> <'		=> '><',
			'  '		=> '',
			'   '		=> '',
			'	'		=> '',
			'	 '		=> '',
			'> <'		=> '><',
			'NAOENTER'	=> PHP_EOL,
			'
'						=> ''
		);

		return str_replace(array_keys($mustache), array_values($mustache), $html);
	}
	/**
	 * @return array
	 */
	public function getHeader(): array
	{
		return $this->header;
	}

	/**
	 * @param array $header
	 */
	public function setHeader($array)
	{
		$temp = [];
		foreach ($array as $meta){
		
			$flag = false;
			foreach($this->header as $key => $arr){
			
				if($arr['name'] === $meta['name']){
					$this->header[$key]['content'] = $meta['content'];
					$flag = true;
				}
			
			}
			if($flag == false){
				$temp[$key] = $meta;
				$temp[$key]['other'] = ($meta['other'] ?? '');
			}
		}
	
		$this->header = array_merge($this->header, $temp);
	}
	
	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}
	
	/**
	 * @param string $title
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}
}