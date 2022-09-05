<?php
function search($db, $page, $lim, $begin, $name, $appointed, $inprogress)
{
    $sel = 'SELECT COUNT(issue_id) AS total FROM issues as i JOIN workers AS w ON i.worker_id = w.worker_id';

    if(!$appointed || !$inprogress) $sel .= ' WHERE ';
    if(!$appointed && !$inprogress) $sel .= '(i.status != \'Назначено\' AND i.status != \'В процессе\')';
    else if(!$appointed) $sel .= ' i.status != \'Назначено\'';
    else if(!$inprogress) $sel .= ' i.status != \'В процессе\'';

    if ($name != '')
    {
        if(!$appointed || !$inprogress) $sel .= ' AND ';
        else $sel .= ' WHERE ';
        $sel .= 'w.fullname ILIKE \'%' . $name . '%\'';
    }

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        return $records['total'];
    }
    return 0;
}

function gettotal($db, $page, $lim, $name, $appointed, $inprogress)
{
    $total = array();

    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT COUNT(issue_id) AS total FROM issues';
    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_fetch_assoc($res);
        $total[] = $records['total']; //всего записей
        $total[] = search($db, $page, $lim, $begin, $name, $appointed, $inprogress); //найдено записей
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
    header('Location: login.php');
    exit();
}

$p = isset($_POST['p']) && is_numeric($_POST['p']) ? $_POST['p']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
if(isset($_POST['name'])) $name = $_POST['name']; else $name = '';
if(isset($_POST['p1'])) $appointed = true; else $appointed = false;
if(isset($_POST['p2'])) $inprogress = true; else $inprogress = false;

gettotal($connection1, $p, $q, $name, $appointed, $inprogress);

?>