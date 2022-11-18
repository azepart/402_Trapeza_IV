import {
    showGame,
    showResult,
    drawGame,
    showAllGames,
    replayGame
} from "./View.js";

import {
    createDB,
    startDB,
    addStepDB,
    updateStatusDB,
    listGamesDB,
    replayGameDB
} from "./Model.js";

document.getElementById("start").addEventListener("click", setName);
document.getElementById("btn-enter-letter").addEventListener("click", gameProcess);
document.getElementById("restart-game").addEventListener("click", restartGame);
document.getElementById("show-games").addEventListener("click", showAllGame);
document.getElementById("start-replay").addEventListener("click", startReplay);

let user;
let fails;
let attempt;
let rightAnswers;
let progress;
let result;
let word;
let lengthWord;
let resultGame;
let remaining;
var entryField;
let wordBase = ["absurd", "hidden", "answer", "laptop", "unreal", "engine", "script"];

String.prototype.replaceAt = function(index, replacement) {
    return this.substr(0, index) + replacement + this.substr(index + replacement.length);
}

function setName() {
    if (document.getElementById("name").value == "") {
        alert("Введите имя игрока");
    } else {
        user = document.getElementById("name").value;
        showGame();
        startGame();
    }
}

function startGame() {
    fails = 0;
    attempt = 0;
    rightAnswers = 0;
    progress = 0;
    result;

    word = wordBase[Math.floor(Math.random() * wordBase.length)].toUpperCase();
    console.log(word);
    lengthWord = word.length;
    remaining = word;
    entryField = "";
    for (let i = 0; i < lengthWord; i++) {
        entryField += ".";
    }
    startDB(user, word, "Не закончена");
    drawGame(fails, entryField);
}


function gameProcess() {
    let letter = document.getElementById("enter-letter").value.toUpperCase();
    if (letter == "" || letter.length > 1) {
        alert("Введите 1 букву");
    } else {
        document.getElementById("enter-letter").value = "";
        if (fails != 6 && rightAnswers != lengthWord) {
            let tempCount = 0;
            attempt++;
            for (let i = 0; i < remaining.length; i++) {
                if (remaining[i] == letter) {
                    entryField = entryField.replaceAt(i, letter);
                    remaining = remaining.replaceAt(i, " ");
                    rightAnswers++;
                    tempCount++;
                }
            }
            if (tempCount == 0) {
                fails++;
                addStepDB(attempt, letter, "не угадано");
            } else {
                addStepDB(attempt, letter, "угадано");
            }
            progress++;            
            drawGame(fails, entryField);
            if (fails == 6 || rightAnswers == lengthWord) {
                if (rightAnswers == lengthWord) {
                    updateStatusDB("Победа")
                    resultGame = "Победа";
                } else {
                    updateStatusDB("Проигрыш");
                    resultGame = "Проигрыш";
                }
                drawGame(fails, entryField);
                showResult(word, resultGame, user);
            }
        }
    }
}

function restartGame() {
    showGame();
    startGame();
}

function showAllGame() {
    showAllGames();
}

function startReplay() {
    let id_game = document.getElementById("id-select-game").value;
    replayGame(id_game);
}