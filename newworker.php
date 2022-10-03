<?php

function is_exists($db, $name, $positions)
{
    $allpositions = explode(',', $positions);
    foreach($allpositions as $position)
    {
        $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE deleted = FALSE AND fullname ILIKE \'%' . $name . '%\' AND array[\'' . $position . '\']::positions[] <@ "current_positions"';

        $res = pg_query($db, $sel);
        if($res)
        {
            $records = pg_fetch_assoc($res);
            if($records['total'] == 0) continue;
            else { echo 'Сотрудник уже существует'; return FALSE; }
        }
        else { echo 'newworker: Ошибка БД #1: ' . pg_last_error($db); return FALSE; }
    }
    return TRUE;
}

function newworker($db, $name, $positions, $phone, $email)
{
    if(!is_exists($db, $name, $positions)) return;

    $ins = 'INSERT INTO workers(fullname, current_positions, phone, email) VALUES (\'' . $name . '\', \'{' . $positions . '}\', \'' . $phone . '\', \'' . $email . '\')';
    $res = pg_query($db, $ins);
    if($res) echo 'Новый работник сохранен';
    else echo 'newworker: Ошибка БД #1: ' . pg_last_error($db);
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

$phone = ''; $email = '';
if(isset($_POST['n']) && isset($_POST['p']))
{
    $name = $_POST['n'];
    $positions = $_POST['p'];
    if(isset($_POST['t'])) $phone = $_POST['t'];
    if(isset($_POST['e'])) $email = $_POST['e'];

    newworker($connection1, $name, $positions, $phone, $email);
} else echo 'newworker: Нет имени или должности';
?>