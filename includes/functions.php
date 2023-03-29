<?php
// Start a new session or resume an existing one
session_start();

// Check if a user is logged in
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

// Redirect the user to the login page if they are not logged in
function require_login()
{
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

// Attempt to log in a user with the specified email address and password
function login_user($email, $password)
{
    global $conn;

    // Find the user with the specified email address
    $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user was found
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $user_name = $row['name'];
        $hashed_password = $row['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;

            return true;
        } else {
            // Password is incorrect
            return false;
        }
    } else {
        // User not found
        return false;
    }
}

// Log out the current user
function logout_user()
{
    // Unset all session variables and destroy the session
    $_SESSION = array();
    session_destroy();
}

// Register a new user with the specified name, email address, and password
function register_user($name, $email, $password)
{
    global $conn;

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Add a new recipe with the specified title, instructions, and ingredients
function add_recipe($title, $instructions, $ingredients)
{
    global $conn;

    // Insert the new recipe into the "recipes" table
    $sql = "INSERT INTO recipes (title, instructions) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $title, $instructions);
    $stmt->execute();

    // Get the ID of the new recipe
    $recipe_id = $stmt->insert_id;

    // Insert the ingredients into the "ingredients" table if they don't already exist, and create links between the recipe and the ingredients in the "recipe_ingredients" table
    foreach ($ingredients as $ingredient_name) {
        // Check if the ingredient already exists
        $sql = "SELECT id FROM ingredients WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ingredient_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Ingredient doesn't exist, insert it into the "ingredients" table
            $sql = "INSERT INTO ingredients (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();

            // Get the ID of the new ingredient
            $ingredient_id = $stmt->insert_id;
        } else {
            // Ingredient already exists, get its ID
            $row = $result->fetch_assoc();
            $ingredient_id = $row['id'];
        }

        // Create a link between the recipe and the ingredient in the "recipe_ingredients" table
        $sql = "INSERT INTO recipe_ingredients (recipe_id, ingredient_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $recipe_id, $ingredient_id);
        $stmt->execute();
    }

    return true;
}


// Update an existing recipe with the specified ID, title, instructions, and ingredients
function update_recipe($recipe_id, $title, $instructions, $ingredients)
{
    global $conn;
    // Update the recipe in the "recipes" table
    $sql = "UPDATE recipes SET title = ?, instructions = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $instructions, $recipe_id);
    $stmt->execute();

    // Delete all existing links between the recipe and ingredients in the "recipe_ingredients" table
    $sql = "DELETE FROM recipe_ingredients WHERE recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();

    // Insert new links between the recipe and ingredients in the "recipe_ingredients" table
    foreach ($ingredients as $ingredient_name) {
        // Check if the ingredient already exists
        $sql = "SELECT id FROM ingredients WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ingredient_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Ingredient doesn't exist, insert it into the "ingredients" table
            $sql = "INSERT INTO ingredients (name) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $ingredient_name);
            $stmt->execute();

            // Get the ID of the new ingredient
            $ingredient_id = $stmt->insert_id;
        } else {
            // Ingredient already exists, get its ID
            $row = $result->fetch_assoc();
            $ingredient_id = $row['id'];
        }

        // Create a link between the recipe and the ingredient in the "recipe_ingredients" table
        $sql = "INSERT INTO recipe_ingredients (recipe_id, ingredient_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $recipe_id, $ingredient_id);
        $stmt->execute();
    }

    return true;
}

// Get all recipes from the database
function get_all_recipes()
{
    global $conn;
    // Get all recipes from the "recipes" table
    $sql = "SELECT * FROM recipes";
    $result = $conn->query($sql);

    $recipes = array();

    if ($result->num_rows > 0) {
        // Loop through all recipes and add them to the array
        while ($row = $result->fetch_assoc()) {
            $recipe_id = $row['id'];
            $recipe_title = $row['title'];
            $recipe_instructions = $row['instructions'];

            // Get all ingredients for the recipe
            $sql = "SELECT ingredients.name FROM ingredients INNER JOIN recipe_ingredients ON ingredients.id = recipe_ingredients.ingredient_id WHERE recipe_ingredients.recipe_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $recipe_id);
            $stmt->execute();
            $result2 = $stmt->get_result();

            $recipe_ingredients = array();

            if ($result2->num_rows > 0) {
                // Loop through all ingredients and add them to the array
                while ($row2 = $result2->fetch_assoc()) {
                    $recipe_ingredients[] = $row2['name'];
                }
            }

            $recipes[] = array(
                'id' => $recipe_id,
                'title' => $recipe_title,
                'instructions' => $recipe_instructions,
                'ingredients' => $recipe_ingredients
            );
        }
    }

    return $recipes;

}

// Get a recipe with the specified ID
function get_recipe($recipe_id)
{
    global $conn;
    // Get the recipe with the specified ID from the "recipes" table
    $sql = "SELECT * FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Recipe found, get its details
        $row = $result->fetch_assoc();
        $recipe_title = $row['title'];
        $recipe_instructions = $row['instructions'];

        // Get all ingredients for the recipe
        $sql = "SELECT ingredients.name FROM ingredients INNER JOIN recipe_ingredients ON ingredients.id = recipe_ingredients.ingredient_id WHERE recipe_ingredients.recipe_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $result2 = $stmt->get_result();

        $recipe_ingredients = array();

        if ($result2->num_rows > 0) {
            // Loop through all ingredients and add them to the array
            while ($row2 = $result2->fetch_assoc()) {
                $recipe_ingredients[] = $row2['name'];
            }
        }

        return array(
            'id' => $recipe_id,
            'title' => $recipe_title,
            'instructions' => $recipe_instructions,
            'ingredients' => $recipe_ingredients
        );
    } else {
        // Recipe not found
        return false;
    }
}
?>