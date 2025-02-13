<?php

    require 'vendor/autoload.php';

    use MongoDB\Client;

    $mongoClient = new Client("mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0");

    $userCollection = $mongoClient->kanban->users;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($username && $password) {
            $user = $userCollection->findOne(['username' => $username]);

            if ($user && $password === $user['password']) {
                header('Location: home.html');
                exit;
            } else {
                header('Location: index.html');
                exit;
            }
        } else {
            header('Location: index.html');
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

            <div class = "login-form" action = "login.php" method = "post">

                <div class = "login-details">
    
                    <p class = "login-title">Log In</p>
                    <p class = "login-desc">Please enter your account details</p>
    
                </div>
    
                <form method = "post" action = "login.php">
    
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