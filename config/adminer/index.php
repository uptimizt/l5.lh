<?php

if(empty($_GET['username']) ){
        $_GET['username'] = 'vagrant';
}

function adminer_object() {
  class AdminerSoftware extends Adminer {
                function credentials() {

                        return array('localhost', 'vagrant', 'password');
                }

                function database() {
                        // database name, will be escaped by Adminer
                        return 'vagrant';
                }

                function login($login, $pass){
                        return true;
                }
        }
        return new AdminerSoftware;
}


require_once './adminer.php';
