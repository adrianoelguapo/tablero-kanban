<?php
session_start();
require 'vendor/autoload.php';

use MongoDB\Client;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");
    
    $database = $mongoClient->kanban;
    $collection = $database->users;

    $username = trim($_POST["sign-up-username"]);
    $password = trim($_POST["sign-up-password"]);

    // Validar campos vacíos
    if (empty($username) || empty($password)) {
        $_SESSION["error"] = "Todos los campos son obligatorios.";
        header("Location: sign-up.php");
        exit();
    }

    // Verificar si el usuario ya existe
    $existingUser = $collection->findOne(["username" => $username]);

    if ($existingUser) {
        $_SESSION["error"] = "El nombre de usuario ya está en uso. Intenta con otro.";
        header("Location: sign-up.php");
        exit();
    }

    // Insertar el nuevo usuario
    $result = $collection->insertOne([
        "username" => $username,
        "password" => $password
    ]);

    if ($result->getInsertedCount() > 0) {
        $_SESSION["success"] = "Registro exitoso. Ahora puedes iniciar sesión.";
        header("Location: sign-up.php");
        exit();
    } else {
        $_SESSION["error"] = "Error: No se pudo registrar el usuario.";
        header("Location: sign-up.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

    <head>

        <meta charset = "UTF-8">
        <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
        <title>Kanban Adriano - Sign Up</title>
        <link rel = "stylesheet" href = "style.css">
        
    </head>

    <body>

        <div class = "login-container">

            <div class = "login-form">

                <div class = "login-details">

                    <p class = "login-title">Sign Up</p>
                    <p class = "login-desc">Please enter your account details</p>

                </div>

                <!-- Mostrar errores o mensajes de éxito -->
                <?php if (isset($_SESSION["error"])): ?>
                    <p class="error-message"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></p>
                <?php endif; ?>
                <?php if (isset($_SESSION["success"])): ?>
                    <p class="success-message"><?php echo $_SESSION["success"]; unset($_SESSION["success"]); ?></p>
                <?php endif; ?>

                <form method = "post" action = "sign-up.php">

                    <label for = "sign-up-email">Username</label>
                    <input type = "text" id = "sign-up-email" name = "sign-up-username">

                    <label for = "sign-up-pass">Password</label>
                    <input type = "password" id = "sign-up-pass" name = "sign-up-password">

                    <span>

                        <input type = "submit" value = "Sign Up" id = "sign-up-submit">

                    </span>

                </form>

                <p class = "login-desc">Already have an account? <a href = "index.php"><b>Log In</b></a></p>

            </div>

        </div>

    </body>

</html>
