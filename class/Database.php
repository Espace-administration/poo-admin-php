<?php

/**
 * Class Database
 */
class Database {

    /**
     * @var
     */
    private $pdo;

    /**
     * @param $login
     * @param $password
     * @param $database_name
     * @param string $host
     */
    public function __construct ($login, $password, $database_name, $host = 'localhost') {
        $this->pdo = new PDO("mysql:dbname=$database_name;host=$host", $login, $password);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
    }

    /**
     * @param $query
     * @param bool|array $params
     * @return PDOStatement
     */
    public function query($query, $params = false) {
        if ($params) {
            $req = $this->pdo->prepare($query);
            $req->execute($params);
        } else {
            $req = $this->pdo->query($query);
        }

        return $req;
    }

    /**
     * @return string
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}