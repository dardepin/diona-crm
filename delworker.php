<?php

function check_issues($db, $id)// check if any tasks
{
    $sel = 'SELECT COUNT(issue_id) AS total FROM issues WHERE deleted = false AND worker_id = ' . $id;

    $res = pg_query($db, $sel);
    if ($res)
    {
        $records = pg_fetch_assoc($res);
        if ($records['total'] != 0) echo 'delworker: Есть активные задачи, невозможно удалить работника';
        else return 0;
    } else echo 'delworker: Ошибка БД #2: ' . pg_last_error($db);
    return 1;
}

function delworker($db, $id)// do not delete, mark as deleted
{
    if($id == 0)
    {
        echo 'delworker: worker_id не должен быть раен 0';
        return;
    }
    if(check_issues($db, $id)) return;
    $upd = 'UPDATE workers SET deleted = TRUE WHERE worker_id=' . $id;
    $res = pg_query($db, $upd);
    if(!$res) echo 'delworker: Ошибка БД #1: ' . pg_last_error($db);
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

$id = isset($_POST['i']) && is_numeric($_POST['i']) ? $_POST['i']:0;
delworker($connection1, $id)

?>