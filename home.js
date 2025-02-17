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
            let newState = $(this).attr("id");
            $originalTask.data("origin", newState);
            let taskId = $originalTask.data("id");
            $.post("update-task-state.php", { id: taskId, state: newState })
                .done(function(response) {
                    console.log("Actualización exitosa:", response);
                });
        }
    });

    let notesModal = $("#notes-modal");
    let notesClose = $(".modal .close").not(".edit-task-close, .collaborators-close, .edit-collaborators-close");
    let currentTaskId = null;

    $(document).on("click", ".notes-button", function (e) {
        e.preventDefault();
        e.stopPropagation();

        currentTaskId = $(this).closest(".task").data("id");
        $("#notes-list").empty();
        notesModal.show();

        $.get("get-notes.php", { id: currentTaskId, t: new Date().getTime() }, function (data) {
            let notesHtml = "";

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
    
    notesClose.on("click", function () {
        notesModal.hide();
    });
    
    $("#add-note-form").on("submit", function (e) {
        e.preventDefault();

        let noteMessage = $(this).find("textarea[name='note-message']").val();

        if (!noteMessage || !currentTaskId) return;

        $.post("add-note.php", { id: currentTaskId, message: noteMessage }, function (response) {
            console.log("Nota añadida:", response);

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

    let addTaskModal = $("#add-task-modal");

    $("#add-task").on("click", function(){
        $("#add-task-form")[0].reset();
        $("#selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        addTaskModal.fadeIn();
    });

    $(".add-task-close").on("click", function(){
        addTaskModal.fadeOut();
    });

    let collaboratorsModal = $("#collaborators-modal");

    $("#select-collaborators").on("click", function(){
        collaboratorsModal.fadeIn();
    });

    $(".collaborators-close").on("click", function(){
        collaboratorsModal.fadeOut();
    });

    $("#save-collaborators").on("click", function(){
        let selected = [];

        $("#collaborators-list input[type='checkbox']:checked").each(function(){
            selected.push($(this).val());
        });

        if(selected.length > 0) {
            $("#selected-collaborators").html("<p>Colaboradores: " + selected.join(", ") + "</p>");
        } else {
            $("#selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        }

        collaboratorsModal.fadeOut();
    });
    
    $("#add-task-form").on("submit", function(e){
        e.preventDefault();

        let title = $("#task-title").val();
        let state = $("input[name='state']").val();
        let collaboratorsText = $("#selected-collaborators p").text();
        let workers = [];

        if(collaboratorsText.indexOf("Colaboradores:") !== -1) {
            workers = collaboratorsText.replace("Colaboradores:", "").trim().split(", ");
        }

        let newTask = {
            title: title,
            state: state,
            workers: workers
        };

        $.post("add-task.php", newTask, function(response){
            location.reload();
        }, "json");
    });
    
    let editTaskModal = $("#edit-task-modal");
    let editCollaboratorsModal = $("#edit-collaborators-modal");
    let currentEditTaskId = null;
    
    $(document).on("click", ".edit-task-button", function(e){
        e.preventDefault();
        e.stopPropagation();

        let $task = $(this).closest(".task");
        currentEditTaskId = $task.data("id");

        let title = $task.data("title") || $task.find(".task-title").text().replace("Task:", "").trim();
        let workers = $task.data("workers") || $task.find(".task-workers").text().replace("Workers:", "").trim();

        $("#edit-task-title").val(title);

        if(workers) {
            $("#edit-selected-collaborators").html("<p>Colaboradores: " + workers + "</p>");
        } else {
            $("#edit-selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        }

        editTaskModal.data("task-id", currentEditTaskId).fadeIn();
    });
    
    $(".edit-task-close").on("click", function(){
        editTaskModal.fadeOut();
    });
    
    $("#edit-select-collaborators").on("click", function(){
        editCollaboratorsModal.fadeIn();
    });

    $(".edit-collaborators-close").on("click", function(){
        editCollaboratorsModal.fadeOut();
    });

    $("#edit-save-collaborators").on("click", function(){
        let selected = [];

        $("#edit-collaborators-list input[type='checkbox']:checked").each(function(){
            selected.push($(this).val());
        });

        if(selected.length > 0) {
            $("#edit-selected-collaborators").html("<p>Colaboradores: " + selected.join(", ") + "</p>");
        } else {
            $("#edit-selected-collaborators").html("<p>No hay colaboradores seleccionados.</p>");
        }

        editCollaboratorsModal.fadeOut();
    });
    
    $("#edit-task-form").on("submit", function(e){
        e.preventDefault();

        let taskId = editTaskModal.data("task-id");
        let newTitle = $("#edit-task-title").val();
        let collaboratorsText = $("#edit-selected-collaborators p").text();
        let workers = [];

        if(collaboratorsText.indexOf("Colaboradores:") !== -1) {
            workers = collaboratorsText.replace("Colaboradores:", "").trim().split(", ");
        }

        $.post("update-task.php", { id: taskId, title: newTitle, workers: workers }, function(response){
            location.reload();
        }, "json");
    });
    
    $("#delete-task").on("click", function(){
        if(confirm("¿Estás seguro de eliminar esta tarea?")) {
            let taskId = editTaskModal.data("task-id");
            $.post("delete-task.php", { id: taskId }, function(response){
                location.reload();
            }, "json");
        }
    });
});
