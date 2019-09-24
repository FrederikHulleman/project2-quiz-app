<?php
// start session to keep track of the question id's which have been answered
session_start();

include 'inc/functions.php';

$resultMessage = "";

if((list($questionsList,$totalRounds) = retrieveQuestions()) === FALSE) {
  echo "The questions could not be displayed. Try again later.";
  exit;
}

// Keep track of which questions have been asked
// My  approach: a question has been asked, when an answer  was submitted and NOT when it is displayed, to avoid strange situations when only refreshing
if (isset($_POST['previousQuestion']) && isset($_POST['submittedAnswer'])) {

  $previousQuestion = $_POST['previousQuestion'];
  $submittedAnswer = $_POST['submittedAnswer'];

  if (validateAnswer($questionsList,$previousQuestion,$submittedAnswer)) {
    $resultMessage = "Well done";
  }
  else {
    $resultMessage = "Next time better";
  }

  // set session array for the previous question answered to true
  $_SESSION['questionAnswered'][$previousQuestion] = TRUE;
}

//the round keeps track of how far the user got
$round = filter_input(INPUT_GET,'round',FILTER_SANITIZE_NUMBER_INT);

//for the first round, it is initially set to 1
if (empty($round)) {
  // for the first round the session array is set will all question id's value FALSE
  $_SESSION['questionAnswered'] = array_fill(0,$totalRounds,FALSE);
  $round = 1;
}

// after the last round, the user is redirected to the results page
if ($round > $totalRounds) {
    header('location: results.php');
    exit;
}

//retrieve questions
if (($questionDetails = selectQuestion($questionsList)) === FALSE) {
  echo "The questions could not be displayed. Try again later.";
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
              <p class="breadcrumbs">Question #<?php echo $round; ?> of #<?php echo $totalRounds; ?></p>
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
