<?php

namespace Model;

use Model\Db\Connection;
use Model\Core\De as de;

class Model {

	public $conexao;

	public function __construct(){
		$this->conexao = Connection::getConnection();
	}
}