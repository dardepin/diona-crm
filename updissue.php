<?php

function issue_exists($db, $id)
{
    $sel = 'SELECT * FROM issues WHERE issue_id=\'' . $id . '\' AND deleted = FALSE';

    $res = pg_query($db, $sel);
    if($res)
    {
        $total = pg_numrows($res);
        if($total == 1) return TRUE;
        else echo 'updissue: Такой задачи не существует';
    } else echo 'updissue: Ошибка БД #1: ' . pg_last_error($db);
    return FALSE;
}

function worker_exists($db, $worker, $position)
{
    $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE deleted = FALSE AND worker_id = \'' . $worker . '\' AND array[\'' . $position . '\']::positions[] <@ "current_positions"';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        if($records['total'] == 1) return TRUE;
        else echo 'updissue: сотрудник не существует или нет такой должности';
    } else echo 'updissue: Ошибка БД #2: ' . pg_last_error($db);
    return FALSE;
}

function updissue($db, $id, $worker, $status, $position, $date, $place, $issue, $notes, $urgent)
{
    if(!issue_exists($db, $id, $worker, $position)) return;
    if(!worker_exists($db, $worker, $position)) return;

    $upd = 'UPDATE issues SET worker_id = \'' . $worker . '\', position = \'' . $position . '\', status = \'' . $status . '\', issue_date = \'' . $date . '\', place=\'' . $place . '\', issue = \'' . $issue . '\', notes=\'' . $notes . '\', urgent=\'' . $urgent . '\', mod_time = current_timestamp WHERE issue_id= \'' . $id . '\'';

    $res = pg_query($db, $upd);
    if(!$res) echo 'updissue: Ошибка БД #3: ' . pg_last_error($db);
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
    header('Location: login.php');
    exit();
}

$urgent = 0;
$notes = '';
if(isset($_POST['i']) && isset($_POST['w']) && isset($_POST['d'])&& isset($_POST['p']) && isset($_POST['t']) && isset($_POST['s']) && isset($_POST['o']))
{
    $urgent = isset($_POST['u']) && is_numeric($_POST['u']) ? $_POST['u']:0;
    $notes = (isset($_POST['n'])) ? $_POST['n']: $notes = '';

    $id = $_POST['i'];
    $worker = $_POST['w'];
    $date = $_POST['d'];
    $place = $_POST['p'];
    $issue = $_POST['t'];
    $status = $_POST['s'];
    $position = $_POST['o'];

    updissue($connection1, $id, $worker, $status, $position, $date, $place, $issue, $notes, $urgent);
} else echo 'updissue: Нет обязательных параметров в запросе.';

?>