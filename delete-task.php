<?php

    session_start();
    
    require 'vendor/autoload.php';
    use MongoDB\Client;
    use MongoDB\BSON\ObjectId;

    $id = $_POST['id'];
    $author = $_SESSION['username'];

    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    $collection = $mongoClient->kanban->tasks;

    $task = $collection->findOne(['_id' => new ObjectId($id)]);
    if (!$task || $task['author'] !== $author) {
        http_response_code(403);
        echo json_encode(["error" => "No autorizado"]);
        exit();
    }

    $result = $collection->deleteOne(['_id' => new ObjectId($id)]);
    if ($result->getDeletedCount() > 0) {
        echo json_encode(["success" => true, "message" => "Tarea eliminada"]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo eliminar la tarea"]);
    }

?>