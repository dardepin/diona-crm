<?php

function worker_exists($db, $id)
{
    //вернет true, если есть такой работник
    if($id == 0) { echo 'setcall: id равен 0'; return FALSE; }

    $sel = 'SELECT * FROM workers WHERE worker_id = \'' . $id . '\'';
    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) return FALSE;
    } else echo "setcall: Ошибка БД #2: " . pg_last_error($db);
    return TRUE;
}

function addissue($db, $id, $date, $place, $issue, $notes, $urgent, $status)
{
    if(!worker_exists($db, $id)) return;

    $ins = 'INSERT INTO issues (worker_id, status, creat_time, mod_time, issue_date, place, issue, notes, urgent) VALUES (\'' . $id . '\', \'' . $status . '\', current_timestamp, current_timestamp, \'' . $date . '\', \'' . $place . '\', \'' . $issue . '\', \'' . $notes . '\', \'' . $urgent . '\')';

    $res = pg_query($db, $ins);
    if($res) echo ' Новая задача сохранена.';
    else echo "setcall: Ошибка БД #1: " . pg_last_error($db);
    return;
}

require_once("connect.php");
session_start();

if(isset($_SESSION['username']) && (time() - $_SESSION['timeout'] < 900))
{
    $username = $_SESSION['username'];
    $_SESSION['timeout'] = time();
}
else
{
    session_destroy();
    header('Location: login.php?r=workers');

    exit();
}

$urgent = 0;
$notes = '';
$status = '';

if(isset($_POST['id']) && isset($_POST['t'])&& isset($_POST['p']) && isset($_POST['i']))
{
    $urgent = isset($_POST['u']) && is_numeric($_POST['u']) ? $_POST['u']:0;
    if(isset($_POST['n'])) $notes = $_POST['n']; else $notes = '';
    if(isset($_POST['s'])) $status = $_POST['s']; else $status = 'Назначено';

    $id = $_POST['id'];
    $date = $_POST['t'];
    $place = $_POST['p'];
    $issue = $_POST['i'];

    addissue($connection1, $id, $date, $place, $issue, $notes, $urgent, $status);
} else echo 'setcall: Нет обязательных параметров в запросе.';
?>