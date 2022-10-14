<?php

function worker_exists($db, $id, $name, $positions) //проверить, есть ли работник с другим id, но с такими же должностями.
{
    $allpositions = explode(',', $positions);
    foreach($allpositions as $position)
    {
        $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE worker_id != \'' . $id . '\' AND fullname = \'' . $name . '\' AND deleted = FALSE AND array[\'' . $position . '\']::positions[] <@ "current_positions"';

        $res = pg_query($db, $sel);
        if($res)
        {
            $records = pg_fetch_assoc($res);
            if($records['total'] == 0) continue;
            else { echo 'nupdworker: сотрудник уже существует'; return FALSE; }
        }
        else { echo 'updworker: Ошибка БД #1: ' . pg_last_error($db); return FALSE; }
    }
    return TRUE;
}

function tasks_exists($db, $id) //проверяет, есть ли задачи до удаления работника. если есть, то можно только уволить
{
    $sel = 'SELECT COUNT(worker_id) AS total FROM issues WHERE worker_id = \'' . $id . '\'';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        if($records['total'] > 0) echo 'updworker: Невозможно удалить работника, у него есть задачи';
        else return 0;
    }
    else echo 'updworker: Ошибка БД #2: ' . pg_last_error($db);
    return 1;
}

function updworker($db, $id, $name, $positions, $phone, $email, $fired, $deleted)
{

    if(($deleted == TRUE) && (tasks_exists($db, $id))) return;
    if(!worker_exists($db, $id, $name, $positions)) return;

    $upd = 'UPDATE workers SET fullname = \'' . $name . '\', current_positions = \'{' . $positions . '}\', phone = \'' . $phone . '\', email = \'' . $email .  '\', fired = \'' . $fired . '\', deleted = \'' . $deleted . '\' WHERE worker_id = \'' . $id . '\'';

    $res = pg_query($db, $upd);
    if(!$res) echo 'updissue: Ошибка БД #3: ' . pg_last_error($db);
    return;

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


if(isset($_POST['n']) && isset($_POST['o'])  && isset($_POST['i']))
{
    $id = $_POST['i'];
    $name = $_POST['n'];
    $positions = $_POST['o'];
    $phone = (isset($_POST['t'])) ? $_POST['t'] : '';
    $email = (isset($_POST['e'])) ? $_POST['e'] : '';
    /*if(isset($_POST['f'])) $fired = $_POST['f'];
    if(isset($_POST['d'])) $deleted = $_POST['d'];*/
    $fired = (isset($_POST['f']) && is_numeric($_POST['f'])) ? $_POST['f']:0;
    $deleted = (isset($_POST['d']) && is_numeric($_POST['d'])) ? $_POST['d']:0;

    updworker($connection1, $id, $name, $positions, $phone, $email, $fired, $deleted);
} else echo 'updworker: Нет id, имени или должности';

?>