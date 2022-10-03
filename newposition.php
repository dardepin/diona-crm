<?php

function is_exists($db, $position)
{
    $sel = 'SELECT COUNT(*) AS total FROM pg_enum WHERE enumlabel = \'' . $position . '\' AND enumtypid = (SELECT oid FROM pg_type WHERE typname = \'positions\')';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        if($records['total'] == 0) return TRUE;
        else echo 'Такая должность уже существует';
    }
    else echo 'newposition: Ошибка БД #1: ' . pg_last_error($db);
    return FALSE;
}

function addposition($db, $position)
{
    if(!is_exists($db, $position)) return;

    $alt = 'ALTER TYPE positions ADD VALUE \'' . $position . '\'';
    $res = pg_query($db, $alt);
    if(!$res) echo 'newposition: Ошибка БД #2: ' . pg_last_error($db);
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
    header('Location: login.php?r=positions');

    exit();
}

if(isset($_POST['p']))
{
    $position = $_POST['p'];
    addposition($connection1, $position);
} else echo 'newposition: Нет должности';
?>