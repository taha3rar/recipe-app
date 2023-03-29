$(document).ready(function() {

    // Show confirmation dialog when deleting a recipe
    $('.delete-link').click(function(event) {
        if (!confirm("Are you sure you want to delete this recipe?")) {
            event.preventDefault();
        }
    });

});
