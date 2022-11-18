<?php

namespace trapezaiv\hangman\View;

function showGame($fails, $entryField)
{
    $pseudoGraphics = array (
        " +---+\n     |\n     |\n     |\n    ===\n ",
        " +---+\n 0   |\n     |\n     |\n    ===\n ",
        " +---+\n 0   |\n |   |\n     |\n    ===\n ",
        " +---+\n 0   |\n/|   |\n     |\n    ===\n ",
        " +---+\n 0   |\n/|\  |\n     |\n    ===\n ",
        " +---+\n 0   |\n/|\  |\n/    |\n    ===\n ",
        " +---+\n 0   |\n/|\  |\n/ \  |\n    ===\n "
    );

    \cli\line($pseudoGraphics[$fails]);

    for ($i = 0; $i < strlen($entryField); $i++) {
        echo $entryField[$i];
    }
    \cli\line("");
    \cli\line("");
}
