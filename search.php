<?php
// Include the database connection file
require_once('includes/db.php');

// Get the user's ingredient input from the POST data
$ingredients = $_POST['ingredients'];

// Split the ingredient input into an array
$ingredient_array = explode(',', $ingredients);

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.id, r.name, r.instructions, GROUP_CONCAT(i.name SEPARATOR ', ') AS ingredients
        FROM recipes r
        JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE i.name IN (" . rtrim(str_repeat('?,', count($ingredient_array)), ',') . ")
        GROUP BY r.id
        HAVING COUNT(DISTINCT i.id) = " . count($ingredient_array);

// Prepare the SQL query
$stmt = $conn->prepare($sql);

// Bind the ingredient values to the prepared statement
foreach ($ingredient_array as $key => $value) {
    $stmt->bindValue(($key+1), $value, PDO::PARAM_STR);
}

// Execute the SQL query and retrieve the matching recipes
$stmt->execute();
$recipes = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $recipes[] = array(
        'name' => $row['name'],
        'instructions' => $row['instructions'],
        'ingredients' => $row['ingredients']
    );
}

// Close the database connection
$conn = null;

// Output the matching recipes as a JSON object
echo json_encode($recipes);

// Redirect to the recipes page
header('Location: recipes.php');
exit();
?>
