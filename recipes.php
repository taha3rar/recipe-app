<?php
// Include the database connection file
include('includes/db.php');

// Get the user's ingredient input from the query string
$ingredients = $_GET['ingredients'];

// Split the ingredient input into an array
$ingredient_array = explode(',', $ingredients);

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.id, r.title, r.instructions
        FROM recipes r
        JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE i.name IN ('" . implode("', '", $ingredient_array) . "')
        GROUP BY r.id
        HAVING COUNT(DISTINCT i.id) = " . count($ingredient_array);

// Execute the SQL query and retrieve the matching recipes
$result = mysqli_query($conn, $sql);

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App - Search Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center my-5">Search Results</h1>
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <ul class="list-group">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <li class="list-group-item">
                        <a href="recipe.php?id=<?php echo $row['id']; ?>"><?php echo $row['title']; ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No matching recipes found. Please try again.</p>
        <?php } ?>
    </div>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
