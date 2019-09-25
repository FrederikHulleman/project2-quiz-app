<?php
//------------------- 1. INITIATION -----------------------------------------------------

// start session to keep track of the question id's which have been answered
session_start();

include 'inc/functions.php';

//the round keeps track of how far the user got
$round = filter_input(INPUT_GET,'round',FILTER_SANITIZE_NUMBER_INT);

//For each question 2 pages are shown: 1st with the 3 answers, 2nd with the outcome

//------------------- 2. SHOW SAME QUESTION & OUTCOME -----------------------------------------------------

if (isset($_POST['previousQuestion']) && isset($_POST['submittedAnswer'])) {
  //handle the given answers and show the 2nd page with the outcome
  $previousQuestion = $_POST['previousQuestion'];
  $submittedAnswer = $_POST['submittedAnswer'];

  //validate the answer
  try {
    list($result,$correctAnswer) = validateAnswer($previousQuestion,$submittedAnswer);

  } catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . "\n";
    exit;
  }

  //write the result message
  $resultMessage = '<p class="result ';

  if ($result) {
    $resultMessage .= 'correct">Well done';
  } else {
    $resultMessage .= 'incorrect">Better luck next time.'
                      . '<br>Your answer: ' . $submittedAnswer
                      . '<br>The correct answer: ' . $correctAnswer;
  }

  $resultMessage .= '</p>';

  //validate whether it's the last round or not. And adjust the button text, name and form action based on this validation.
  if ($round == $_SESSION['totalRounds']) {
    $btnText = 'Show final score';
    $btnName = 'ShowFinalScore';
    $formAction = 'results.php';
  } else {
    $btnText = 'Next question';
    $btnName = 'NextQuestion';
    $formAction = 'index.php?round=' . ($round+1);
  }

  //write the full form string with the question, the result and the button
  $formHTML = '<p class="quiz">What is ' . $_SESSION['questionDetails'][$previousQuestion]['leftAdder'] . ' + ' . $_SESSION['questionDetails'][$previousQuestion]['rightAdder'] . '?</p>'
                . $resultMessage
                . '<form action="'.$formAction .'" method="post">
                    <input type="submit" class="btn" name="'.$btnName.'" value="'.$btnText.'" />
                </form>';

//------------------- 3. SHOW QUESTION & ANSWERS -----------------------------------------------------

} else  {
  //since no answer was submitted a new question should be shown

  //for the first round, it is initially set to 1
  if (empty($round)) {
    $round = 1;
    //since it's the first round, previous sessions should be killed and a new session should be started
    session_destroy();
    session_start();

    try {
      //retrieve the full list of questions and store it in the session
      retrieveQuestions();
    }
    catch (Exception $e) {
      echo 'Caught exception: ' . $e->getMessage() . '\n';
      exit;
    }
  }

  //select random question
  try {
    $questionDetails = selectQuestion();
  }
  catch (Exception $e) {
    echo 'Caught exception: ' . $e->getMessage() . '\n';
    exit;
  }
  //write the full form string with the question and the answers
  $formHTML = '<p class="quiz">What is ' . $questionDetails['leftAdder'] . ' + ' . $questionDetails['rightAdder'] . '?</p>
                <form action="index.php?round=' . $round . '" method="post">
                    <input type="hidden" name="previousQuestion" value="' . $questionDetails['currentQuestion'] . '" />
                    <input type="submit" class="btn" name="submittedAnswer" value="'.$questionDetails['firstAnswer'] . '" />
                    <input type="submit" class="btn" name="submittedAnswer" value="'.$questionDetails['secondAnswer'] .'" />
                    <input type="submit" class="btn" name="submittedAnswer" value="'.$questionDetails['thirdAnswer'] .'" />
                </form>';

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
              <p class="breadcrumbs">Question #<?php echo $round; ?> of #<?php echo $_SESSION['totalRounds']; ?></p>

              <?php echo $formHTML; ?>

          </div>
      </div>
  </body>
  </html>
