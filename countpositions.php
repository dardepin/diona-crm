<?php

function search($db, $page, $lim, $begin, $name)
{
    if($name != '') $sel = 'SELECT COUNT(*) AS total FROM pg_enum WHERE enumlabel ILIKE \'%' . $name . '%\' AND pg_enum.enumtypid = (SELECT oid FROM pg_type WHERE typname = \'positions\')';
    else $sel = 'SELECT COUNT(*) AS total FROM pg_enum WHERE enumtypid = (SELECT oid FROM pg_type WHERE typname = \'positions\')';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    return 0;
}

function gettotal($db, $page, $lim, $name)
{
    $total = array();

    if($page < 0) $page = 0;
    $begin = $page * $lim;
    $sel = 'SELECT COUNT(*) AS total FROM unnest(enum_range(NULL::positions))';

    $res = pg_query($db, $sel);
    {
        $records = pg_fetch_assoc($res);
        $total[] = $records['total']; //всего записей
        $total[] = search($db, $page, $lim, $begin, $name);
        $pages = ceil($total[1] / $lim);
        $total[] = ($pages == 0)?1:$pages;
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

gettotal($connection1, $p, $q, $n);

?>