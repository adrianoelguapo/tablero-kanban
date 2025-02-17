<?php

    header('Content-Type: application/json');
    require 'vendor/autoload.php';
    use MongoDB\Client;
    use MongoDB\BSON\ObjectId;

    $id = $_GET['id'];

    try {
        $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
        $collection = $mongoClient->kanban->tasks;
        
        $task = $collection->findOne(['_id' => new ObjectId($id)]);
        if (!$task) {
            echo json_encode([]);
            exit();
        }
        
        $notes = isset($task['notes']) ? (array)$task['notes'] : [];
        
        if (!empty($notes)) {
            $notes = array_reverse($notes);
            $notes = array_slice($notes, 0, 5);
        }
        
        echo json_encode($notes);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }

?>
