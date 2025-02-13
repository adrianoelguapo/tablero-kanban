$(document).ready(function () {
    $(".task").draggable({
        revert: "invalid",
        stack: ".task",
        containment: ".home-container",
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

            if (origin !== "idea" && target === "idea") {
                return false;
            }

            if (origin === "idea" && target === "done") {
                return false;
            }

            return true;
        },
        drop: function (event, ui) {
            let $task = $(ui.draggable);

            $task.css({
                top: "auto",
                left: "auto",
                position: "relative",
                opacity: 1,
                zIndex: 100
            });

            $(this).append($task);
            $task.data("origin", $(this).attr("id"));
        }
    });
});
