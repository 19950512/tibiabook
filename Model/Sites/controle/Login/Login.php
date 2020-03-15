<?php

namespace Model\Sites\Admin\Login;

use PDO;

use Model\Core\De as de;


/**
** @see Essa classe é usada somente para o LOGIN NO SISTEMA.
** Por isso tem a aconexao aqui, somente aqui é aonde conecta com o DB geral. (ADM)
**/
class Login {

	private $conexao;

	function __construct(){

		$this->conexao = new PDO('pgsql:host = ' . DB_HOST . ' dbname = admin user = devnux password = qwerty port =' . DB_PORT);
		$this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	function autentica($id, $senha){

		// acc_status = 1 ATIVO
		$sql = $this->conexao->prepare('
			SELECT
				acc.id,
				acc.senha,
				acc.usu_nome,
				acc.db_name,
				acc.usu_codigo,
				conf.conf_visitante,
				conf.conf_traducao,
				conf.conf_publicacao,
				conf.conf_configuracao,
				conf.conf_tv
			FROM usuarios AS acc
			LEFT JOIN configuracoes AS conf ON conf.usu_codigo = acc.usu_codigo
			WHERE acc.id = :id AND acc.senha = :senha
		');
		$sql->bindParam(':id', $id);
		$sql->bindParam(':senha', $senha);
		$sql->execute();
		$temp = $sql->fetch(PDO::FETCH_ASSOC);

		// Senha inválida
		if($temp === false){	 
			sleep(2);
			return ['r' => 'no', 'data' => 'Senha inválida.'];
		}

		// Senha Válida

		// Cria as sessions
		$_SESSION[SESSION_LOGIN]['id'] 			= $temp['id'];
		$_SESSION[SESSION_LOGIN]['usu_nome'] 	= $temp['usu_nome'];
		$_SESSION[SESSION_LOGIN]['db_name'] 	= $temp['db_name'];
		$_SESSION[SESSION_LOGIN]['senha'] 		= $temp['senha'];
		$_SESSION[SESSION_LOGIN]['usu_codigo'] 	= $temp['id'];

		// Configuracoes
		$_SESSION[SESSION_CONFIGURACOES]['conf_visitante'] 		= $temp['conf_visitante'];
		$_SESSION[SESSION_CONFIGURACOES]['conf_publicacao'] 	= $temp['conf_publicacao'];
		$_SESSION[SESSION_CONFIGURACOES]['conf_traducao'] 		= $temp['conf_traducao'];
		$_SESSION[SESSION_CONFIGURACOES]['conf_configuracao'] 	= $temp['conf_configuracao'];
		$_SESSION[SESSION_CONFIGURACOES]['conf_tv'] 			= $temp['conf_tv'];

		return ['r' => 'ok', 'data' => 'Login efetuado com sucesso.'];
	}
}