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