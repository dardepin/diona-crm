
<?php
require_once("connect.php");
session_start();

function getworkers($db, $page, $lim, $name, $plumbers, $electrics)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT * FROM workers';
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
    $sel .= ' LIMIT ' . $lim . ' OFFSET ' . $begin;


    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records > 0)
        {
            $workers = array();
            while($row = pg_fetch_row($res))
            {
                $worker = array();
                $worker[] = $row[0];//worker_id
                $worker[] = $row[1];//fullname
                $worker[] = $row[2];//current_position
                $worker[] = $row[3];//phone
                $worker[] = $row[4];//email
                $workers[] = $worker;
            }
            if(count($workers)) echo json_encode($workers);
        }
    }
    return;
}

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

getworkers($connection1, $p, $q, $name, $plumbers, $electrics);

?>