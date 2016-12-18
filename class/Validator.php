<?php

/**
 * Class Validator
 */
class Validator {

    private $data;
    private $errors = [];

    public function __construct($data){
        $this->data = $data;
    }

    /**
     * @param $field
     * @return null
     */
    private function getField($field) {
        if(!isset($this->data[$field])) {
            return null;
        }
        return $this->data[$field];
    }

    /**
     * @param $field
     * @param $errorMsg
     */
    public function isAlpha($field, $errorMsg) {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $this->getField($field))){
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * @param $field
     * @param $db
     * @param $table
     * @param $errorMsg
     */
    public function isUniq($field, $db, $table, $errorMsg) {
        $record = $db->query("SELECT id FROM $table WHERE $field = ?", [$this->getField($field)])->fetchAll();

        if ($record){
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * @param $field
     * @param $errorMsg
     */
    public function isEmail($field, $errorMsg) {
        if (!filter_var($this->getField($field), FILTER_VALIDATE_EMAIL)){
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * @param $field
     * @param $errorMsg
     */
    public function isConfirmed($field, $errorMsg = '') {
        $value = $this->getField($field);

        if (empty($value) || $value != $this->getField($field.'_confirm')){
            $this->errors[$field] = $errorMsg;
        }
    }

    /**
     * @return bool
     */
    public function isValid() {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
}