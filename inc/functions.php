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

  $currentQuestion = array_rand($questions);

  $question = Array();

  $question["rightAdder"] = $questions[$currentQuestion]->rightAdder;
  $question["leftAdder"] = $questions[$currentQuestion]->leftAdder;

  $answers = array();
  $answers = [
              $questions[$currentQuestion]->correctAnswer,
              $questions[$currentQuestion]->firstIncorrectAnswer,
              $questions[$currentQuestion]->secondIncorrectAnswer
            ];
  shuffle($answers);

  $question["firstAnswer"] = $answers[0];
  $question["secondAnswer"] = $answers[1];
  $question["thirdAnswer"] = $answers[2];

  return array ($question,$currentQuestion,$totalRounds);
}

?>
