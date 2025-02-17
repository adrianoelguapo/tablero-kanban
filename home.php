<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require 'vendor/autoload.php';
use MongoDB\Client;

$mongoClient = new Client('mongodb+srv://admin:123@cluster0.tz018.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0');
$collection = $mongoClient->kanban->tasks;
$username = $_SESSION['username'];

$tasksCursor = $collection->find([
    '$or' => [
        ['author' => $username],
        ['workers' => $username]
    ]
]);

$ideaTasks = [];
$todoTasks = [];
$doingTasks = [];
$doneTasks = [];

foreach ($tasksCursor as $task) {
    $taskTitle   = $task['title']   ?? 'Sin título';
    $taskAuthor  = $task['author']  ?? 'Desconocido';
    $taskWorkers = isset($task['workers']) ? implode(', ', (array)$task['workers']) : '';
    $state       = $task['state']   ?? 'idea';
    $taskId      = (string)$task['_id'];

    $taskHTML = "
    <div class='task' data-id='{$taskId}' data-origin='{$state}' data-title='" . htmlspecialchars($taskTitle, ENT_QUOTES) . "' data-workers='" . htmlspecialchars($taskWorkers, ENT_QUOTES) . "'>
        <p class='task-title'><b>Task:</b> {$taskTitle}</p>
        <p class='task-author'><b>Author:</b> {$taskAuthor}</p>
        <p class='task-workers'><b>Workers:</b> {$taskWorkers}</p>
        <div class='task-buttons'>
            <button class='notes-button'>Ver Notas</button>";
    if ($username === $taskAuthor) {
        $taskHTML .= "<button class='edit-task-button'>Editar Tarea</button>";
    }
    $taskHTML .= "
            </div>
        </div>";

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
            $ideaTasks[] = $taskHTML;
            break;
    }
}

// Cargar colaboradores desde la colección de usuarios (excluyendo el usuario actual)
$userCollection = $mongoClient->kanban->users;
$usersCursor = $userCollection->find([
    'username' => ['$ne' => $username]
]);

$collaboratorsHtml = "";
foreach ($usersCursor as $user) {
    $userCandidate = $user['username'] ?? '';
    if ($userCandidate) {
        $userCandidateEsc = htmlspecialchars($userCandidate, ENT_QUOTES, 'UTF-8');
        $collaboratorsHtml .= "<label><input type='checkbox' value='{$userCandidateEsc}'> {$userCandidateEsc}</label><br>";
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
            <div class="kanban-column" id="idea">
                <h2>IDEA</h2>
                <?php echo implode("\n", $ideaTasks); ?>
            </div>
            <div class="kanban-column" id="todo">
                <h2>TO DO</h2>
                <?php echo implode("\n", $todoTasks); ?>
            </div>
            <div class="kanban-column" id="doing">
                <h2>DOING</h2>
                <?php echo implode("\n", $doingTasks); ?>
            </div>
            <div class="kanban-column" id="done">
                <h2>DONE</h2>
                <?php echo implode("\n", $doneTasks); ?>
            </div>
        </div>
    </div>

    <!-- Modal para añadir nueva tarea -->
    <div id="add-task-modal" class="modal">
        <div class="modal-content">
            <span class="close add-task-close">&times;</span>
            <h2>Añadir Nueva Tarea</h2>
            <form id="add-task-form">
                <label for="task-title">Título de la Tarea:</label>
                <input type="text" id="task-title" name="title" placeholder="Escribe el título..." required>
                <!-- Estado por defecto "idea" -->
                <input type="hidden" name="state" value="idea">
                <div id="selected-collaborators">
                    <p>No hay colaboradores seleccionados.</p>
                </div>
                <button type="button" id="select-collaborators">Seleccionar Colaboradores</button>
                <button type="submit">Crear Tarea</button>
            </form>
        </div>
    </div>

    <!-- Modal para seleccionar colaboradores para nueva tarea -->
    <div id="collaborators-modal" class="modal">
        <div class="modal-content">
            <span class="close collaborators-close">&times;</span>
            <h2>Seleccionar Colaboradores</h2>
            <div id="collaborators-list">
                <?php echo $collaboratorsHtml; ?>
            </div>
            <button id="save-collaborators">Guardar Selección</button>
        </div>
    </div>

    <!-- Modal para editar tarea -->
    <div id="edit-task-modal" class="modal">
        <div class="modal-content">
            <span class="close edit-task-close">&times;</span>
            <h2>Editar Tarea</h2>
            <form id="edit-task-form">
                <label for="edit-task-title">Título de la Tarea:</label>
                <input type="text" id="edit-task-title" name="title" required>
                <div id="edit-selected-collaborators">
                    <p>No hay colaboradores seleccionados.</p>
                </div>
                <button type="button" id="edit-select-collaborators">Seleccionar Colaboradores</button>
                <button type="submit">Guardar Cambios</button>
                <button type="button" id="delete-task">Eliminar Tarea</button>
            </form>
        </div>
    </div>

    <!-- Modal para seleccionar colaboradores para editar tarea -->
    <div id="edit-collaborators-modal" class="modal">
        <div class="modal-content">
            <span class="close edit-collaborators-close">&times;</span>
            <h2>Seleccionar Colaboradores</h2>
            <div id="edit-collaborators-list">
                <?php echo $collaboratorsHtml; ?>
            </div>
            <button id="edit-save-collaborators">Guardar Selección</button>
        </div>
    </div>

    <!-- Modal para mostrar las notas -->
    <div id="notes-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Notas de la Tarea</h2>
            <div id="notes-list">
                <!-- Aquí se cargarán las notas -->
            </div>
            <form id="add-note-form">
                <textarea name="note-message" placeholder="Escribe una nota..." required></textarea>
                <button type="submit">Añadir Nota</button>
            </form>
        </div>
    </div>

</body>
</html>
