<?php

function worker_exists($db, $id)
{
    if($id == 0) { echo 'newissue: id равен 0'; return FALSE; }

    $sel = 'SELECT * FROM workers WHERE worker_id = \'' . $id . '\'';
    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) return FALSE;
    }
    else { echo 'newissue: Ошибка БД #2: ' . pg_last_error($db); return FALSE; }

    return TRUE;
}

function newissue($db, $id, $position, $date, $place, $issue, $notes, $urgent, $status)
{
    if(!worker_exists($db, $id)) return;

    $ins = 'INSERT INTO issues (worker_id, status, position, creat_time, mod_time, issue_date, place, issue, notes, urgent) VALUES (\'' . $id . '\', \'' . $status . '\',  \''. $position . '\', current_timestamp, current_timestamp, \'' . $date . '\', \'' . $place . '\', \'' . $issue . '\', \'' . $notes . '\', \'' . $urgent . '\')';

    $res = pg_query($db, $ins);
    if(!$res) echo 'newissue: Ошибка БД #1: ' . pg_last_error($db);
    return;
}

require_once('connect.php');
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

if(isset($_POST['id']) && isset($_POST['t'])&& isset($_POST['p']) && isset($_POST['i']) && isset($_POST['o']))
{
    $urgent = isset($_POST['u']) && is_numeric($_POST['u']) ? $_POST['u']:0;
    if(isset($_POST['n'])) $notes = $_POST['n']; else $notes = '';
    if(isset($_POST['s'])) $status = $_POST['s']; else $status = 'Назначено';

    $id = $_POST['id'];
    $pos = $_POST['o'];
    $date = $_POST['t'];
    $place = $_POST['p'];
    $issue = $_POST['i'];

    newissue($connection1, $id, $pos, $date, $place, $issue, $notes, $urgent, $status);
} else echo 'newissue: Нет обязательных параметров в запросе.';
?>