<?php

namespace Model\Db;

use Model\Core\De as de;
use PDO;
use PDOException;

class Connection {

    public static $instance;

    function __construct(){}

    public static function getConnection(){

        try {

           if(!isset(self::$instance)){

                self::$instance = new PDO('pgsql:host = ' . DB_HOST . ' dbname = ' . DB_NAME . ' user = ' . DB_USER . ' password = ' . DB_PASSWORD . ' port =' . DB_PORT);

                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return self::$instance;

        } catch (PDOException $e){

            return 'Error connection';
        }
    }
}