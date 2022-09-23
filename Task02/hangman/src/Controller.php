<?php
namespace trapezaiv\hangman\Controller;
use function trapezaiv\hangman\View\showGame;

function startGame(){
   echo "Game started" .PHP_EOL;
   showGame();
}
?>