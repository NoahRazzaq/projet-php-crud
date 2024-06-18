<?php
require_once '../src/Models/{{className}}.php';

class {{className}}Controller {

    private $db;
    private $model;

    public function __construct($db) {
        $this->db = $db;
        $this->model = new {{className}}($db);
    }

    public function read() {
        $result = $this->model->read();
        $num = $result->rowCount();

        if ($num > 0) {
            $items = array();
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $item = array(
                    "id" => $id,
                    // Add other fields here
                );
                $items[] = $item;
            }
            echo json_encode($items);
        } else {
            echo json_encode(array("message" => "No items found."));
        }
    }

    public function create($data) {
        if ($this->model->create($data)) {
            echo json_encode(array("message" => "Item created successfully."));
        } else {
            echo json_encode(array("message" => "Unable to create item."));
        }
    }

    public function update($data, $id) {
        if ($this->model->update($data, $id)) {
            echo json_encode(array("message" => "Item updated successfully."));
        } else {
            echo json_encode(array("message" => "Unable to update item."));
        }
    }

    public function delete($id) {
        if ($this->model->delete($id)) {
            echo json_encode(array("message" => "Item deleted successfully."));
        } else {
            echo json_encode(array("message" => "Unable to delete item."));
        }
    }
}
?>
