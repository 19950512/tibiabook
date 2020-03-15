<?php

namespace Model\Core;

class De {

    function __construct($a = ''){

        /* DEBUGAR EM DESENVOLVIMENTO */
        if(DEV === true){

            if(is_array($a)){

                echo '<pre>';
                print_r($a);
                exit;

            }else{

                echo '<pre>';
                var_dump($a);
                exit;
            }

            /* HTML EM PRODUÇÃO */
        }else{

            //echo manutencao();
            //exit;
        }
    }
}