import {
    showGame,
    showResult,
    drawGame
} from "./View.js";

document.getElementById("start").addEventListener("click", setName);
document.getElementById("btn-enter-letter").addEventListener("click", gameProcess);
document.getElementById("restart-game").addEventListener("click", restartGame);

let user;
let fails;
let rightAnswers;
let progress;
let result;
let word;
let lengthWord;
let resultGame;
let remaining;
var entryField;
let wordBase = [ "absurd", "hidden", "answer", "laptop", "unreal", "engine", "script"];

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
    drawGame(fails, entryField);
}


function gameProcess() {
    let letter = document.getElementById("enter-letter").value.toUpperCase();
    if (letter == "" || letter.length > 1) {
        alert("Введите 1 букву");
    } 
    else if (letter.match(/[A-Z]/gi).length==0){
        alert("Введите букву английского алфавита");
    } 
    else {
        document.getElementById("enter-letter").value = "";
        if (fails != 6 && rightAnswers != lengthWord) {
            let attempt = 0;
            for (let i = 0; i < remaining.length; i++) {
                if (remaining[i] == letter) {
                    entryField = entryField.replaceAt(i, letter);
                    remaining = remaining.replaceAt(i, " ");
                    rightAnswers++;
                    attempt++;
                }
            }
            if (attempt == 0) {
                fails++;
                result = 0;
            } else {
                result = 1;
            }
            progress++;            
            drawGame(fails, entryField);
            if (fails == 6 || rightAnswers == lengthWord) {
                if (rightAnswers == lengthWord) {
                    resultGame = "Win";
                } else {
                    resultGame = "Lose";
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