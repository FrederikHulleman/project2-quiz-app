<?php
// start session to keep track of the question id's which have been answered
session_start();

include 'inc/functions.php';

$resultMessage = "";

//handle the given answers
if (isset($_POST['previousQuestion']) && isset($_POST['submittedAnswer'])) {
  try {
    $previousQuestion = $_POST['previousQuestion'];
    $submittedAnswer = $_POST['submittedAnswer'];

    list($result,$correctAnswer) = validateAnswer($previousQuestion,$submittedAnswer);

    if ($result) {
      $resultMessage = "Well done";
    }
    else {
      $resultMessage = "Next time better. The correct answer: " . $correctAnswer;
    }

  } catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . "\n";
    exit;
  }


}

//the round keeps track of how far the user got
$round = filter_input(INPUT_GET,'round',FILTER_SANITIZE_NUMBER_INT);

//for the first round, it is initially set to 1
if (empty($round)) {
  try {
    session_destroy();
    session_start();
    //retrieve the full list of questions and store it in the session
    retrieveQuestions();
    $round = 1;
  }
  catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . "\n";
    exit;
  }
}

// after the last round, the user is redirected to the results page
if ($round > $_SESSION['totalRounds']) {
    header('location: results.php');
    exit;
}

//select random question
try {
  $questionDetails = selectQuestion();

}
catch (Exception $e) {
  echo 'Caught exception: ' . $e->getMessage() . "\n";
  exit;
}

?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Math Quiz: Addition</title>
      <link href='https://fonts.googleapis.com/css?family=Playfair+Display:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="css/normalize.css">
      <link rel="stylesheet" href="css/styles.css">
  </head>
  <body>

      <div class="container">
          <div id="quiz-box">
              <p><?php echo $resultMessage; ?></p>
              <p class="breadcrumbs">Question #<?php echo $round; ?> of #<?php echo $_SESSION['totalRounds']; ?></p>
              <p class="quiz">What is <?php echo $questionDetails["leftAdder"]; ?> + <?php echo $questionDetails["rightAdder"]; ?>?</p>
              <form action="index.php?round=<?php echo ($round+1); ?>" method="post">
                  <input type="hidden" name="previousQuestion" value="<?php echo $questionDetails["currentQuestion"]; ?>" />
                  <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["firstAnswer"]; ?>" />
                  <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["secondAnswer"]; ?>" />
                  <input type="submit" class="btn" name="submittedAnswer" value="<?php echo $questionDetails["thirdAnswer"]; ?>" />
              </form>
          </div>
      </div>
  </body>
  </html>
