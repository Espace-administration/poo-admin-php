<?php

/**
 * Class Str
 */
class Str {

    /**
     * @param $length
     * @return string
     */
    static function random($length){
        $alphabet = "1234567890azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";
        return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
    }
}