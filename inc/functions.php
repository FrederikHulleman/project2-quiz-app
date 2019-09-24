<?php

function retrieveQuestions() {

  // created this function, so I'm flexible when I want to change the source of the $questions
  // so i won't have to change index.php

  // path to JSON file with questions
  $path = 'inc/questions.json';

  //validate whether path exists, to avoid errors in the json_decode later
  if (!file_exists($path)) {
    return FALSE;
  }

  // create questions objects from json file
  $questionsList = json_decode(file_get_contents($path));

  // validate whether questions contain objects
  if(!is_object($questionsList[0])) {
    return FALSE;
  }

  // calculate the total number of rounds
  $totalRounds = count($questionsList);

  return array($questionsList,$totalRounds);
}

function validateAnswer($questionsList,$previousQuestion,$answer) {

  if ($questionsList[$previousQuestion]->correctAnswer == $answer) {
    return TRUE;
  } else {
    return FALSE;
  }

}

function selectQuestion($questionsList)  {

  // create new array  for all relevant question details, which will also contain the answers in random order, which is not possible in the object properties
  $questionDetails = Array();

  // select the keys of the questions which have not been answered yet
  // thanks to https://stackoverflow.com/questions/38195517/choose-random-index-of-array-with-condition-on-value for selecting keys from the array with only non answered questions
  $remainingQuestions = array_keys($_SESSION['questionAnswered'],FALSE);

  // randomly select ID of one of the remaining questions
  $questionDetails['currentQuestion'] = $remainingQuestions[array_rand($remainingQuestions)];

  // copy details from object to array
  $questionDetails['rightAdder'] = $questionsList[$questionDetails['currentQuestion']]->rightAdder;
  $questionDetails['leftAdder'] = $questionsList[$questionDetails['currentQuestion']]->leftAdder;

  // the part to put the answers in random order, making use of shuffle
  $answers = array();
  $answers = [
              $questionsList[$questionDetails['currentQuestion']]->correctAnswer,
              $questionsList[$questionDetails['currentQuestion']]->firstIncorrectAnswer,
              $questionsList[$questionDetails['currentQuestion']]->secondIncorrectAnswer
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
