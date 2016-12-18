<?php

require_once "inc/bootstrap.php";

if (!empty($_POST) && !empty($_POST['email'])){
    $db = App::getDatabase();
    $auth = App::getAuth();
    $session = Session::getInstance();
    if ($auth->resetPassword($db, $_POST['email'])) {
        $session->setFlash('success', 'Le rappel du mot de passe a été envoyé par mail');

        App::redirection('login.php');
    }else{
        $session->setFlash('danger', 'Aucun compte ne correspond à cette adresse');
    }
}
?>

<?php require "inc/header.php"; ?>
<h1>Mot de passe oublié</h1>

<form action="" method="POST">
    <div class="form-group">
        <label for="">E-mail</label>
        <input type="email" name="email" class="form-control" />
    </div>

    <button type="submit" class="btn btn-primary">Renouveller le mot de passe</button>
</form>

<?php require "inc/footer.php"; ?>
