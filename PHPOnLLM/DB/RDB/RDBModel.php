<?php
namespace PHPOnLLM\DB\RDB;

abstract class Model {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = DB::getInstance(); // Assuming DB class is already included
        $this->table = $this->setTable();
    }

    // Method to set the table name, must be implemented by the child class
    abstract protected function setTable();

    public function getTable() {
        return $this->table;
    }

    public function find($id) {
        return $this->db->get($this->table, ['id', '=', $id])->first();
    }

    public function findBy($field, $value, $operator = '=', $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} {$operator} ?";
        $params = [$value];

        if ($limit !== null && is_numeric($limit)) {
            $sql .= " LIMIT ?";
            $params[] = (int) $limit;

            if ($offset !== null && is_numeric($offset)) {
                $sql .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }

        $this->db->query($sql, $params);
        return $this->db->results();
    }

    public function all($limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];

        if ($limit !== null && is_numeric($limit)) {
            $sql .= " LIMIT ?";
            $params[] = (int) $limit;

            if ($offset !== null && is_numeric($offset)) {
                $sql .= " OFFSET ?";
                $params[] = (int) $offset;
            }
        }

        $this->db->query($sql, $params);
        return $this->db->results();
    }

    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id', '=', $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id', '=', $id]);
    }
}
