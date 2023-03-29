<?php
// Include the database connection file
include('includes/db.php');

// Get the user's ingredient input from the POST data
$ingredients = $_POST['ingredients'];

// Split the ingredient input into an array
$ingredient_array = explode(',', $ingredients);

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.id, r.title, r.instructions, GROUP_CONCAT(i.name SEPARATOR ', ') AS ingredients
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

// Output the matching recipes as a JSON object
$recipes = array();
while ($row = mysqli_fetch_assoc($result)) {
    $recipes[] = array(
        'title' => $row['title'],
        'instructions' => $row['instructions'],
        'ingredients' => $row['ingredients']
    );
}
echo json_encode($recipes);
?>
