<?php
namespace PHPOnLLM\DB\RDB;

class DB {
    private static $instance = null;
    private $pdo, $query, $error = false, $results, $count = 0;

    private function __construct($config) {
        try {
            $this->pdo = new PDO($config['dsn'], $config['username'], $config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance($config = null) {
        if (!isset(self::$instance)) {
            if ($config === null) {
                throw new Exception('Configuration array is required for the first time initialization.');
            }
            self::$instance = new DB($config);
        }
        return self::$instance;
    }

    public function query($sql, $params = []) {
        $this->error = false;
        if ($this->query = $this->pdo->prepare($sql)) {
            $x = 1;
            foreach ($params as $param) {
                $this->query->bindValue($x, $param);
                $x++;
            }

            if ($this->query->execute()) {
                $this->results = $this->query->fetchAll();
                $this->count = $this->query->rowCount();
            } else {
                $this->error = true;
            }
        }
        return $this;
    }

    public function action($action, $table, $where = []) {
        if (count($where) === 3) {
            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];
            $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
            if (!$this->query($sql, [$value])->error()) {
                return $this;
            }
        }
        return false;
    }

    public function get($table, $where) {
        return $this->action('SELECT *', $table, $where);
    }

    public function insert($table, $data) {
        $keys = array_keys($data);
        $values = ':' . implode(', :', $keys);
        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
        return !$this->query($sql, $data)->error();
    }

    public function update($table, $data, $where) {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "`{$key}` = :{$key},";
        }
        $set = rtrim($set, ',');

        $sql = "UPDATE {$table} SET {$set} WHERE {$where[0]} {$where[1]} :whereValue";
        $data['whereValue'] = $where[2];

        return !$this->query($sql, $data)->error();
    }

    public function delete($table, $where) {
        return $this->action('DELETE', $table, $where);
    }

    public function error() {
        return $this->error;
    }

    public function count() {
        return $this->count;
    }

    public function results() {
        return $this->results;
    }

    public function first() {
        return !empty($this->results) ? $this->results[0] : null;
    }
}
