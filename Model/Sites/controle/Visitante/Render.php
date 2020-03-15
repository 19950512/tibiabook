<?php

namespace Model\Sites\Admin\Visitante;

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
					'{{vis_nome}}' 			=> $arr['vis_nome'] ?? '-',
					'{{vis_avatar}}' 		=> $arr['vis_avatar'] ?? 'user.jpg',
					'{{vis_email}}' 		=> $arr['vis_email'] ?? '-',
					'{{time}}' 				=> time(),
					'{{vis_tel}}' 			=> $arr['vis_tel'] ?? '-',
					'{{vis_cel}}' 			=> $arr['vis_cel'] ?? '-',
					'{{vis_ip}}' 			=> $arr['vis_ip'] ?? '-',
					'{{vis_atualizacao}}' 	=> date('d/m/Y', strtotime($arr['vis_atualizacao'])).' às '.date('H:i', strtotime($arr['vis_atualizacao'])).'h',
				];

				$html .= Core::mustache($mustache, $mascara);
			}
		}

		return $html;
	}
}