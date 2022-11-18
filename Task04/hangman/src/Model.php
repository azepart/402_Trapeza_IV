<?php

namespace trapezaiv\hangman\Model;

use SQLite3;

use function cli\line;
use function trapezaiv\hangman\View\showGame;

function createDB()
{
    $root = new SQLite3('DB.db');

    $table = "CREATE TABLE playerInfo(
        id INTEGER PRIMARY KEY,
        gameDate DATE,
        gameTime TIME,
        nickname TEXT,
        word TEXT,
        result TEXT)";
    $root->exec($table);

    $tableAttempts = "CREATE TABLE attemptsInfo(
        id INTEGER KEY,
        progress INTEGER,
        letter TEXT,
        result TEXT)";
    $root->exec($tableAttempts);
}

function openDB()
{
    if (!file_exists('DB.db')) {
        $root = createDB();
    } else {
        $root = new SQLite3('DB.db');
    }
    return $root;
}

function createRecord($gameDate, $gameTime, $nickname, $word)
{
    $root = openDB();
    $root->exec("INSERT INTO playerInfo(
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

    $id = $root->querySingle("SELECT id FROM playerInfo ORDER BY id DESC LIMIT 1");
    return $id;
}

function addAttemptsInfo($id, $progress, $letter, $result)
{
    $root = openDB();
    $root->exec("INSERT INTO attemptsInfo(
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
    $root = openDB();
    $root->exec("UPDATE playerInfo
            SET result = '$result'
            WHERE id = '$id'");
}

function gameList()
{
    $root = openDB();
    $query = $root->query('SELECT * FROM playerInfo');
    while ($row = $query->fetchArray()) {
        line("Id: $row[0]\n
        Date: $row[1]\n
        Time: $row[2]\n
        Name: $row[3]\n
        Word: $row[4]\n
        Result: $row[5]\n");
    }
}

function gameReplay($id)
{
    $root = openDB();
    //$id = $root->querySingle("SELECT EXISTS(SELECT 1 FROM playerInfo WHERE id = '$id')");

    if ($id) {
        $query = $root->query("SELECT letter, result FROM attemptsInfo WHERE id = '$id'");
        $word = $root->querySingle("SELECT word FROM playerInfo WHERE id = '$id'");

        $entryField = "......";
        $remaining = $word;
        $fails = 0;

        while ($row = $query->fetchArray()) {
            showGame($fails, $entryField);
            $letter = $row[0];
            $result = $row[1];
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
