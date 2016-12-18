<?php
require_once "inc/bootstrap.php";
$db = App::getDatabase(Session::getInstance());

if (App::getAuth()->confirm($db, $_GET['id'], $_GET['token'], Session::getInstance())) {
    Session::getInstance()->setFlash('success', "Votre compte a été validé");
    App::redirection('account.php');
} else {
    Session::getInstance()->setFlash('danger', "Ce token n'est plus valide");
    App::redirection('login.php');
}