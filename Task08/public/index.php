<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// $app->get('/', function ($request, $response) {
//     return $response->withRedirect('./index.html', 301);
// });

$app->get('/', 'index.html');

$app->get('/games', function ($request, $response) {
    $gamesInfo = json_encode(listGames()); 
    $response->getBody()->write($gamesInfo);
    return $response;
});

$app->get('/games/{id}', function ($request, $response, array $args) {
    $Gameid = $args['id'];
    $responseBody = json_encode(turnsById($Gameid));
    $response->getBody()->write($responseBody);
    return $response;
});

$app->post('/games', function ($request, $response) {
    $string = json_decode($request->getBody());
    $info = explode("|", $string);
    $gamesInfo = explode("+", $info[1]); // 0 - name, 1 - size, 2 - date, 3 - time, 4 - human_mark, 5 - winner
    $turns = explode("+", $info[0]); 
    insertInfo($gamesInfo, $turns);
    $response->write('Бд заполнена');
    return $response;
});

$app->run();

function openDatabase()
{
    if (!file_exists("./../db/gamedb.db")) {
        $db = new \SQLite3('./../db/gamedb.db');

        $gamesInfoTable = "CREATE TABLE playerInfo(
            id INTEGER PRIMARY KEY,
            date DATE,
            nickname TEXT,
            word TEXT,
            result TEXT)";
        $db->exec($gamesInfoTable);


        $stepsInfoTable = "CREATE TABLE attemptsInfo(
            id INTEGER KEY,
            attempt INTEGER,
            letter TEXT,
            result TEXT)";
        $db->exec($stepsInfoTable);
    } else {
        $db = new \SQLite3('./../db/gamedb.db');
    }
    return $db;
}

function getGameId($db)
{
    $query = "SELECT id 
    FROM playerInfo 
    ORDER BY id DESC LIMIT 1";
    $result = $db->querySingle($query);
    if (is_null($result))
        return 1;
    return $result + 1;
}

function insertInfo($gamesInfo, $turns)
{
    $db = openDatabase();
    $id = getGameId($db);
    $data = $gamesInfo[0];
    $nickname = $gamesInfo[1];
    $word = $gamesInfo[2];
    $result = $gamesInfo[3];
    $attempt = $gamesInfo[4];
    $letter = $gamesInfo[5];
    $letterResult = $gamesInfo[6];
    $db->exec("INSERT INTO playerInfo (
        id,
        date,
        nickname,
        word,
        result
        ) VALUES (
        '$id', 
        '$data', 
        '$nickname', 
        '$word',  
        '$result')");
    for($i = 0; $i < count($attempt); $i++) {
        $db->exec("INSERT INTO attemptsInfo (
            id, 
            attempt, 
            letter,
            result
            ) VALUES (
            '$id', 
            '$attempt[$i]', 
            '$letter[$i]',
            '$letterResult[$i]')");
    }
}

function listGames()
{
    $db = openDatabase();
    $result = $db->query("SELECT * FROM playerInfo");
    $gamesInfo = "";
    while ($row = $result->fetchArray()) {
        for ($i = 0; $i < 5; $i++) {
            $gamesInfo .= $row[$i] . "|";
        }
        $gamesInfo .= ";";
    }
    return $gamesInfo;
}


function gameById($id)
{
    $db = openDatabase();
    $result = $db->query("SELECT * FROM playerInfo WHERE id = '$id'");
    $gamesInfo = "";
    while ($row = $result->fetchArray()) {
        for ($i = 0; $i < 5; $i++) {
            $gamesInfo .= $row[$i] . "|";
        }
        $gamesInfo .= ";";
    }
}

function turnsById($id)
{
    $db = openDatabase();
    $result = $db->query("SELECT * FROM attemptsInfo WHERE id = '$id'");
    $turnsInfo = "";
    while ($row = $result->fetchArray()) {
        for ($i = 0; $i < 4; $i++) {
            $turnsInfo .= $row[$i] . "|";
        }
        $turnsInfo .= ";";
    }
    return $turnsInfo;
}
