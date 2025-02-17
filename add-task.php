<?php

    session_start();
    
    require 'vendor/autoload.php';
    use MongoDB\Client;
    use MongoDB\BSON\ObjectId;

    if (!isset($_POST['title']) || !isset($_POST['state'])) {
        http_response_code(400);
        echo json_encode(["error" => "Parámetros faltantes"]);
        exit();
    }

    $title = $_POST['title'];
    $state = $_POST['state'];
    $workers = isset($_POST['workers']) ? $_POST['workers'] : [];

    if(!is_array($workers)) {
        $workers = array_map('trim', explode(",", $workers));
    }

    $author = $_SESSION['username'];

    try {
        $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
        $collection = $mongoClient->kanban->tasks;
        
        $result = $collection->insertOne([
            "title" => $title,
            "state" => $state,
            "author" => $author,
            "workers" => $workers,
            "notes" => []
        ]);
        
        if ($result->getInsertedCount() > 0) {
            echo json_encode(["success" => true, "message" => "Tarea creada", "id" => (string)$result->getInsertedId()]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo crear la tarea"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

?>