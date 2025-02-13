<?php
require 'vendor/autoload.php'; // Cargar la librería de MongoDB

use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a MongoDB Atlas
    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    
    $database = $mongoClient->kanban; // Seleccionar la base de datos
    $collection = $database->users;   // Seleccionar la colección

    // Obtener datos del formulario
    $username = trim($_POST["sign-up-username"]);
    $password = trim($_POST["sign-up-password"]);

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Verificar si el usuario ya existe
    $existingUser = $collection->findOne(["username" => $username]);

    if ($existingUser) {
        die("Error: El nombre de usuario ya está en uso. Intenta con otro.");
    }

    // Insertar el nuevo usuario con la contraseña en texto plano (⚠️ No recomendado en producción)
    $result = $collection->insertOne([
        "username" => $username,
        "password" => $password
    ]);

    if ($result->getInsertedCount() > 0) {
        header("Location: index.html");
        exit();
    } else {
        echo "Error: No se pudo registrar el usuario.";
    }
}
?>
