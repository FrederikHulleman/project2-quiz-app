<?php

function retrieveQuestions() {

  // created this function, so I'm flexible when I want to change the source of the $questions
  // so i won't have to change index.php

  // path to JSON file with questions
  $path = 'inc/questions.json';

  //validate whether path exists, to avoid errors in the json_decode later
  if (!file_exists($path)) {
    throw new Exception('Path to JSON file does not exist.');
  }

  // create questions objects from json file
  $_SESSION['questionDetails'] = json_decode(file_get_contents($path));

  // validate whether questions contain objects
  if(!is_object($_SESSION['questionDetails'][0])) {
    throw new Exception('File does not contain valid JSON objects.');
  }

  // calculate the total number of rounds
  $_SESSION['totalRounds'] = count($_SESSION['questionDetails']);

  // for the first round the session array is set will all question id's value FALSE
  $_SESSION['questionAnswered'] = array_fill(0,$_SESSION['totalRounds'],FALSE);

  // create session variable for full question list
  // foreach ($questionsList as $key=>$question) {
  //   $_SESSION['questionDetails'][$key] = $question;
  // }

  // var_dump($_SESSION);

  return TRUE;
}

function validateAnswer($previousQuestion,$answer) {

  if(!isset($previousQuestion) || !isset($answer) || !is_numeric($previousQuestion) || !is_numeric($answer)) {
    throw new Exception('No valid input for function validateAnswer');
  }

  if(!isset($_SESSION['questionAnswered']) || !isset($_SESSION['questionDetails']) ||
        !is_array($_SESSION['questionAnswered']) || !is_array($_SESSION['questionDetails'])) {
    throw new Exception('Session variables not set properly');
  }

  $correctAnswer = $_SESSION['questionDetails'][$previousQuestion]->correctAnswer;

  // set session array for the previous question answered to true
  $_SESSION['questionAnswered'][$previousQuestion] = TRUE;
  
  if ($correctAnswer == $answer) {
    return TRUE;
  } else {
    return FALSE;
  }

}

function selectQuestion()  {

  if(!isset($_SESSION['questionAnswered']) || !isset($_SESSION['questionDetails']) ||
        !is_array($_SESSION['questionAnswered']) || !is_array($_SESSION['questionDetails'])) {
    throw new Exception('Session variables not set properly');
  }

  // create new array  for all relevant question details, which will also contain the answers in random order, which is not possible in the object properties
  $questionDetails = Array();

  // select the keys of the questions which have not been answered yet
  // thanks to https://stackoverflow.com/questions/38195517/choose-random-index-of-array-with-condition-on-value for selecting keys from the array with only non answered questions
  $remainingQuestions = array_keys($_SESSION['questionAnswered'],FALSE);

  // randomly select ID of one of the remaining questions
  $questionDetails['currentQuestion'] = $remainingQuestions[array_rand($remainingQuestions)];

  // copy details from object to array
  $questionDetails['rightAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->rightAdder;
  $questionDetails['leftAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->leftAdder;

  // the part to put the answers in random order, making use of shuffle
  $answers = array();
  $answers = [
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->correctAnswer,
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->firstIncorrectAnswer,
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->secondIncorrectAnswer
            ];
  shuffle($answers);

  // adding the randomly ordered answers to the question details array
  $questionDetails['firstAnswer'] = $answers[0];
  $questionDetails['secondAnswer'] = $answers[1];
  $questionDetails['thirdAnswer'] = $answers[2];

  // return the question details, the ID of the selected question (currentQuestion) and the total number of rounds
  return $questionDetails;
}

?>
