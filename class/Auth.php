<?php

/**
 * Class Auth
 */
class Auth {
    private $options = [
        'restriction_msg' => "Vous devez vous connecter pour accéder à cette page !",
    ];
    private $session;

    /**
     * @param $session
     * @param array $options
     */
    public function __construct($session, $options = []) {
        $this->options = array_merge($this->options, $options);
        $this->session = $session;
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param $db
     * @param $username
     * @param $password
     * @param $email
     */
    public function register($db, $username, $password, $email) {
        $password = $this->hashPassword($password);
        $token = Str::random(60);

        $db->query(
            'INSERT INTO users SET username = ?, password = ?, email = ?, confirmation_token= ?',[
            $username,
            $password,
            $email,
            $token
        ]);

        $user_id = $db->lastInsertId();
        $email = strip_tags($email);

        $header = "From : ".$email;
        $sujet = "Confirmation de votre mail";
        $message = "Afin de valider votre compte, merci de cliquer sur ce lien\n\nhttp://".$_SERVER['SERVER_NAME']."/confirm.php?id=".$user_id."&token=".$token;

        mail($email, $sujet, $message, $header);
    }

    /**
     * @param $db
     * @param $user_id
     * @param $token
     * @return bool
     */
    public function confirm($db, $user_id, $token) {
        $user = $db->query('SELECT * FROM users WHERE id = ?', [$user_id])->fetch();

        if ($user && $user->confirmation_token == $token) {
            $req = $db->query('UPDATE users SET confirmation_token = NULL, confirmed_at = NOW() WHERE id = ?', [$user_id]);

            $this->session->write('auth', $user);

            return true;
        }

        return false;
    }

    /**
     * function restrict
     */
    public function restrict(){
        if (!$this->session->read('auth')){
            $this->session->setFlash('danger', $this->options['restriction_msg']);

            header('Location: login.php');
            exit();
        }
    }

    /**
     * @return bool
     */
    public function user() {
        if (!$this->session->read('auth')) {
            return false;
        }
        $this->session->read('auth');
    }

    /**
     * @param $user
     */
    public function connect($user) {
        $this->session->write('auth', $user);
    }

    /**
     * @param $db
     */
    public function connectFromCookie($db){
        if (isset($_COOKIE['remember']) && !$this->user()){
            $remember_token = $_COOKIE['remember'];
            $parts = explode('==', $remember_token);
            $user_id = $parts[0];

            $user = $db->query('SELECT * FROM users WHERE id = ?',[$user_id])->fetch();

            if($user) {
                $expected = $user_id."==".$user->remember_token.sha1($user_id.'ratonlaveurs');
                if ($expected == $remember_token){
                    $this->connect($user);
                    setcookie('remember', $remember_token, time()+60*60*24*7);
                } else {
                    setcookie('remember', NULL, -1);
                }
            } else {
                setcookie('remember', NULL, -1);
            }
        }
    }

    /**
     * @param $db
     * @param $username
     * @param $password
     * @param bool $remember
     */
    public function login($db, $username, $password, $remember = false) {
        $user = $db->query('SELECT * FROM users WHERE (username = :username OR email = :username) AND confirmed_at IS NOT NULL ', ['username' => $username])->fetch();

        if (password_verify($password, $user->password)){
            $this->connect($user);

            if ($remember){
                $this->remember($db, $user->id);
            }
            return $user;
        } else {
            return false;
        }
    }

    /**
     * @param $db
     * @param $user_id
     */
    public function remember($db, $user_id) {
        $remember_token = Str::random(250);
        $db->query('UPDATE users SET remember_token = ? WHERE id = ?', [$remember_token, $user_id]);
        setcookie('remember', $user_id.'=='.$remember_token.sha1($user_id.'ratonlaveurs'), time()+60*60*24*7);
    }

    public function logout() {
        setcookie('remember', NULL, -1);
        $this->session->delete('auth');
    }

    public function resetPassword($db, $email) {
        $user = $db->query('SELECT * FROM users WHERE email = ? AND confirmed_at IS NOT NULL', [$email])->fetch();
        if ($user){
            $reset_token = Str::random(60);
            $db->query('UPDATE users SET reset_token = ?, reset_at = NOW() WHERE id = ?', [$reset_token, $user->id]);

            $header = "From : ".$email;
            $sujet = "[http://".$_SERVER['SERVER_NAME']."/] Reset du password";
            $message = "Pour réinitialiser votre mot de passe, merci de cliquer sur ce lien\n\nhttp://".$_SERVER['SERVER_NAME']."/reset.php?id={$user->id}&token=".$reset_token;

            mail($email, $sujet, $message, $header);

            return $user;
        }
        return false;
    }

    public function checkResetToken($db, $user_id, $token) {
        return $db->query("SELECT * FROM users WHERE id = ? AND reset_token = ? AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)",[$user_id, $token])->fetch();
    }
}