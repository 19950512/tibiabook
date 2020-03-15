<?php

namespace Model\Sites\Admin\Traducao;

use Model\Core\Core;
use Model\Core\De as de;
use Model\Sites\Sites;

class Render {

	public static function miniatura($data, $mascara = ''){

		$site = new Sites();

		$domain_static = $site->sites[$_SERVER['SERVER_NAME']]['statics'];
		$html = '';
		if(is_array($data)){

			foreach($data as $arr){

				$mustache = [
					'{{domain_static}}'		=> $domain_static,
					'{{trad_excluir}}' 		=> $arr['trad_excluir'] ?? '-',
					'{{trad_codigo}}' 		=> $arr['trad_codigo'] ?? '-',
					'{{trad_br}}' 			=> $arr['trad_br'] ?? '-',
					'{{trad_it}}' 			=> $arr['trad_it'] ?? '-',
					'{{trad_en}}' 			=> $arr['trad_en'] ?? '-',
					'{{trad_ip}}' 			=> $arr['trad_ip'] ?? '-',
					'{{trad_atualizacao}}' 	=> date('d/m/Y', strtotime($arr['trad_atualizacao'])).' às '.date('H:i', strtotime($arr['trad_atualizacao'])).'h',
				];

				$html .= Core::mustache($mustache, $mascara);
			}
		}

		return $html;
	}
}