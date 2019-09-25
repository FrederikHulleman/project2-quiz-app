<?php

// generate questions & answers and store them in a session variable
function retrieveQuestions() {

  //set number of desired rounds
  $totalRounds = 10;

  // Generate random questions
  // Loop for required number of questions
  // Add question and answer to questions array
  for ($count=0; $count < $totalRounds; $count++) {

    // Get random numbers to add
    // all left adders are spread over different decimal blocks, calculated based on $count
    $questionDetails[$count]['leftAdder'] = rand((10*$count)+1,($count+1)*10);

    // to reduce complexity, the right adder is <=50, when the left adder is >50
    if($questionDetails[$count]['leftAdder'] < 50) {
      $questionDetails[$count]['rightAdder'] = rand(1,99);
    } else {
      $questionDetails[$count]['rightAdder'] = rand(1,50);
    }

    // Calculate correct answer
    $questionDetails[$count]['correctAnswer'] =  $questionDetails[$count]['leftAdder'] + $questionDetails[$count]['rightAdder'];

    // Get incorrect answers within 10 numbers either way of correct answer
    // Make sure it is a unique answer;
    // this is implemented by having fixed ranges around the correct answer
    // first incorrect is always lower. second incorrect always higher
    // since answers are shuffled, this approach is fine
    $questionDetails[$count]['firstIncorrectAnswer'] = rand($questionDetails[$count]['correctAnswer']-10,$questionDetails[$count]['correctAnswer']-1);
    $questionDetails[$count]['secondIncorrectAnswer'] = rand($questionDetails[$count]['correctAnswer']+1,$questionDetails[$count]['correctAnswer']+10);

  }

  // store questions & answers in a session variable
  $_SESSION['questionDetails'] = $questionDetails;

  // store the total number of rounds in the session variable
  $_SESSION['totalRounds'] = $totalRounds;

  // for the first round the session array is set will all question id's value FALSE
  // this i'll use to validate in function selectQuestion whether a question has been asked or not
  $_SESSION['questionAnswered'] = array_fill(0,$totalRounds,FALSE);

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
  if ($_SESSION['questionDetails'][$previousQuestion]['correctAnswer'] == $submittedAnswer) {
    // if the question is correct write 1 as indicator, so later in results.php array_sum can be used
    $_SESSION['answerCorrect'][$previousQuestion] = 1;
    return array(TRUE,$_SESSION['questionDetails'][$previousQuestion]['correctAnswer']);
  } else {
    // if the question is inccorrect write 0 as indicator, so later in results.php array_sum can be used
    // this is only necessary to make sure the array_sum is working properly, in case all answers are incorrect
    $_SESSION['answerCorrect'][$previousQuestion] = 0;
    return array(FALSE,$_SESSION['questionDetails'][$previousQuestion]['correctAnswer']);
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

  // copy details from session to array
  $questionDetails['rightAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]['rightAdder'];
  $questionDetails['leftAdder'] = $_SESSION['questionDetails'][$questionDetails['currentQuestion']]['leftAdder'];

  // the part to put the answers in random order, making use of shuffle
  $answers = [
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]['correctAnswer'],
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]['firstIncorrectAnswer'],
              $_SESSION['questionDetails'][$questionDetails['currentQuestion']]['secondIncorrectAnswer']
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
