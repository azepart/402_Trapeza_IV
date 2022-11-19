<?php

namespace trapezaiv\hangman\Model;

use RedBeanPHP\R as R;

use function cli\line;
use function trapezaiv\hangman\View\showGame;

R::setup('sqlite:DB.db');

function createDB()
{
    $table = "CREATE TABLE playerInfo(
        id INTEGER PRIMARY KEY,
        gameDate DATE,
        gameTime TIME,
        nickname TEXT,
        word TEXT,
        result TEXT)";
    R::exec($table);

    $tableAttempts = "CREATE TABLE attemptsInfo(
        id INTEGER KEY,
        progress INTEGER,
        letter TEXT,
        result TEXT)";
    R::exec($tableAttempts);
}

function openDB()
{
    if (!file_exists('DB.db')) {
        createDB();
    }
}

function createRecord($gameDate, $gameTime, $nickname, $word)
{
    openDB();
    R::exec("INSERT INTO playerInfo(
            gameDate,
            gameTime,
            nickname,
            word,
            result
            ) VALUES (
            '$gameDate',
            '$gameTime',
            '$nickname',
            '$word',
            'Dont finished')");

    $id = R::getCell("SELECT id FROM playerInfo ORDER BY id DESC LIMIT 1");
    return $id;
}

function addAttemptsInfo($id, $progress, $letter, $result)
{
    openDB();
    R::exec("INSERT INTO attemptsInfo(
            id,
            progress,
            letter,
            result) VALUES (
            '$id',
            '$progress',
            '$letter',
            '$result')");
}

function updateDB($id, $result)
{
    openDB();
    R::exec("UPDATE playerInfo
            SET result = '$result'
            WHERE id = '$id'");
}

function gameList()
{
    openDB();
    $query = R::getAll('SELECT * FROM playerInfo');
    foreach ($query as $row) {
        line("Id: $row[id]\n
        Date: $row[gameDate]\n
        Time: $row[gameTime]\n
        Name: $row[nickname]\n
        Word: $row[word]\n
        Result: $row[result]\n");
    }
}

function gameReplay($id)
{
    openDB();

    if ($id) {
        $query = R::getAll("SELECT letter, result FROM attemptsInfo WHERE id = '$id'");
        $word = R::getCell("SELECT word FROM playerInfo WHERE id = '$id'");

        $entryField = "......";
        $remaining = $word;
        $fails = 0;

        foreach ($query as $row) {
            showGame($fails, $entryField);
            $letter = $row["letter"];
            $result = $row["result"];
            line("Letter: " . $letter);
            for ($i = 0; $i < strlen($remaining); $i++) {
                if ($remaining[$i] == $letter) {
                    $entryField[$i] = $letter;
                    $remaining[$i] = " ";
                }
            }
            if ($result == 0) {
                $fails++;
            }
        }
    }
}
