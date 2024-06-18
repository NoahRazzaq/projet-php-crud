<?php

set_include_path('.'); 
require __DIR__ . '/../vendor/autoload.php';
require_once '../src/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Template 
$modelTemplatePath = __DIR__ . '/../src/templates/modelTemplate.php';
$controllerTemplatePath = __DIR__ . '/../src/templates/controllerTemplate.php';

$modelTemplate = file_get_contents($modelTemplatePath);
$controllerTemplate = file_get_contents($controllerTemplatePath);

// Parcourir les tables
$sql = "SHOW TABLES";
$stmt = $db->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tableName = $row[0];
    $className = ucwords($tableName); // CamelCase
    $modelFile = __DIR__ . "/../src/models/{$className}.php";
    $controllerFile = __DIR__ . "/../src/controller/{$className}Controller.php";

    // Créer le modèle si n'existe pas
    if (!file_exists($modelFile)) {
        $newModelContent = str_replace(['{{className}}', '{{tableName}}'], [$className, $tableName], $modelTemplate);
        file_put_contents($modelFile, $newModelContent);
        echo "Modèle pour $tableName créé.\n";
    }

    // Créer le contrôleur si n'existe pas
    if (!file_exists($controllerFile)) {
        $newControllerContent = str_replace(['{{className}}', '{{tableName}}'], [$className, $tableName], $controllerTemplate);
        file_put_contents($controllerFile, $newControllerContent);
        echo "Contrôleur pour $tableName créé.\n";
    }
}

http_response_code(200);
echo json_encode(["message" => "Vérification et création de fichiers effectuée."]);
?>
