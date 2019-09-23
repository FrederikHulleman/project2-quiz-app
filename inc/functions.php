<?php

function selectQuestion()  {
  // created this function, so I'm flexible when I want to change the source of the $questions
  // so i won't have to change index.php

  $path = 'inc/questions.json';

  if (!file_exists($path)) {
    return FALSE;
  }

  $questions = json_decode(file_get_contents($path));

  if(!is_object($questions[0])) {
    return FALSE;
  }

  $totalRounds = count($questions);

  //-------------------------------

  $remaining = array_keys($_SESSION['questionAnswered'],FALSE);

  echo "<br> REMAINING";
  var_dump($remaining);
  echo "<br><BR>";

  //---------------------------------



  $currentQuestion = array_rand($questions);

  $questionDetails = Array();

  $questionDetails["rightAdder"] = $questions[$currentQuestion]->rightAdder;
  $questionDetails["leftAdder"] = $questions[$currentQuestion]->leftAdder;

  $answers = array();
  $answers = [
              $questions[$currentQuestion]->correctAnswer,
              $questions[$currentQuestion]->firstIncorrectAnswer,
              $questions[$currentQuestion]->secondIncorrectAnswer
            ];
  shuffle($answers);

  $questionDetails["firstAnswer"] = $answers[0];
  $questionDetails["secondAnswer"] = $answers[1];
  $questionDetails["thirdAnswer"] = $answers[2];

  return array ($questionDetails,$currentQuestion,$totalRounds);
}

?>
