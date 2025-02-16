<?php
session_start();

// Si no existe el usuario en sesión, redirigir a index.php (o el login)
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require 'vendor/autoload.php'; // Ajusta la ruta según tu proyecto
use MongoDB\Client;

// Conectar a MongoDB Atlas
$mongoClient = new Client('mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');

// Seleccionar la base de datos y la colección
$collection = $mongoClient->kanban->tasks;

// Obtener el usuario de la sesión
$username = $_SESSION['username'];

// Consultar tareas donde el usuario sea autor o esté en workers
$tasksCursor = $collection->find([
    '$or' => [
        ['author' => $username],
        ['workers' => $username]
    ]
]);

// Preparar arrays para cada columna
$ideaTasks = [];
$todoTasks = [];
$doingTasks = [];
$doneTasks = [];

// Recorrer los documentos y crear el HTML de cada tarea
foreach ($tasksCursor as $task) {
    // Extraer campos
    $taskTitle   = $task['title']   ?? 'Sin título';
    $taskAuthor  = $task['author']  ?? 'Desconocido';
    $taskWorkers = isset($task['workers']) ? implode(', ', (array)$task['workers']) : '';
    $state       = $task['state']   ?? 'idea'; // Por defecto idea

    // Construir el bloque HTML de la tarea
    $taskHTML = "
    <div class='task' data-origin='{$state}'>
        <p class='task-title'><b>Task:</b> {$taskTitle}</p>
        <p class='task-author'><b>Author:</b> {$taskAuthor}</p>
        <p class='task-workers'><b>Workers:</b> {$taskWorkers}</p>
    </div>";

    // Colocar la tarea en el array de la columna correspondiente
    switch ($state) {
        case 'idea':
            $ideaTasks[] = $taskHTML;
            break;
        case 'todo':
            $todoTasks[] = $taskHTML;
            break;
        case 'doing':
            $doingTasks[] = $taskHTML;
            break;
        case 'done':
            $doneTasks[] = $taskHTML;
            break;
        default:
            // Si el estado no coincide con ninguno de los anteriores,
            // podrías ponerlo en 'idea' por defecto o ignorarlo
            $ideaTasks[] = $taskHTML;
            break;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Adriano - Home</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="home.js" defer></script>
</head>
<body>

<div class="max-container">

    <div class="home-container">

        <div class="header-container">

            <div class="user-container">
                <img src="images/users.svg" alt="user-icon">
                <p><?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <div class="buttons-container">
                <button class="button" id="add-task">Add task</button>
                <button class="button" id="log-out">
                    <a href="logout.php">Log Out</a>
                </button>
            </div>

        </div>

    </div>

    <div class="kanban-container">

        <!-- Columna IDEA -->
        <div class="kanban-column" id="idea">
            <h2>IDEA</h2>
            <?php
            // Imprimir las tareas de 'idea'
            echo implode("\n", $ideaTasks);
            ?>
        </div>

        <!-- Columna TO DO -->
        <div class="kanban-column" id="todo">
            <h2>TO DO</h2>
            <?php
            // Imprimir las tareas de 'todo'
            echo implode("\n", $todoTasks);
            ?>
        </div>

        <!-- Columna DOING -->
        <div class="kanban-column" id="doing">
            <h2>DOING</h2>
            <?php
            // Imprimir las tareas de 'doing'
            echo implode("\n", $doingTasks);
            ?>
        </div>

        <!-- Columna DONE -->
        <div class="kanban-column" id="done">
            <h2>DONE</h2>
            <?php
            // Imprimir las tareas de 'done'
            echo implode("\n", $doneTasks);
            ?>
        </div>

    </div>

    <footer>Developed by Adriano ©</footer>

</div>

</body>
</html>
