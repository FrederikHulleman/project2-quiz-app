<?php
session_start();

include 'inc/functions.php';


// Keep track of which questions have been asked
//My  approach: a question has been asked, when an answer  was submitted
if (isset($_POST['previousQuestion'])) {
  $previousQuestion = $_POST['previousQuestion'];
  $_SESSION['questionAnswered'][$previousQuestion] = TRUE;
  // echo "previous: "  . $previousQuestion . "<br>";
  var_dump($_SESSION);

}

//retrieve questions
if ((list($questionDetails,$currentQuestion,$totalRounds) = selectQuestion()) === FALSE) {
  echo "STOP";
  exit;
}

$round = filter_input(INPUT_GET,'round',FILTER_SANITIZE_NUMBER_INT);

if (empty($round)) {
  session_destroy();
  session_start();
  $_SESSION['questionAnswered'] = array_fill(0,$totalRounds,FALSE);
  var_dump($_SESSION);
  $round = 1;
}


if ($round > $totalRounds) {
    header('location: results.php');
    exit;
}



//thanks to https://stackoverflow.com/questions/38195517/choose-random-index-of-array-with-condition-on-value for selecting keys from the array with only non answered questions



//validate whether $questions contain objects




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
              <p class="breadcrumbs">Question #<?php echo $round; ?> of #<?php echo $totalRounds; ?></p>
              <p class="quiz">What is <?php echo $questionDetails["leftAdder"]; ?> + <?php echo $questionDetails["rightAdder"] . " " . $currentQuestion; ?>?</p>
              <form action="index.php?round=<?php echo ($round+1); ?>" method="post">
                  <input type="hidden" name="previousQuestion" value="<?php echo $currentQuestion; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $questionDetails["firstAnswer"]; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $questionDetails["secondAnswer"]; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $questionDetails["thirdAnswer"]; ?>" />
              </form>
          </div>
      </div>
  </body>
  </html>
