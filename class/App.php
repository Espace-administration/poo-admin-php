<?php

/**
 * Class App
 */
class App {

    /**
     * @var null
     */
    static $db = null;

    /**
     * @return Database|null
     */
    static function getDatabase() {
        if (!self::$db) {
            self::$db = new Database('root','admin', 'admin_basic');
        }

        return self::$db;
    }

    /**
     * @return Auth
     */
    static function getAuth(){
        return new Auth(
            Session::getInstance(),
            ['restriction_msg' => 'lol, tu est bloqué !']
        );
    }

    /**
     * @param $page
     */
    static function redirection($page) {
        header('Location: '.$page);
        exit();
    }
}