<?php

//__ gestion des erreurs
ini_set('display_errors','on');
error_reporting(E_ALL);

spl_autoload_register('app_autoload');

function app_autoload ($class) {
    require "class/".$class.".php";
}