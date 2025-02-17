$(document).ready(function () {
    // Configuración del draggable y droppable (ya existente)
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
            let newState = $(this).attr("id");
            $originalTask.data("origin", newState);
            let taskId = $originalTask.data("id");
            $.post("update-task-state.php", { id: taskId, state: newState })
                .done(function(response) {
                    console.log("Actualización exitosa:", response);
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error al actualizar:", textStatus, errorThrown);
                });
        }
    });

    // Modal functionality
    var modal = $("#notes-modal");
    var spanClose = $(".modal .close");
    var currentTaskId = null;

    // Usamos event delegation para el clic en .notes-button
    $(document).on("click", ".notes-button", function (e) {
        e.preventDefault();
        e.stopPropagation();
        // Actualizamos el id de la tarea a mostrar
        currentTaskId = $(this).closest(".task").data("id");
        // Limpiar el contenedor de notas
        $("#notes-list").empty();
        // Mostrar el modal
        modal.show();
        // Agregar un parámetro de tiempo para evitar caché
        $.get("get-notes.php", { id: currentTaskId, t: new Date().getTime() }, function (data) {
            var notesHtml = "";
            if (data && data.length > 0) {
                data.forEach(function(note) {
                    notesHtml += "<div class='note'><p><b>" + note.author + ":</b> " + note.message + "</p></div>";
                });
            } else {
                notesHtml = "<p>No hay notas para esta tarea.</p>";
            }
            $("#notes-list").html(notesHtml);
        }, "json");
    });
    

    // Cerrar el modal al hacer clic en el <span class="close">
    spanClose.on("click", function () {
        modal.hide();
    });

    // Cerrar el modal si se hace clic fuera del contenido
    $(window).on("click", function (e) {
        if ($(e.target).is(modal)) {
            modal.hide();
        }
    });

    // Enviar nueva nota
    $("#add-note-form").on("submit", function (e) {
        e.preventDefault();
        var noteMessage = $(this).find("textarea[name='note-message']").val();
        if (!noteMessage || !currentTaskId) return;
        $.post("add-note.php", { id: currentTaskId, message: noteMessage }, function (response) {
            console.log("Nota añadida:", response);
            // Recargar las notas después de añadir
            $.get("get-notes.php", { id: currentTaskId }, function (data) {
                var notesHtml = "";
                if (data && data.length > 0) {
                    data.forEach(function(note) {
                        notesHtml += "<div class='note'><p><b>" + note.author + ":</b> " + note.message + "</p></div>";
                    });
                } else {
                    notesHtml = "<p>No hay notas para esta tarea.</p>";
                }
                $("#notes-list").html(notesHtml);
                $("#add-note-form textarea").val("");
            }, "json");
        });
    });

    // --- Modal "Añadir Tarea" ---
    let addTaskModal = $("#add-task-modal");
    let collaboratorsModal = $("#collaborators-modal");
    
    // Abrir modal de "Añadir Tarea" al hacer clic en el botón correspondiente
    $("#add-task").on("click", function(){
        // Limpiar formulario y colaboradores seleccionados
        $("#add-task-form")[0].reset();
        $("#selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        addTaskModal.fadeIn();
    });
    
    // Cerrar modal "Añadir Tarea"
    $(".add-task-close").on("click", function(){
        addTaskModal.fadeOut();
    });
    
    // Abrir modal de "Seleccionar Colaboradores" desde el modal de tarea
    $("#select-collaborators").on("click", function(){
        collaboratorsModal.fadeIn();
    });
    
    // Cerrar modal de "Seleccionar Colaboradores"
    $(".collaborators-close").on("click", function(){
        collaboratorsModal.fadeOut();
    });
    
    // Guardar la selección de colaboradores y actualizar el modal de tarea
    $("#save-collaborators").on("click", function(){
        var selected = [];
        $("#collaborators-list input[type='checkbox']:checked").each(function(){
            selected.push($(this).val());
        });
        if(selected.length > 0) {
            $("#selected-collaborators").html("<p>Colaboradores: " + selected.join(", ") + "</p>");
        } else {
            $("#selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        }
        collaboratorsModal.hide();
    });
    
    // Enviar el formulario para crear la nueva tarea
    $("#add-task-form").on("submit", function(e){
        e.preventDefault();
        var title = $("#task-title").val();
        var state = $("input[name='state']").val(); // 'idea'
        // Extraer colaboradores del contenido mostrado (quita "Colaboradores: " si es necesario)
        var collaboratorsText = $("#selected-collaborators p").text();
        var workers = [];
        if(collaboratorsText.indexOf("Colaboradores:") !== -1) {
            workers = collaboratorsText.replace("Colaboradores:", "").trim().split(", ");
        }
        
        var newTask = {
            title: title,
            state: state,
            workers: workers
        };
        
        $.post("add-task.php", newTask, function(response){
            // Recargar la página para ver la nueva tarea en el tablero
            location.reload();
        }, "json").fail(function(jqXHR, textStatus, errorThrown){
            console.error("Error al crear tarea:", textStatus, errorThrown);
        });
    });
});
