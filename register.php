<?php
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is already logged in
if (is_logged_in()) {
    header('Location: index.php');
    exit();
}

// Define variables and set to empty values
$username = $password = $confirm_password = '';
$username_err = $password_err = $confirm_password_err = '';

// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } else {
        // Check if the username is already taken
        $sql = 'SELECT id FROM users WHERE username = ?';
        $stmt = $conn->prepare($sql);
        $param_username = trim($_POST['username']);
        $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            $username_err = 'This username is already taken.';
        } else {
            $username = trim($_POST['username']);
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must have at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_err = 'Passwords do not match.';
        }
    }

    // Check for input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = 'INSERT INTO users (username, password) VALUES (?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
        $stmt->bindParam(2, $param_password, PDO::PARAM_STR);
        $param_username = $username;
        $param_password = $hashed_password;

        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header('Location: login.php');
            exit();
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        // Close statement
        $stmt->closeCursor();
    }

    // Close connection
    $conn = null;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h2>Register</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback">
                            <?php echo $username_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback">
                            <?php echo $confirm_password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Register" class="btn btn-primary">
                        <input type="reset" value="Reset" class="btn btn-secondary ml-2">
                    </div>

                    <p>Already have an account? <a href="login.php">Login here</a>.</p>

                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>