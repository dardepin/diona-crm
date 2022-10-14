<?php

function exists($db, $name, $positions)
{
    $allpositions = explode(',', $positions);
    foreach($allpositions as $position)
    {
        $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE deleted = false AND fullname ILIKE \'%' . $name . '%\' AND array[\'' . $position . '\']::positions[] <@ "current_positions"';

        $res = pg_query($db, $sel);
        if($res)
        {
            $records = pg_fetch_assoc($res);
            if($records['total'] == 0) continue;
            else { echo 'newworker: сотрудник уже существует'; return FALSE; }
        }
        else { echo 'newworker: Ошибка БД #1: ' . pg_last_error($db); return FALSE; }
    }
    return TRUE;
}

function newworker($db, $name, $positions, $phone, $email)
{
    if(!exists($db, $name, $positions)) return;

    $ins = 'INSERT INTO workers(fullname, current_positions, phone, email) VALUES (\'' . $name . '\', \'{' . $positions . '}\', \'' . $phone . '\', \'' . $email . '\')';
    $res = pg_query($db, $ins);
    if(!$res) echo 'newworker: Ошибка БД #1: ' . pg_last_error($db);
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

$t = ''; $e = '';
if(isset($_POST['n']) && isset($_POST['o']))
{
    $n = $_POST['n'];
    $o = $_POST['o'];
    if(isset($_POST['t'])) $t = $_POST['t'];
    if(isset($_POST['e'])) $e = $_POST['e'];

    newworker($connection1, $n, $o, $t, $e);
} else echo 'newworker: Нет имени или должности';
?>