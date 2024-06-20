<?php
require __DIR__ . '/../vendor/autoload.php';
require_once '../src/config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$modelTemplatePath = __DIR__ . '/../src/templates/modelTemplate.php';
$controllerTemplatePath = __DIR__ . '/../src/templates/controllerTemplate.php';

// Lire le contenu des templates
$modelTemplate = file_get_contents($modelTemplatePath);
$controllerTemplate = file_get_contents($controllerTemplatePath);

$sql = "SHOW TABLES";
$stmt = $db->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tableName = $row[0];
    $className = ucwords($tableName); // Transforme tableName en CamelCase pour les noms de classe
    $modelFile = __DIR__ . "/../src/models/{$className}.php";
    $controllerFile = __DIR__ . "/../src/controller/{$className}Controller.php";

    // Créer le modèle si nécessaire
    if (!file_exists($modelFile)) {
        $newModelContent = str_replace(['{{className}}', '{{tableName}}'], [$className, $tableName], $modelTemplate);
        file_put_contents($modelFile, $newModelContent);
        echo "Modèle pour $tableName créé.\n";
    }

    // Créer le contrôleur si nécessaire
    if (!file_exists($controllerFile)) {
        $newControllerContent = str_replace(['{{className}}', '{{tableName}}'], [$className, $tableName], $controllerTemplate);
        file_put_contents($controllerFile, $newControllerContent);
        echo "Contrôleur pour $tableName créé.\n";
    }
}

// Répondre avec succès
http_response_code(200);
echo json_encode(["message" => "Vérification et création de fichiers effectuée."]);


$requestMethod = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];

if (preg_match("/\/(\w+)(\/(\d+))?$/", $url, $matches)) {
    $entity = $matches[1]; 
    $id = $matches[3] ?? null;
    
    $controllerName = ucwords($entity) . 'Controller';
    $controllerFile = __DIR__ . "/../src/controller/{$controllerName}.php";
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controller = new $controllerName($db);

        switch ($requestMethod) {
            case 'GET':
                if ($id) {
                    $controller->readOne($id);
                } else {
                    $controller->read();
                }
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"), true);
                $controller->create($data);
                break;
            case 'PUT':
                if ($id) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $controller->update($id, $data);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "ID is required for PUT requests"]);
                }
                break;
            case 'DELETE':
                if ($id) {
                    $controller->delete($id);
                } else {
                    http_response_code(400);
                    echo json_encode(["message" => "ID is required for DELETE requests"]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method Not Allowed"]);
                break;
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Controller not found for $entity"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Resource not found"]);
}
?>
