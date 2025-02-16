$(document).ready(function () {
    $(".task").draggable({
        helper: "clone",
        appendTo: "body",
        revert: "invalid",
        start: function (event, ui) {
            // Guardar columna de origen
            $(this).data("origin", $(this).parent().attr("id"));
            // Ocultar el original para que no interfiera con el clon
            $(this).hide();

            // Ajustar el clon al tamaño del original
            let originalWidth = $(this).outerWidth();
            let originalHeight = $(this).outerHeight();
            ui.helper.css({
                width: originalWidth,
                height: originalHeight,
                transform: "none"
            });
        },
        stop: function (event, ui) {
            // Si no se suelta en una zona válida, mostramos el original
            $(this).show();
        }
    });

    $(".kanban-column").droppable({
        /* 
         * Aceptar o rechazar la tarea según el origen/destino.
         * Esto aplica las restricciones:
         *   1. Una vez sale de IDEA, no puede volver.
         *   2. Solo llegar a DONE desde DOING (o reordenar dentro de DONE).
         */
        accept: function (draggable) {
            let origin = $(draggable).data("origin");
            let target = $(this).attr("id");

            // 1. No volver a IDEA si ya ha salido
            if (origin !== "idea" && target === "idea") {
                return false; // Rechaza y revierte
            }

            // 2. Solo pasar a DONE desde DOING (o quedarse en DONE)
            if (target === "done" && origin !== "doing" && origin !== "done") {
                return false; // Rechaza y revierte
            }

            return true; // Acepta en otros casos
        },
        tolerance: "pointer",
        over: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.2)");
        },
        out: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.1)");
        },
        drop: function (event, ui) {
            // Recuperamos el elemento original
            let $originalTask = ui.draggable;
            // Movemos la tarea original a la columna destino
            $(this).append($originalTask);
            // Restablecemos estilos y mostramos
            $originalTask.css({
                top: "auto",
                left: "auto",
                position: "relative",
                opacity: 1,
                zIndex: 100
            }).show();

            // Restablecer color de la columna
            $(this).css("background", "rgba(255, 255, 255, 0.1)");

            // Actualizar el origen para futuros arrastres
            $originalTask.data("origin", $(this).attr("id"));
        }
    });
});