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
$email = $password = '';
$email_err = $password_err = '';

// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter an email address.';
    } else {
        $email = trim($_POST['email']);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } else {
        $password = trim($_POST['password']);
    }

    // Check for input errors before logging in
    if (empty($email_err) && empty($password_err)) {

        // Check if the email and password are correct
        $sql = 'SELECT id, email, password FROM users WHERE email = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $param_email);
        $param_email = $email;
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Password is correct, log in the user
                $_SESSION['user_id'] = $row['id'];
                header('Location: index.php');
                exit();
            } else {
                // Password is not correct, show error message
                $password_err = 'The password you entered is not valid.';
            }
        } else {
            // Email address not found, show error message
            $email_err = 'No account found with that email address.';
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h2>Login</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"
                            class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $email; ?>">
                        <span class="invalid-feedback">
                            <?php echo $email_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password"
                            class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Login" class="btn btn-primary">
                        <input type="reset" value="Reset" class="btn btn-secondary ml-2">
                    </div>
                    <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>

                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>