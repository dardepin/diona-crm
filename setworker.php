<?php
function worker_exists($db, $name, $position)
{
    //вернет true, если уже есть такой работник
    $sel = 'SELECT * FROM workers WHERE fullname = \'' . $name . '\' AND current_position =  \'' . $position . '\'';
    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) return TRUE;
        else echo "Уже есть работник с именем '" . $name . "' и должностью '" . $position . "'";
    } else echo "setworker: Ошибка БД #2: " . pg_last_error($db);
    return FALSE;
}

function addworker($db, $name, $position, $phone, $email)
{
    if(!worker_exists($db, $name, $position)) return;

    $ins = 'INSERT INTO workers(fullname, current_position, phone, email) VALUES (\'' . $name . '\', \'' . $position . '\', \'' . $phone . '\', \'' . $email . '\')';

    $res = pg_query($db, $ins);
    if($res) echo 'Новый работник сохранен';
    else echo "setworker: Ошибка БД #1: " . pg_last_error($db);
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

$phone = ''; $email = '';

if(isset($_POST['name']) && isset($_POST['position']))
{
    $name = $_POST['name'];
    $position = $_POST['position'];
    if(isset($_POST['phone'])) $phone = $_POST['phone'];
    if(isset($_POST['email'])) $email = $_POST['email'];

    addworker($connection1, $name, $position, $phone, $email);
} else echo 'setworker: Нет имени или должности';
?>