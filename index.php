<?php
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Get all recipes from the database
$recipes = get_all_recipes();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recipe App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-8">
                <h2>Recipes</h2>

                <?php if (count($recipes) > 0) { ?>
                    <ul class="list-group">
                        <?php foreach ($recipes as $recipe) { ?>
                            <li class="list-group-item">
                                <a href="recipes.php?id=<?php echo $recipe['id']; ?>"><?php echo $recipe['title']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>No recipes found.</p>
                <?php } ?>
            </div>

            <?php if (is_logged_in()) { ?>
                <div class="col-md-4">
                    <h2>Create Recipe</h2>
                    <p><a href="add_recipe.php" class="btn btn-primary">Create New Recipe</a></p>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
