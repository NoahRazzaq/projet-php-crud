<?php
class Users {

    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " SET ";
        
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key=:$key";
        }
        $query .= implode(', ', $sets);
        
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update($data, $id) {
        $query = "UPDATE " . $this->table_name . " SET ";
        
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key=:$key";
        }
        $query .= implode(', ', $sets);
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
