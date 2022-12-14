let db;
let table = document.getElementById("table");

export async function createDB() {
    db = await idb.openDB("DB", 1, {upgrade(db) {
        db.createObjectStore('playerInfo', {keyPath: 'id', autoIncrement: true});
        db.createObjectStore('attemptsInfo', {keyPath: 'id', autoIncrement: true});
    }
    });
}

export async function startDB(nickname, word, result) {
    db = await idb.openDB("DB", 1, { upgrade(db) {
        db.createObjectStore('playerInfo', {keyPath: 'id', autoIncrement: true});
        db.createObjectStore('attemptsInfo', {keyPath: 'id', autoIncrement: true});
    }
    });

    await db.add('playerInfo', {
        date: new Date(),
        nickname,
        word,
        result
    });
}

export async function addStepDB(attempt, letter, result) {
    let idGame = await db.getAll('playerInfo');
    await db.add('attemptsInfo', {
        idGame: idGame.length,
        attempt,
        letter,
        result
    });
}

export async function updateStatusDB(result) {
    let idGame = await db.getAll('playerInfo');
    let cursor = await db.transaction('playerInfo', 'readwrite').store.openCursor();

    while (cursor) {
        if (cursor.value.id == idGame.length) {
            let updateData = cursor.value;
            updateData.result = result;
            cursor.update(updateData);
        }

        cursor = await cursor.continue();
    }
}

export async function listGamesDB(){
    let games = await db.getAll('playerInfo');
    let html = "<tr>";
    html += "<th>ID</th>";
    html += "<th>Дата</th>";
    html += "<th>Имя</th>";
    html += "<th>Слово</th>";
    html += "<th>Результат</th>";
    html += "</tr>";

    if(games.length > 0){
        for(let i = 0; i < games.length; i++){
            html += "<tr>";
            html += "<th>" + games[i]['id'] + "</th>";
            html += "<th>" + games[i]['date'] + "</th>";
            html += "<th>" + games[i]['nickname'] + "</th>";
            html += "<th>" + games[i]['word'] + "</th>";
            html += "<th>" + games[i]['result'] + "</th>";
            html += "</tr>";
        }

        return html;
    }
    else{
        return "<tr><th>Пока что нет записанных игр</th></tr>";
    }
}

export async function replayGameDB(id_game){
    let cursor = await db.transaction('attemptsInfo', 'readwrite').store.openCursor();

    let html = "<tr>";
    html += "<th>Попытка</th>";
    html += "<th>Буква</th>";
    html += "<th>Результат</th>";
    html += "</tr>";

    let countAttemps = 0;

    while (cursor) {
        if (cursor.value.idGame == id_game) {
            let data = cursor.value;
            html += "<tr>";
            html += "<th>" + data.attempt + "</th>";
            html += "<th>" + data.letter + "</th>";
            html += "<th>" + data.result + "</th>";
            html += "</tr>";
            countAttemps++;
        }

        cursor = await cursor.continue();
    }

    if(countAttemps == 0){
        return "Не было сделано шагов";
    }
    else{
        return html;
    }
}
