<?php
function search($db, $page, $lim, $begin, $name, $plumbers, $electrics)
{
    $sel = 'SELECT COUNT(worker_id) AS total FROM workers';

    if(!$plumbers || !$electrics) $sel .= ' WHERE ';
    if(!$plumbers && !$electrics)  $sel .= '(current_position != \'Электрик\' AND current_position != \'Сантехник\')';
    else if(!$plumbers) $sel .= 'current_position != \'Сантехник\'';
    else if(!$electrics) $sel .= 'current_position != \'Электрик\'';

    if($name != '')
    {
        if(!$plumbers || !$electrics) $sel .= ' AND ';
        else $sel .= ' WHERE ';
        $sel .= ' fullname ILIKE \'%' . $name . '%\'';
    }

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    return 0;
}

function gettotal($db, $page, $lim, $name, $plumbers, $electrics)
{
    $total = array();

    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT COUNT(worker_id) AS total FROM workers';
    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        $total[] = $records['total']; //всего записей
        $total[] = search($db, $page, $lim, $begin, $name, $plumbers, $electrics); //найдено записей
        $pages = ceil($total[1] / $lim);
        $total[] = ($pages == 0)?1:$pages; //найдено страниц
        if($page > $total[2]) $page = $total[2];
        $total[] = $page + 1; //текущая страница
        
        echo json_encode($total);
    }
    //draw pages?
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

$p = isset($_POST['p']) && is_numeric($_POST['p']) ? $_POST['p']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
if(isset($_POST['name'])) $name = $_POST['name']; else $name = '';
if(isset($_POST['p1'])) $plumbers = true; else $plumbers = false;
if(isset($_POST['p2'])) $electrics = true; else $electrics = false;

gettotal($connection1, $p, $q, $name, $plumbers, $electrics);

?>