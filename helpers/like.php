<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$recipe_id = $_GET['id'];
$my_id = $_SESSION['user_id'];
$unlike = $_GET['unlike'] ?? false;
// follow
if (!$unlike) {
    $sql = "INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $recipe_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>  window.location.href='../recipe.php?id=$recipe_id'; </script>";
    } else {
        echo "Error occurred while liking the recipe.";
    }
} else {
    $sql = "DELETE FROM likes WHERE user_id = ? AND recipe_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $recipe_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>  window.location.href='../recipe.php?id=$recipe_id'; </script>";
    } else {
        echo "Error occurred while unliking the recipe.";
    }
}
// unfollow
