$(document).ready(function () {
    $(".task").draggable({
        helper: "clone",
        appendTo: "body",
        revert: "invalid",
        start: function (event, ui) {
            $(this).data("origin", $(this).parent().attr("id"));
            $(this).hide();

            let originalWidth = $(this).outerWidth();
            let originalHeight = $(this).outerHeight();
            ui.helper.css({
                width: originalWidth,
                height: originalHeight,
                transform: "none"
            });
        },
        stop: function (event, ui) {
            $(this).show();
        }
    });

    $(".kanban-column").droppable({
        accept: function (draggable) {
            let origin = $(draggable).data("origin");
            let target = $(this).attr("id");

            if (origin !== "idea" && target === "idea") {
                return false;
            }

            if (target === "done" && origin !== "doing" && origin !== "done") {
                return false;
            }

            return true;
        },
        tolerance: "pointer",
        over: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.2)");
        },
        out: function (event, ui) {
            $(this).css("background", "rgba(255, 255, 255, 0.1)");
        },
        drop: function (event, ui) {
            let $originalTask = ui.draggable;
            $(this).append($originalTask);
            $originalTask.css({
                top: "auto",
                left: "auto",
                position: "relative",
                opacity: 1,
                zIndex: 100
            }).show();
            $(this).css("background", "rgba(255, 255, 255, 0.1)");
            // Actualizar el 'origin' para futuras operaciones:
            let newState = $(this).attr("id");
            $originalTask.data("origin", newState);
            
            // Obtener el _id de la tarea desde el atributo data-id
            let taskId = $originalTask.data("id");
            
            // Enviar la actualización vía AJAX a updateTaskState.php
            $.post("update-task-state.php", { id: taskId, state: newState })
                .done(function(response) {
                    console.log("Actualización exitosa:", response);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error al actualizar:", textStatus, errorThrown);
                });
        }        
    });
});