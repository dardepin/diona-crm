<?php
function is_exists($db, $id, $name, $positions, $phone, $email)
{
    $sel = 'SELECT * FROM workers WHERE worker_id = \'' . $id . '\' AND fullname = \'' . $name . '\' AND current_positions =  \'{' . $positions . '}\' AND phone = \'' . $phone . '\' AND email = \'' . $email . '\'';

    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total != 0) return TRUE;
    } else echo 'updworker: Ошибка БД #2: ' . pg_last_error($db);
    return FALSE;
}

function updworker($db, $id, $name, $positions, $phone, $email)
{
    if(is_exists($db, $id, $name, $positions, $phone, $email)) return;//ok
    $upd = 'UPDATE workers SET fullname = \'' . $name . '\', current_positions = \'{' . $positions . '}\', phone = \'' . $phone . '\', email = \'' . $email .  '\' WHERE worker_id = \'' . $id . '\'';

    $res = pg_query($db, $upd);
    if(!$res) echo 'updworker: Ошибка БД #1: ' . pg_last_error($db);
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

if(isset($_POST['n']) && isset($_POST['p'])  && isset($_POST['i']))
{
    $id = $_POST['i'];
    $name = $_POST['n'];
    $positions = $_POST['p'];
    if(isset($_POST['t'])) $phone = $_POST['t'];
    if(isset($_POST['e'])) $email = $_POST['e'];

    updworker($connection1, $id, $name, $positions, $phone, $email);
} else echo 'updworker: Нет id, имени или должности';

?>