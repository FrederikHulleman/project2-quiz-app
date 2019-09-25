<?php

// a one time function to retrieve all questions and answers, store it in the session variables and prep some other stuff, like 'totalRounds'
function retrieveQuestions() {

  // created this function, so I'm flexible when I want to change the source of the $questions
  // so i won't have to change index.php

  // path to JSON file with questions
  $path = 'inc/questions.json';

  //validate whether path exists, to avoid errors in the json_decode later
  if (!file_exists($path)) {
    throw new Exception('Path to JSON file does not exist.');
  }

  // create questions objects from json file and store it in a session variable
  $_SESSION['questionDetails'] = json_decode(file_get_contents($path));

  // validate whether the new session variable contains objects
  if(!is_object($_SESSION['questionDetails'][0])) {
    throw new Exception('File does not contain valid JSON objects.');
  }

  // calculate the total number of rounds
  $_SESSION['totalRounds'] = count($_SESSION['questionDetails']);

  // for the first round the session array is set will all question id's value FALSE
  // this i'll use to validate in function selectQuestion whether a question has been asked or not
  $_SESSION['questionAnswered'] = array_fill(0,$_SESSION['totalRounds'],FALSE);

  return TRUE;
}

// this function validates the answer on the previous question, updates session variables to make sure the question is not raised again
// and the score is stored and returns the correct answer
function validateAnswer($previousQuestion,$submittedAnswer) {

  // validate input variables
  if(!isset($previousQuestion) || !isset($submittedAnswer) || !is_numeric($previousQuestion) || !is_numeric($submittedAnswer)) {
    throw new Exception('No valid input for function validateAnswer');
  }

  // validate session variables
  if(!isset($_SESSION['questionAnswered']) || !isset($_SESSION['questionDetails']) ||
        !is_array($_SESSION['questionAnswered']) || !is_array($_SESSION['questionDetails'])) {
    throw new Exception('Session variables not set properly');
  }

  // set session array for the previous question answered to true, so it won't  be asked again
  $_SESSION['questionAnswered'][$previousQuestion] = TRUE;

  //validate whether the submitted answer equals to the correct answer and return the correctAnswer so it can be shown to the user
  if ($_SESSION['questionDetails'][$previousQuestion]->correctAnswer == $submittedAnswer) {
    // if the question is correct write 1 as indicator, so later in results.php array_sum can be used
    $_SESSION['answerCorrect'][$previousQuestion] = 1;
    return array(TRUE,$_SESSION['questionDetails'][$previousQuestion]->correctAnswer);
  } else {
    return array(FALSE,$_SESSION['questionDetails'][$previousQuestion]->correctAnswer);
  }

}

// this function selects one random question  which wasn't raised before. It shuffles the answers
// and returns one array with all relevant details
function selectQuestion()  {

  // validate the session variables
  if(!isset($_SESSION['questionAnswered']) || !isset($_SESSION['questionDetails']) ||
        !is_array($_SESSION['questionAnswered']) || !is_array($_SESSION['questionDetails'])) {
    throw new Exception('Session variables not set properly');
  }

  // select the keys of the questions which have not been answered yet
  // thanks to https://stackoverflow.com/questions/38195517/choose-random-index-of-array-with-condition-on-value for selecting keys from the array with only non answered questions
  $remainingQuestions = array_keys($_SESSION['questionAnswered'],FALSE);

  // create new $questionDetails array for all relevant question details, which will also contain the answers in random order, which is not possible in the object properties
  // randomly select ID of one of the remaining questions
  $questionDetails['currentQuestion'] = $remainingQuestions[array_rand($remainingQuestions)];

  // copy details from object to array
  $questionDetails['rightAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->rightAdder;
  $questionDetails['leftAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]->leftAdder;

  // the part to put the answers in random order, making use of shuffle
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
