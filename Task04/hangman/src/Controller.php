<?php

namespace trapezaiv\hangman\Controller;

use function trapezaiv\hangman\Model\addAttemptsInfo;
use function trapezaiv\hangman\View\showGame;
use function trapezaiv\hangman\Model\openDB;
use function trapezaiv\hangman\Model\createRecord;
use function trapezaiv\hangman\Model\gameList;
use function trapezaiv\hangman\Model\gameReplay;
use function trapezaiv\hangman\Model\updateDB;

function menu($key)
{
    if ($key == "--new" || $key == "-n") {
        startGame();
    } elseif ($key == "--list" || $key == "-l") {
        gameList();
    } elseif ($key == "--help" || $key == "-h") {
        \cli\line("
        --new or -n: start new game\n
        --list or -l: list of all games\n
        --help or -h: command list\n
        --replay [id] or -r [id]: replay of the game");
    } else {
        \cli\line("Wrong key");
    }
}

function menuReplay($key1, $key2)
{
    if ($key1 == "--replay" || $key1 == "-r") {
        if (is_numeric($key2)) {
            gameReplay($key2);
        } else {
            \cli\line("Wrong key");
        }
    } else {
        \cli\line("Wrong key");
    }
}

function showResult($word, $result)
{
    \cli\line("$result");

    \cli\line("The hidden word was: $word");
}

function startGame()
{
    $root = openDB();
    date_default_timezone_set("Europe/Moscow");
    $gameDate = date("d") . "." . date("m") . "." . date("Y");
    $gameTime = date("H") . ":" . date("i") . ":" . date("s");
    $nickname = getenv("username");

    $wordBase = array("hidden", "answer", "laptop", "unreal", "script");
    $randomChoice = random_int(0, count($wordBase) - 1);
    $word = $wordBase[$randomChoice];
    $lengthWord = strlen($word);
    $remaining = $word;

    $id = createRecord($gameDate, $gameTime, $nickname, $word);

    $entryField = "";
    for ($i = 0; $i < $lengthWord; $i++) {
        $entryField .= ".";
    }

    $fails = 0;
    $rightAnswers = 0;
    $progress = 0;

    while ($fails != 6 && $rightAnswers != $lengthWord) {
        showGame($fails, $entryField);
        $letter = mb_strtolower(\cli\prompt("Letter: "));
        $attempt = 0;

        for ($i = 0; $i < strlen($remaining); $i++) {
            if ($remaining[$i] == $letter) {
                $entryField[$i] = $letter;
                $remaining[$i] = " ";
                $rightAnswers++;
                $attempt++;
            }
        }

        if ($attempt == 0) {
            $fails++;
            $result = 0;
        } else {
            $result = 1;
        }
        $progress++;

        addAttemptsInfo($id, $progress, $letter, $result);
    }

    $result = "";

    if ($rightAnswers == $lengthWord) {
        $result = "Win";
    } else {
        $result = "Lose";
    }

    showGame($fails, $entryField);
    showResult($word, $result);
    updateDB($id, $result);
}
