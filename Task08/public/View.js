import {
    listGamesDB,
    replayGameDB
} from "./Model.js";

let canvas = document.getElementById("canvas");
let can = canvas.getContext("2d");
can.font = "26pt Times New Roman";

export function showGame() {
    document.getElementById("login").style.display = "none";
    document.getElementById("game-menu").style.display = "block";
    canvas.hidden = false;
}

export function drawGame(fails, entryField) {
    can.clearRect(0, 0, canvas.width, canvas.height);

    can.beginPath();
    can.moveTo(50,100);
    can.lineTo(50,50);
    can.lineTo(200,50);
    can.lineTo(200,450);
    can.lineTo(50,450);
    can.stroke();

    can.fillText(entryField, 250, 100);
      
    if (fails > 0){
        can.beginPath();
        can.arc(50, 122, 20, 0, Math.PI * 2, false);
        can.stroke();
    }
    
    if (fails > 1){
        can.beginPath();
        can.moveTo(50,140);
        can.lineTo(50,250);
        can.stroke();
    }
    
    if(fails > 2){
        can.beginPath();
        can.moveTo(50,160);
        can.lineTo(5,200);
        can.stroke();
    }

    if(fails > 3){
        can.beginPath();
        can.moveTo(50,160);
        can.lineTo(95,200);
        can.stroke();
    }

    if(fails > 4){
        can.beginPath();
        can.moveTo(50,250);
        can.lineTo(25,310);
        can.stroke();
    }

    if(fails > 5){
        can.beginPath();
        can.moveTo(50,250);
        can.lineTo(75,310);
        can.stroke();
    }
}

export function showResult(word, resultGame, user) {
    can.fillText("Слово: " + word, 250, 150);
    can.fillText("Результат: " + resultGame, 250, 200);
    can.fillText("Игрок: " + user, 250, 250);
}


export async function showAllGames() {
    let html = await listGamesDB();
    table.innerHTML = html;
    textGamesList.innerHTML = "Список игр";
}

export async function replayGame(id_game) {
    let html = await replayGameDB(id_game);
    table.innerHTML = html;
    textGamesList.innerHTML = "Повтор игры";
}