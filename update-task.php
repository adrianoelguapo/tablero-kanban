<?php
session_start();
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

if (!isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['workers'])) {
    http_response_code(400);
    echo json_encode(["error" => "Parámetros faltantes"]);
    exit();
}

$id = $_POST['id'];
$title = $_POST['title'];
$workers = $_POST['workers'];
if(!is_array($workers)) {
    $workers = array_map('trim', explode(",", $workers));
}
$author = $_SESSION['username'];

$mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
$collection = $mongoClient->kanban->tasks;

$task = $collection->findOne(['_id' => new ObjectId($id)]);
if(!$task || $task['author'] !== $author) {
    http_response_code(403);
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$result = $collection->updateOne(
    ['_id' => new ObjectId($id)],
    ['$set' => ['title' => $title, 'workers' => $workers]]
);

if ($result->getModifiedCount() > 0) {
    echo json_encode(["success" => true, "message" => "Tarea actualizada"]);
} else {
    echo json_encode(["success" => false, "message" => "No se actualizó la tarea"]);
}
?>
