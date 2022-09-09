@ECHO OFF
chcp 65001>nul

WHERE sqlite3>nul 2>nul
IF %ERRORLEVEL% NEQ 0 ( ECHO SQL3 command didn't found & PAUSE & EXIT )

ECHO.
ECHO CREATE TABLE IF NOT EXISTS logger(User varchar(10), Date text default current_timestamp); | sqlite3 logger.db
ECHO INSERT INTO logger VALUES('%USERNAME%', DATETIME('now', 'localtime')); | sqlite3 logger.db

ECHO Имя программы: %~nx0
ECHO|<nul SET /p="Количество запусков: "
ECHO SELECT COUNT(*) FROM logger; | sqlite3 logger.db
ECHO|<nul SET /p="Первый запуск: "
ECHO SELECT Date FROM logger ORDER BY Date ASC LIMIT 1; | sqlite3 logger.db

ECHO.
ECHO SELECT * FROM logger; | sqlite3 -table logger.db
ECHO.

PAUSE
