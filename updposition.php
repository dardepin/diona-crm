<?php

function is_exists($db, $position)
{
    $sel = 'SELECT COUNT(*) AS total FROM pg_enum WHERE enumlabel = \'' . $position . '\' AND enumtypid = (SELECT oid FROM pg_type WHERE typname = \'positions\')';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    else echo 'updposition: Ошибка БД #1: ' . pg_last_error($db);
    return 0;
}

function updposition($db, $new_position, $old_position)
{
    if(!is_exists($db, $old_position))
    {
        echo 'updposition: Реактируемая должность не найдена.';
        return;
    }

    if(is_exists($db, $new_position))
    {
        echo 'updposition: Уже есть такая должность.';
        return;
    }
    $alt = 'ALTER TYPE positions RENAME VALUE \'' . $old_position . '\' TO \'' . $new_position . '\'';

    $res = pg_query($db, $alt);
    if(!$res) echo 'updposition: Ошибка БД #2: ' . pg_last_error($db);
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

if(isset($_POST['p']) && $_POST['o'])
{
    $new_position = $_POST['p'];
    $old_position = $_POST['o'];
    updposition($connection1, $new_position, $old_position);
} else echo 'updposition: Нет должности';
?>