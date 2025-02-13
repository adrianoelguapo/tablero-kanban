$(document).ready(function () {
    $(".task").draggable({
        revert: "invalid", // Solo revierte si no es válido
        stack: ".task",
        containment: ".home-container", // Evita que las tareas salgan del área del tablero
        cursor: "move",
        zIndex: 100,
        start: function (event, ui) {
            $(this).data("origin", $(this).parent().attr("id"));
        }
    });

    $(".kanban-column").droppable({
        accept: function (draggable) {
            let origin = $(draggable).data("origin");
            let target = $(this).attr("id");

            // Restricción: No volver a IDEA
            if (origin !== "idea" && target === "idea") {
                return false;
            }

            // Restricción: No mover directamente de IDEA a DONE
            if (origin === "idea" && target === "done") {
                return false;
            }

            return true;
        },
        drop: function (event, ui) {
            let $task = $(ui.draggable);

            // Resetear posición relativa al contenedor al hacer el drop
            $task.css({
                top: "auto",
                left: "auto",
                position: "relative"
            });

            // Insertar la tarea en la columna correcta
            $(this).append($task);
            $task.data("origin", $(this).attr("id"));
        }
    });
});
