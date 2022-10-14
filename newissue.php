<?php

function worker_exists($db, $id, $position)
{
    if($id == 0) { echo 'newissue: id равен 0'; return FALSE; }

    $sel = 'SELECT * FROM workers WHERE worker_id = \'' . $id . '\' AND array[\'' . $position . '\']::positions[] <@ "current_positions" AND fired = FALSE AND deleted = FALSE';
    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) { echo 'newissue: работник не существует, либо удален или уволен'; return FALSE; }
    }
    else { echo 'newissue: Ошибка БД #2: ' . pg_last_error($db); return FALSE; }
    return TRUE;
}

function newissue($db, $id, $position, $date, $place, $issue, $notes, $urgent, $status)
{
    if(!worker_exists($db, $id, $position)) return;

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

if(isset($_POST['w']) && isset($_POST['t'])&& isset($_POST['p']) && isset($_POST['x']) && isset($_POST['o']))
{
    $urgent = (isset($_POST['u']) && is_numeric($_POST['u'])) ? $_POST['u']:0;
    $notes = (isset($_POST['n'])) ? $_POST['n'] : '';
    $status = (isset($_POST['s'])) ? $_POST['s'] : 'Назначено';

    $id = $_POST['w'];
    $pos = $_POST['o'];
    $date = $_POST['t'];
    $place = $_POST['p'];
    $issue = $_POST['x'];

    newissue($connection1, $id, $pos, $date, $place, $issue, $notes, $urgent, $status);
} else echo 'newissue: Нет обязательных параметров в запросе.';
?>