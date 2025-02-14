$(document).ready(function () {
    $(".task").draggable({
        revert: "invalid",
        stack: ".task",
        cursor: "move",
        zIndex: 1000,
        start: function (event, ui) {
            $(this).data("origin", $(this).parent().attr("id"));
            $(this).css({
                position: "absolute", /* 🔥 Evita que la tarea quede atrapada */
                width: $(this).outerWidth() /* Mantiene el tamaño original */
            });
        },
        stop: function (event, ui) {
            $(this).css({
                position: "relative", /* 🔥 Vuelve a su posición normal */
                width: "90%" /* Asegura que la tarea no se deforme */
            });
        }
    });

    $(".kanban-column").droppable({
        accept: ".task",
        tolerance: "pointer", /* 🔥 Detecta mejor cuando se suelta la tarea */
        over: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.2)"); /* Efecto al entrar */
        },
        out: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.1)"); /* Restaura */
        },
        drop: function (event, ui) {
            let $task = $(ui.draggable);
            $(this).append($task);

            $task.css({
                top: "auto",
                left: "auto",
                position: "relative", /* 🔥 Importante para que la tarea quede bien posicionada */
                opacity: 1,
                zIndex: 100
            });

            $(this).css("background", "rgba(255, 255, 255, 0.1)"); /* Restaura color */
            $task.data("origin", $(this).attr("id"));
        }
    });
});
