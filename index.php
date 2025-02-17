<?php

    session_start();
    require 'vendor/autoload.php';

    use MongoDB\Client;

    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");

    $userCollection = $mongoClient->kanban->users;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $user = $userCollection->findOne(['username' => $username]);

            if ($user && $password === $user['password']) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user['role'];
                header('Location: home.php');
                exit;
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos.";
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header('Location: index.php');
            exit;
        }
    }

?>

<!DOCTYPE html>
<html>

    <head>

        <meta charset = "UTF-8">
        <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
        <title>Kanban Adriano - Login</title>
        <link rel = "stylesheet" href = "style.css">

    </head>

    <body>

        <div class = "login-container">

            <div class = "login-form">

                <div class = "login-details">

                    <p class = "login-title">Log In</p>
                    <p class = "login-desc">Please enter your account details</p>

                </div>

                <?php if (isset($_SESSION["error"])): ?>

                    <p class = "error-message"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></p>
                
                <?php endif; ?>

                <form method = "post">

                    <label for = "login-email">Username</label>
                    <input type = "text" id = "login-email" name = "username">

                    <label for = "login-pass">Password</label>
                    <input type = "password" id = "login-pass" name = "password">

                    <span>
                        <input type = "submit" value = "Log In" id = "login-submit">
                    </span>

                </form>

                <p class = "login-desc">Don't have an account? <a href = "sign-up.php"><b>Sign Up</b></a></p>

            </div>

        </div>

    </body>

</html>