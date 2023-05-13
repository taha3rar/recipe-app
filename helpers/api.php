<?php
// start session
// Include the database connection file
session_start();
require_once('../includes/db.php');
$api_key = "503b3e8bf94941519b3b7f1f72e9ac6b";
// Get the user's ingredient input from the POST data
$ingredients = $_POST['ingredients'];
$url = 'https://api.spoonacular.com/recipes/findByIngredients?ingredients=' . $ingredients . '&apiKey=' . $api_key;
$response = file_get_contents($url);
$data = array();
if ($response === false) {
    // Handle error
    echo 'Error retrieving data from the API. maybe max limit reached';
} else {

    // Process the API response
    $data = json_decode($response, true);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Search results Spoonacular
    </title>
    <base href="/recipe-app/">

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/recipes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <?php include_once('../includes/header.php'); ?>
    <div class="container">
        <h1 class="text-center my-5">
            Found Recipes
        </h1>

        <?php if ($data && count($data) > 0) { ?>
            <div class="row">
                <?php foreach ($data as $recipe) { ?>
                    <div class="col-md-4 ">
                        <div class="card" style="border-radius: 15px;">
                            <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
                                <img src="<?php echo $recipe['image'] ?>" style="border-top-left-radius: 15px; border-top-right-radius: 15px;" class="img-fluid" alt="image" />

                            </div>
                            <div class="card-body pb-0">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p><a id="<?php echo $recipe['id'] ?>" role="button" class="main-name main-color recipe-click">
                                                <?php echo $recipe['title'] ?>
                                            </a></p>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-0" />
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
                                    <p class="text-dark fw-bold">
                                        By:
                                        Spoonacular API</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        <?php } else { ?>
            <p>No matching recipes found. Please try again.</p>
        <?php } ?>


    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $('.recipe-click').click(function() {
            var id = $(this).attr('id');
            const url = ('https://api.spoonacular.com/recipes/' + id + '/information?includeNutrition=false' + '&apiKey=503b3e8bf94941519b3b7f1f72e9ac6b');
            // call this request and open the response.sourceUrl in a new tab

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    var sourceUrl = response.sourceUrl;
                    if (sourceUrl) {
                        window.open(sourceUrl, '_blank');
                    } else {
                        console.log('Recipe URL not available');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error retrieving recipe information:', error);
                }
            });

        })
    </script>
</body>

</html>