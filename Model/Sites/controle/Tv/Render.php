<?php

namespace Model\Sites\Admin\Tv;

use Model\Core\Core;
use Model\Core\De as de;
use Model\Sites\Sites;

class Render {

	public static function miniatura($data, $mascara = ''){

		$html = '';
		if(is_array($data)){

			krsort($data);
			foreach($data as $arr){

				$vis_codigo = $_SESSION[SESSION_VISITANTE]['vis_codigo'] ?? '';

				$mustache = [
					'{{tv_url}}' => $arr['tv_url'],
					'{{tv_titulo}}' => $arr['tv_titulo'],
					'{{tv_reproducoes}}' => $arr['tv_reproducoes'],
					'{{vis_nome}}' => $arr['vis_nome'] ?? '<i>anonymous</i>',
					'{{tv_comentarios}}' => Core::number_format($arr['tv_comentarios'], 3, '.', '.'),
					'{{tv_visualizacoes}}' => Core::number_format($arr['tv_visualizacoes'], 3, '.', '.'),
					'{{tv_like}}' => Core::number_format($arr['tv_like'], 3, '.', '.'),
					'{{tv_dislike}}' => Core::number_format($arr['tv_dislike'], 3, '.', '.'),
					'{{tv_miniatura}}' => $arr['tv_miniatura'],
					'{{tv_duracao}}' => $arr['tv_duracao'],
					'{{tv_codigo}}' => $arr['tv_codigo'],
					'{{tv_autodata_str}}' 	=> Core::datemask($arr['tv_autodata'], 'd/m/Y'),
					'{{tv_atualizacao_str}}' => Core::datemask($arr['tv_atualizacao'], 'd/m/Y'),
					'{{tv_status_str}}' 		=> ($arr['tv_status'] == '1') ? 'Ativo' : 'Inativo',
					'{{tv_atualizacao}}' 	=> date('d/m/Y', strtotime($arr['tv_atualizacao'])).' às '.date('H:i', strtotime($arr['tv_atualizacao'])).'h',
					'{{vis_hidden}}' => ($arr['vis_codigo'] !== $vis_codigo) ? 'hidden' : '',
				];

				$html .= Core::mustache($mustache, $mascara);
			}
		}

		return $html;
	}
}