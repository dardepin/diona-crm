<?php 

function getpositions($db, $page, $lim, $name)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;

    if($name == '') $sel = 'SELECT enumlabel FROM pg_enum WHERE enumtypid = (SELECT oid FROM pg_type WHERE typname = \'statuses\')';
    else $sel = 'SELECT enumlabel FROM pg_enum WHERE enumlabel LIKE \'%' . $name . '%\' AND enumtypid = (SELECT oid FROM pg_type WHERE typname = \'statuses\')';

    if($lim == 0) $sel .= ' ORDER BY enumlabel OFFSET ' . $begin;
    else $sel .= ' ORDER BY enumlabel LIMIT ' . $lim . ' OFFSET ' . $begin;

    $statuses = array();

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records > 0)
        {
            while($row = pg_fetch_row($res)) 
                $statuses[] = $row[0];
        }
    }
    if ($statuses) echo json_encode($statuses);
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

$p = isset($_POST['p']) && is_numeric($_POST['p']) ? $_POST['p']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:0;
if(isset($_POST['n'])) $n = $_POST['n']; else $n = '';

getpositions($connection1, $p, $q, $n);
?>