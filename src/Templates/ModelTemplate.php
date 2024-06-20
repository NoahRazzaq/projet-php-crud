<?php
class {{className}} {

    private $conn;
    private $table_name = "{{tableName}}";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTableColumns() {
        $query = "SHOW COLUMNS FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        $columns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[] = $row['Field'];
        }
        return $columns;
    }

    // Lire tous les éléments
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
    
        return $stmt;
    }

    // Créer un nouvel élément
    public function create($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } 
        
        $query = "INSERT INTO " . $this->table_name . " SET ";
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key=:$key";
        }
        $query .= implode(', ', $sets);
    
        $stmt = $this->conn->prepare($query);
    
        // Bind des données avec `bindValue`
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
    
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Mettre à jour un élément
    public function update($data, $id) {
        $query = "UPDATE " . $this->table_name . " SET ";
        
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key=:$key";
        }
        $query .= implode(', ', $sets);
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Bind des données
        foreach ($data as $key => $value) {
            $stmt->bindParam(':' . $key, $value);
        }
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Supprimer un élément
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
