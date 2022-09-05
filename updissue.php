<?php

function issue_exists($db, $id, $status, $date, $place, $issue, $notes, $urgent)
{
    $sel = 'SELECT * FROM issues WHERE issue_id=\'' . $id . '\' AND issue_date= \'' . $date . '\' AND place=\'' . $place . '\' AND issue=\'' . $issue . '\' AND notes=\'' . $notes . '\' AND urgent=\'' . $urgent . '\' AND status=\'' . $status .'\'';

    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) return TRUE;
    } else echo "updissue: Ошибка БД #2: " . pg_last_error($db);
    return FALSE;
}

function updissue($db, $id, $status, $date, $place, $issue, $notes, $urgent)
{
    if(!issue_exists($db, $id, $status, $date, $place, $issue, $notes, $urgent)) return;
    $upd = 'UPDATE issues SET status=\'' . $status . '\', issue_date=\'' . $date . '\', place=\'' . $place . '\', issue=\'' . $issue . '\', notes=\'' . $notes . '\', urgent=\'' . $urgent . '\' WHERE issue_id= \'' . $id . '\'';

    $res = pg_query($db, $upd);
    if(!$res) echo "updissue: Ошибка БД #1: " . pg_last_error($db);
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
    header('Location: login.php');
    exit();
}

$urgent = 0;
$notes = '';
if(isset($_POST['id']) && isset($_POST['t'])&& isset($_POST['p']) && isset($_POST['i']) && isset($_POST['s']))
{
    $urgent = isset($_POST['u']) && is_numeric($_POST['u']) ? $_POST['u']:0;
    if(isset($_POST['n'])) $notes = $_POST['n']; else $notes = '';

    $id = $_POST['id'];
    $date = $_POST['t'];
    $place = $_POST['p'];
    $issue = $_POST['i'];
    $status = $_POST['s'];

    updissue($connection1, $id, $status, $date, $place, $issue, $notes, $urgent);
} else echo 'updissue: Нет обязательных параметров в запросе.';

?>