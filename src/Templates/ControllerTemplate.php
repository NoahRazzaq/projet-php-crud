<?php
require_once '../src/Models/{{className}}.php';

class {{className}}Controller {
    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new {{className}}($db);
    }

    // Lister tous les éléments
    public function read() {
        // Récupérer les noms des colonnes de la table de manière dynamique
        $columns = $this->model->getTableColumns();
        $result = $this->model->read();
        $num = $result->rowCount();
    
        if ($num > 0) {
            $items = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $item = array();
                // Utiliser les noms des colonnes pour construire chaque objet de manière dynamique
                foreach ($columns as $column) {
                    $item[$column] = $row[$column];
                }
                $items[] = $item;
            }
            http_response_code(200);
            echo json_encode($items);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No items found."));
        }
    }

    
    public function readOne($id) {
        $columns = $this->model->getTableColumns();

        $result = $this->model->readOne($id);
        $num = $result->rowCount();
    
        if ($num > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                $item[$column] = $row[$column];
            }
    
            http_response_code(200);
            echo json_encode($item);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "User not found."));
        }
    }

    // Créer un nouvel élément
    public function create($data) {
        if ($this->model->create($data)) {
            http_response_code(201);
            echo json_encode(array("message" => " created successfully."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create Data may be incomplete or invalid."));
        }
    }

    // Mettre à jour un élément existant
    public function update($data, $id) {
        if ($this->model->update($data, $id)) {
            http_response_code(200);
            echo json_encode(array("message" => "$this->model updated successfully."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update $this->model. Check your data and try again."));
        }
    }

    // Supprimer un élément
    public function delete($id) {
        if ($this->model->delete($id)) {
            http_response_code(200);
            echo json_encode(array("message" => "$this->model deleted successfully."));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to delete $this->model. It may not exist."));
        }
    }
}
?>
