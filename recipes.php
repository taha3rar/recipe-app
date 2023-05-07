<?php
// start session
// Include the database connection file
session_start();
require_once('includes/db.php');
// Get the user's ingredient input from the POST data
$ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : array();
print_r($_POST);

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.id, r.name, r.instructions, GROUP_CONCAT(i.name SEPARATOR ', ') AS ingredients
        FROM recipes r
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE i.name IN (" . rtrim(str_repeat('?,', count($ingredients)), ',') . ")
        GROUP BY r.id
        HAVING COUNT(DISTINCT i.id) = " . count($ingredients);

// Prepare the SQL query
$stmt = $conn->prepare($sql);

// Bind the ingredient values to the prepared statement
foreach ($ingredients as $key => $value) {
    $stmt->bindValue(($key + 1), $value, PDO::PARAM_STR);
}

// Execute the SQL query and retrieve the matching recipes
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;

// Output the matching recipes as a JSON object
$recipes = array();
foreach ($result as $row) {
    $recipes[] = array(
        'name' => $row['name'],
        'instructions' => $row['instructions'],
        'ingredients' => $row['ingredients']
    );
}

echo json_encode($recipes);
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
        <?php if ($recipes && count($recipes) > 0) { ?>
            <ul class="list-group">
                <?php foreach ($recipes as $recipe) { ?>
                    <li class="list-group-item">
                        <a href="recipe.php?id=<?php echo $recipe['id']; ?>"><?php echo $recipe['name']; ?></a>
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