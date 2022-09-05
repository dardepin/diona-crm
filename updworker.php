<?php
function worker_exists($db, $id, $name, $position, $phone, $email)
{
    //вернет true, если уже есть такой работник
    $sel = 'SELECT * FROM workers WHERE worker_id=\'' . $id . '\' AND fullname = \'' . $name . '\' AND current_position =  \'' . $position . '\' AND phone=\'' . $phone . '\' AND email=\'' . $email . '\'';
    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 0) return TRUE;
    } else echo "updworker: Ошибка БД #2: " . pg_last_error($db);
    return FALSE;
}

function updworker($db, $id, $name, $position, $phone, $email)
{
    if(!worker_exists($db, $id, $name, $position, $phone, $email)) return;//ok
    $upd = 'UPDATE workers SET fullname=\'' . $name . '\', current_position=\'' . $position .  '\', phone=\'' . $phone . '\', email=\'' . $email . '\' WHERE worker_id=\'' . $id . '\'';

    $res = pg_query($db, $upd);
    if(!$res) echo "updworker: Ошибка БД #1: " . pg_last_error($db);
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

$phone = ''; $email = '';

if(isset($_POST['name']) && isset($_POST['position'])  && isset($_POST['id']))
{
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    if(isset($_POST['phone'])) $phone = $_POST['phone'];
    if(isset($_POST['email'])) $email = $_POST['email'];

    updworker($connection1, $id, $name, $position, $phone, $email);
} else echo 'updworker: Нет id, имени или должности';

?>