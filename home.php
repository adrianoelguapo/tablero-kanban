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

        <div class = "home-container">

            <div class = "buttons-container">

                <button class = "button" id = "add-task">Add task</button>
                <button class = "button" id = "log-out"><a href = "logout.php">Log Out</a></button>
    
            </div>

            <div class = "kanban-container">

                <div class = "kanban-column" id = "idea">

                    <h2>IDEA</h2>

                    <div class = "task" data-origin = "idea">

                        <p class = "task-title">Tablero Kanban - Adriano</p>

                        <div class = "task-buttons">

                            <button class = "task-button" id = "show-notes">Notas</button>
                            <button class = "task-button second-task-button" id = "show-workers">Colaboradores</button>

                        </div>

                    </div>

                    <div class = "task" data-origin = "idea">Tarea 2</div>
    
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

            </div>

    </body>

</html>
