<?php
session_start();

//validate session variables
if(!isset($_SESSION['answerCorrect']) || !isset($_SESSION['totalRounds']) ||
    !is_array($_SESSION['answerCorrect']) || !is_numeric($_SESSION['totalRounds'])) {

      echo "Results could not be displayed";
      exit;
}

// Because each correct answer has value 1, we can sum the total score with array_sum
$countCorrect = array_sum($_SESSION['answerCorrect']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <link href='https://fonts.googleapis.com/css?family=Playfair+Display:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

    <div class="container">
        <div id="quiz-box">
          <p class="result">Results</p>
          <p class="quiz"><?php echo $countCorrect; ?> correct out of <?php echo $_SESSION['totalRounds']; ?></p>
          <button id="start-over" class="btn" onclick="location.href='index.php';" >Start over</button>



        </div>
    </div>
</body>
</html>
