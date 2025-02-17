<?php
// add-note.php
session_start();
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

if (!isset($_POST['id']) || !isset($_POST['message'])) {
    http_response_code(400);
    echo json_encode(["error" => "Parámetros faltantes"]);
    exit();
}

$id = $_POST['id'];
$message = $_POST['message'];
$author = $_SESSION['username'] ?? 'Desconocido';

try {
    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    $collection = $mongoClient->kanban->tasks;
    
    // Añadir la nueva nota al array 'notes'
    $result = $collection->updateOne(
        ['_id' => new ObjectId($id)],
        ['$push' => ['notes' => ['author' => $author, 'message' => $message]]]
    );
    
    if ($result->getModifiedCount() > 0) {
        echo json_encode(["success" => true, "message" => "Nota añadida"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo añadir la nota"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
