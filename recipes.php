<?php
// start session
// Include the database connection file
session_start();
require_once('includes/db.php');
// Get the user's ingredient input from the POST data
$ingredients_string = $_POST['ingredients'];
$ingredients = explode(',', $ingredients_string);

$conditions = array();
$params = array();
if (count($ingredients) == 1) {
    $params[":name"] = strtolower(trim($ingredients[0]));
    $conditions[] = "LOWER(name) LIKE :name";
} else {
    foreach ($ingredients as $i => $ingredient) {
        $params[":ingredient$i"] = strtolower(trim($ingredient));
        $conditions[] = "LOWER(ingredients) LIKE :ingredient$i";
    }
}

$conditions_query = implode(' OR ', $conditions);
// Build the SQL query to retrieve the matching recipes
$sql = "SELECT id, name, ingredients
        FROM recipes
        WHERE $conditions_query";

// Prepare the SQL query
$stmt = $conn->prepare($sql);
$stmt->execute($params);


// Execute the SQL query and retrieve the matching recipes
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($result);    

// Close the database connection
$conn = null;

// Output the matching recipes as a JSON object
$recipes = array();
foreach ($result as $row) {
    $recipes[] = array(
        'name' => $row['name'],
        'id' => $row['id'],
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
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>