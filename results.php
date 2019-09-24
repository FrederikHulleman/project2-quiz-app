<?php
session_start();
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
          <p class="breadcrumbs">Results</p>
          <p class="quiz"><?php echo $countCorrect; ?> correct out of <?php echo $_SESSION['totalRounds']; ?></p>

            <!-- <p><?php echo $resultMessage; ?></p>
            <p class="breadcrumbs">Question #<?php echo $round; ?> of #<?php echo $_SESSION['totalRounds']; ?></p>
            <p class="quiz">What is <?php echo $questionDetails["leftAdder"]; ?> + <?php echo $questionDetails["rightAdder"]; ?>?</p>
            <form action="index.php?round=<?php echo ($round+1); ?>" method="post">
                <input type="hidden" name="previousQuestion" value="<?php echo $questionDetails["currentQuestion"]; ?>" />
                <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["firstAnswer"]; ?>" />
                <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["secondAnswer"]; ?>" />
                <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["thirdAnswer"]; ?>" />
            </form> -->


        </div>
    </div>
</body>
</html>
