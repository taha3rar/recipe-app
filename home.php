<?php
// Include the necessary files
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');
if (!isset($_SESSION['user_id'])) {
    // Redirect to auth.php
    header('Location: landing-page.php');
    exit(); // Make sure to exit the script after the redirect
  }

// Get all recipes from the database
$recipes = get_all_recipes();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dessfits</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div <?php
                if (isset($_SESSION['user_id'])) { ?> class="row">
            <div class="col-md-8">
                <h2>Recipes</h2>

                <form action="recipes.php" method="POST" class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" placeholder="Search recipes by ingredients" name="ingredients">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>

                <?php if (count($recipes) > 0) { ?>
                    <ul class="list-group">
                        <?php foreach ($recipes as $recipe) { ?>
                            <li class="list-group-item">
                                <a href="recipes.php?id=<?php echo $recipe['id']; ?>"><?php echo $recipe['name']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>No recipes found.</p>
                <?php } ?>
            </div>
        <?php } ?>


        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>