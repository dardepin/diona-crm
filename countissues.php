<?php

function searchissues($db, $page, $lim, $begin, $name, $status)
{
    $sel = 'SELECT COUNT(issue_id) AS total FROM issues INNER JOIN workers ON issues.worker_id = workers.worker_id WHERE issues.deleted = FALSE';
    if ($status != '') $sel .= ' AND issues.status = \'' . $status . '\'';
    if ($name != '') $sel .= ' AND workers.fullname ILIKE \'%' . $name . '%\'';

    $res = pg_query($db, $sel);
    if ($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    return 0;
}

function countissues($db, $page, $lim, $name, $status)
{
    $total = array();

    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT COUNT(issue_id) AS total FROM issues WHERE deleted = FALSE';
    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        $total[] = $records['total']; //всего записей
        $total[] = searchissues($db, $page, $lim, $begin, $name, $status); //найдено записей

        $pages = ceil($total[1] / $lim);
        $total[] = ($pages == 0)?1:$pages; //найдено страниц
        if($page > $total[2]) $page = $total[2];
        $total[] = $page + 1; //текущая страница
        
        echo json_encode($total);
    }
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
    header('Location: login.php?r=issues');
    exit();
}

$g = isset($_POST['g']) && is_numeric($_POST['g']) ? $_POST['g']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
$n = isset($_POST['n']) ? $_POST['n'] : ''; // name
$s = isset($_POST['s']) ? $_POST['s'] : ''; // status
if($s == 'Все статусы') $s = '';

countissues($connection1, $g, $q, $n, $s);

?>