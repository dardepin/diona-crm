<?php

function search($db, $page, $lim, $begin, $name, $position)
{
    $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE deleted = FALSE ';
    if ($name != '') $sel .= ' AND fullname ILIKE \'%' . $name . '%\'';
    if ($position != '') $sel .= ' AND array[\'' . $position . '\']::positions[] <@ "current_positions"';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    return 0;
}

function gettotal($db, $page, $lim, $name, $position)
{
    $total = array();

    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT COUNT(worker_id) AS total FROM workers WHERE deleted = FALSE';
    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        $total[] = $records['total']; //всего записей
        $total[] = search($db, $page, $lim, $begin, $name, $position); //найдено записей

        $pages = ceil($total[1] / $lim);
        $total[] = ($pages == 0)?1:$pages; //найдено страниц
        if($page > $total[2]) $page = $total[2];
        $total[] = $page + 1; //текущая страница
        
        echo json_encode($total);
    }
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

$p = isset($_POST['p']) && is_numeric($_POST['p']) ? $_POST['p']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
if(isset($_POST['n'])) $n = $_POST['n']; else $n = '';
if(isset($_POST['s'])) $s = $_POST['s']; else $s = ''; // position
if($s == 'Все должности') $s = '';

gettotal($connection1, $p, $q, $n, $s);

?>