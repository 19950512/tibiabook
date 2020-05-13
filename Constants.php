<?php

define( 'DEV', true );

define( 'DIR', __DIR__ );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'CLASSES', DIR . DS . 'Model' );
define( 'POLLING', DIR . DS . 'Polling' );
define( 'CONTROLLER', 'Controller' );
define( 'MODEL', 'Model' );
define( 'VIEW', 'View' );
define( 'LAYOUT', 'Layout' );
define( 'EXTENSAO_VIEW', '.html' );
define( 'PATH_SITES', 'Sites' );
define( 'SUBDOMINIO', 'tibiabook' );


/* CONFIGURAÇÕES DO SITE */
define( 'SESSION_LOGIN', 'LOGIN' );
define( 'SESSION_CONFIGURACOES', 'CONFIGURACOES' );
define( 'SESSION_VISITANTE', 'VISITANTE' );
define( 'SESSION_TOKEN', 'TOKEN' );

define( 'SITE_PROTOCOLO', 'http://' );
define( 'SITE_DOMINIO', 'tibiabook.local' );
define( 'SITE_NOME', 'Tibiabook' );
define( 'SITE_STATICS', 'statics.tibiabook.local' );





/* CONFIGURAÇÃO LINGUAGENS SUPORTADAS */
define( 'LANGUAGES' , [
	'' => 'br',
	'en' => 'en',
	'it' => 'it'
]);

/* AUTHOR - DEVELOPER */
define ( 'AUTHOR', 'Matheus Maydana' );


/* DB - arquivo Db.php 
	define ( 'DB_HOST', '127.0.0.1' );
	define ( 'DB_NAME', 'database_name' );
	define ( 'DB_USER', 'nome_user' );
	define ( 'DB_PASSWORD', 'senha_do_db' );
	define ( 'DB_PORT', '5432' );
	
	// SECRET
	define( 'API_KEY', 'SUA_API_KEY' );
*/
require_once 'Db.php';

/* Config.php - arquivo de configurações

	return array(

		'host'	=> '',
		'user'	=> '',
		'pass'	=> '',
		'nome' 	=> '',
		'from' 	=> '',
		'port'	=> '',
		'crip'	=> '',
		'debug'	=> 1,
		'para' 	=> ['email@..' => 'nome'],
		'icone' => 'url-logo',
		'alt' => '',

		'comoCopiaOculta' => ['email@..' => 'nome']

	);
*/
require_once 'Config.php';

define( 'AUTOLOAD_CLASSES', serialize(array(CLASSES, CONTROLLER, VIEW, MODEL)));