<?php

    require 'vendor/autoload.php';
    use MongoDB\Client;
    use MongoDB\BSON\ObjectId;

    $id = $_POST['id'];
    $newState = $_POST['state'];

    try {
        $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
        $collection = $mongoClient->kanban->tasks;
        
        $result = $collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => ['state' => $newState]]
        );
        
        if ($result->getModifiedCount() > 0) {
            echo json_encode(["success" => true, "message" => "Estado actualizado"]);
        } else {
            echo json_encode(["success" => false, "message" => "No se actualizó el estado o no hubo cambios"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

?>