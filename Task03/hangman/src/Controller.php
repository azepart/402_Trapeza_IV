<?php

namespace trapezaiv\hangman\Controller;

use function trapezaiv\hangman\View\showGame;
use function trapezaiv\hangman\View\showList;
use function trapezaiv\hangman\View\showReplay;
use function trapezaiv\hangman\View\help;

function key($key)
{
    if ($key == "--new" || $key == "-n") {
        startGame();
    } elseif ($key == "--list" || $key == "-l") {
        showList();
    } elseif ($key == "--replay" || $key == "-r") {
        showReplay();
    } elseif ($key == "--help" || $key == "-h") {
        help();
    } else {
        echo "Неверный ключ.";
    }
}

function startGame()
{
    $words = array("string", "letter", "artist", "arrive", "public");
    $playWord = $words[array_rand($words)];
    $remainingLetters = substr($playWord, 1, -1);
    $maxAnswers = strlen($remainingLetters);
    $maxFaults = 6;

    $progress = "______";
    $progress[0] = $playWord[0];
    $progress[-1] = $playWord[-1];

    $faultCount = 0;
    $answersCount = 0;

    do {
        showGame($faultCount, $progress);
        $letter = mb_strtolower(\cli\prompt("Буква"));
        $tempCount = 0;

        for ($i = 0; $i < strlen($remainingLetters); $i++) {
            if ($remainingLetters[$i] == $letter) {
                $progress[$i + 1] = $letter;
                $remainingLetters[$i] = " ";
                $answersCount++;
                $tempCount++;
            }
        }

        if ($tempCount == 0) {
            $faultCount++;
        }
    } while ($faultCount < $maxFaults && $answersCount < $maxAnswers);
    showGame($faultCount, $progress);
    showResult($answersCount, $playWord);
}

//результат игры
function showResult($answersCount, $playWord)
{
    if ($answersCount == 4) {
        \cli\line("Вы победили!");
    } else {
        \cli\line("\nВы проиграли!");
    }
    \cli\line("\nИгровое слово было: $playWord\n");
}
