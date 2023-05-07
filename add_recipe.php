<?php
session_start();
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

// Define variables and set to empty values
$name = $description = '';
$name_err = $description_err = '';
$description = '';
$instructions = '';
$image = '';
$ingredients = '';
$recipe = array();
// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate name
    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter a recipe name.';
    } else {
        $name = trim($_POST['name']);
    }

    // Validate description
    if (empty(trim($_POST['description']))) {
        $description_err = 'Please enter a recipe description.';
    } else {
        $description = trim($_POST['description']);
    }

    // Validate instructions
    if (empty(trim($_POST['instructions'] ?? ''))) {
        $instructions_err = 'Please enter recipe instructions.';
    } else {
        $instructions = trim($_POST['instructions']);
    }

    // Validate image
    if (!empty($_FILES['image']['name'])) {
        $max_size = 1 * 1024 * 1024; // 1 MB in bytes
        $file_size = $_FILES['image']['size'];
        $check = getimagesize($_FILES['image']['tmp_name']);
        if (!$check) {
            $image_err = 'File is not an image.';
        } else if ($file_size > $max_size) {
            $image_err = 'File size cannot exceed 1MB.';
        } else {
            // Upload the image
            $target_dir = 'uploads/';
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $image_name = $name . '.' . $image_type;
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $image_name;
            } else {
                $image_err = 'Error uploading image.';
            }
        }
    }

    // Validate ingredients
    foreach ($_POST['ingredient'] as $ingredient) {
        if (!empty(trim($ingredient))) {
            $ingredients .= trim($ingredient) . "\n";
            echo $ingredients;
        }
    }

    // Check for input errors before updating the recipe
    if (empty($name_err) && empty($description_err) && empty($instructions_err) && empty($image_err)) {
        $param_name = $name;
        $param_description = $description;
        $param_instructions = $instructions;
        $param_image = $image;
        $param_ingredients = $ingredients;
        if (isset($_GET['id'])) {
            // Update the existing recipe in the database
            $sql = 'UPDATE recipes SET name = ?, description = ?, instructions = ?, image = ?, ingredients = ? WHERE id = ?';
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(1, $param_name, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_description, PDO::PARAM_STR);
            $stmt->bindParam(3, $param_instructions, PDO::PARAM_STR);
            $stmt->bindParam(4, $param_image, PDO::PARAM_STR);
            $stmt->bindParam(5, $param_ingredients, PDO::PARAM_STR);
            $stmt->bindParam(6, $param_id, PDO::PARAM_INT);

            $param_id = $_GET['id'];
        } else {
            // Insert a new recipe into the database
            $sql = 'INSERT INTO recipes (name, description, instructions, image, ingredients,user_id) VALUES (?, ?, ?, ?, ?, ?)';
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(1, $param_name, PDO::PARAM_STR);
            $stmt->bindParam(2, $param_description, PDO::PARAM_STR);
            $stmt->bindParam(3, $param_instructions, PDO::PARAM_STR);
            $stmt->bindParam(4, $param_image, PDO::PARAM_STR);
            $stmt->bindParam(5, $param_ingredients, PDO::PARAM_STR);
            $stmt->bindParam(6, $_SESSION['user_id'], PDO::PARAM_INT);
        }

        // Bind parameters and execute the statement


        if ($stmt->execute()) {
            if (isset($_GET['id'])) {
                // Recipe updated successfully, redirect to the recipe page
                header('Location: recipe.php?id=' . $_GET['id']);
            } else {
                // Recipe created successfully, redirect to the home page
                header('Location: index.php');
            }
            exit();
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($conn);
} else if (isset($_GET['id'])) {
    // ...
    // Retrieve the existing recipe data
    $sql = 'SELECT name, description, instructions, image, ingredients FROM recipes WHERE id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $param_id, PDO::PARAM_INT);
    $param_id = $_GET['id'];
    $stmt->execute();
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set the existing recipe data to the variables
    $name = $recipe['name'];
    $description = $recipe['description'];
    $instructions = $recipe['instructions'];
    $image = $recipe['image'];
    $ingredients = explode("\n", $recipe['ingredients']);

    // Close statement
    unset($stmt);

    // Close connection
    unset($conn);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Recipe - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h2>Add Recipe</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback">
                            <?php echo $name_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $description_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Instructions</label>
                        <textarea name="instructions" class="form-control <?php echo (!empty($instructions_err)) ? 'is-invalid' : ''; ?>"><?php echo $instructions; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $instructions_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Ingredients</label>

                        <?php
                        // Retrieve the existing ingredients from the form or database

                        // Make sure there is always at least one ingredient field
                        $ingredients = !empty($_POST['ingredient']) ? $_POST['ingredient'] : [];

                        if (count($ingredients) === 0) {
                            $ingredients[] = '';
                        }

                        // Generate the input fields for each ingredient
                        foreach ($ingredients as $key => $value) {
                        ?>
                            <div class="input-group mb-2">
                                <input type="text" name="ingredient[]" class="form-control" value="<?php echo $value; ?>">
                                <div class="input-group-append">
                                    <?php

                                    echo $key;
                                    if (
                                        $key === count($ingredients) - 1
                                    ) { ?>
                                        <button class="btn btn-secondary add-ingredient" type="button">Add</button>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary remove-ingredient" type="button">Remove</button>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>


                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Add Recipe" class="btn btn-primary">
                        <input type="reset" onclick="location.href='add_recipe.php';" href="add_recipe.php" value="Reset" class="btn btn-secondary ml-2">
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $('form').on('click', '.add-ingredient', function() {
            var inputGroup = $(this).closest('.input-group');
            const newInput = inputGroup.clone();
            newInput.find('input').val('');
            inputGroup.after(newInput);
        });
        // Add and remove input fields for ingredients
        // console.log('here')
        // // Add button click handler
        // $('.add-ingredient').click(function() {
        //     console.log('add')
        //     var inputGroup = $(this).closest('.input-group');
        //     const newInput = inputGroup.clone();
        //     newInput.find('input').val('');
        //     inputGroup.after(newInput);
        // });

        // // Remove button click handler
        // $('.remove-ingredient').click(function() {
        //     $(this).closest('.input-group').remove();
        // });
    </script>

</body>

</html>