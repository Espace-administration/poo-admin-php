<?php

/**
 * Class Session
 */
class Session {

    /**
     * @var
     */
    static $instance;

    /**
     * @return Session
     */
    static function getInstance() {
        if(!self::$instance){
            self::$instance = new Session();
        }
        return self::$instance;
    }

    /**
     * function __contruct
     */
    public function __construct() {
        session_start();
    }

    /**
     * @param $key
     * @param $message
     */
    public function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * @return bool
     */
    public function hasFlashes() {
        return isset($_SESSION['flash']);
    }

    /**
     * @return mixed
     */
    public function getFlashes() {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * @param $key
     * @param $value
     */
    public function write($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function read($key){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }
}