<?php
session_start();

//retrieve questions from json file

$path = 'inc/questions.json';

if (!file_exists($path)) {
  echo "Question file \"$path\" does not exist.";
  exit;
}

$questions = json_decode(file_get_contents($path));

if(!is_object($questions[0])) {
  echo "Question file \"$path\" does not contain JSON.";
  exit;
}

$totalRounds = count($questions);

$round = filter_input(INPUT_GET,'round',FILTER_SANITIZE_NUMBER_INT);

if (empty($round)) {
  session_destroy();
  $round = 1;
}

// Keep track of which questions have been asked
//My  approach: a question has been asked, when an answer  was submitted
if (isset($_POST['previous_question'])) {
  $previous_question = $_POST['previous_question'];
  $_SESSION['question_answered'][$previous_question] = TRUE;

  var_dump($_SESSION);

}

if ($round > $totalRounds) {
    header('location: results.php');
    exit;
}



//thanks to https://stackoverflow.com/questions/38195517/choose-random-index-of-array-with-condition-on-value for selecting keys from the array with only non answered questions



//validate whether $questions contain objects



$selectedid = array_rand($questions);
//$question = $questions[5];
$question = $questions[$selectedid];

$answers = array();
$answers = [$question->correctAnswer,$question->firstIncorrectAnswer,$question->secondIncorrectAnswer];
shuffle($answers);
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
              <p class="quiz">What is <?php echo $question->leftAdder; ?> + <?php echo $question->rightAdder . " " . $selectedid; ?>?</p>
              <form action="index.php?round=<?php echo ($round+1); ?>" method="post">
                  <input type="hidden" name="previous_question" value="<?php echo $selectedid; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $answers[0]; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $answers[1]; ?>" />
                  <input type="submit" class="btn" name="answer" value="<?php echo $answers[2]; ?>" />
              </form>
          </div>
      </div>
  </body>
  </html>
