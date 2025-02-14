<?php

    session_start();

?>

<!DOCTYPE html>
<html>

    <head>

        <meta charset = "UTF-8">
        <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
        <title>Kanban Adriano - Home</title>
        <link rel = "stylesheet" href = "style.css">
        <script src = "https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src = "https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src = "home.js" defer></script>

    </head>

    <body>

        <div class = "max-container">

            <div class = "home-container">

                <div class = "header-container">

                    <div class = "user-container">

                        <img src = "images/users.svg" alt = "user-icon">
                        <p><?php echo $_SESSION['username'];?></p>

                    </div>

                    <div class = "buttons-container">

                        <button class = "button" id = "add-task">Add task</button>
                        <button class = "button" id = "log-out"><a href = "logout.php">Log Out</a></button>

                    </div>

                </div>

            </div>

            <div class = "kanban-container">

                <div class = "kanban-column" id = "idea">

                    <h2>IDEA</h2>

                    <div class = "task" data-origin = "idea">

                        <p class = "task-title"><b>Task:</b> Tablero Kanban</p>
                        <p class = "task-author"><b>Author:</b> adriano</p>
                        <p class = "task-workers"><b>Workers:</b> bruno, orto, noel</p>

                    </div>

                    <div class = "task" data-origin = "idea">

                        <p class = "task-title"><b>Task:</b> Tablero Kanban</p>
                        <p class = "task-author"><b>Author:</b> adriano</p>
                        <p class = "task-workers"><b>Workers:</b> bruno, orto, noel</p>

                    </div>

                    <div class = "task" data-origin = "idea">

                        <p class = "task-title"><b>Task:</b> Tablero Kanban</p>
                        <p class = "task-author"><b>Author:</b> adriano</p>
                        <p class = "task-workers"><b>Workers:</b> bruno, orto, noel</p>

                    </div>

                    <div class = "task" data-origin = "idea">

                        <p class = "task-title"><b>Task:</b> Tablero Kanban</p>
                        <p class = "task-author"><b>Author:</b> adriano</p>
                        <p class = "task-workers"><b>Workers:</b> bruno, orto, noel</p>

                    </div>
    
                </div>

                <div class = "kanban-column" id = "todo">
    
                    <h2>TO DO</h2>
    
                </div>

                <div class = "kanban-column" id = "doing">
    
                    <h2>DOING</h2>
    
                </div>

                <div class = "kanban-column" id = "done">
    
                    <h2>DONE</h2>
    
                </div>

            </div>

            <footer>Developed by Adriano ©</footer>

        </div>

    </body>

</html>
