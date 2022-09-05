<?php

function getissues($db, $page, $lim, $name, $appointed, $inprogress)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT * FROM issues AS i JOIN workers AS w ON i.worker_id = w.worker_id';

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
    $sel .= ' LIMIT ' . $lim . ' OFFSET ' . $begin;

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records > 0)
        {
            $issues = array();
            while($row = pg_fetch_row($res))
            {
                $issue = array();
                $issue[] = $row[0];//id
                $issue[] = $row[1];//worker_id
                $issue[] = $row[2];//status
                $issue[] = $row[3];//creat time
                $issue[] = $row[4];//mod time
                $issue[] = $row[5];//issue date
                $issue[] = $row[6];//place
                $issue[] = $row[7];//issue
                $issue[] = $row[8];//notes
                $issue[] = $row[9];//urgent
                //10=worker-id
                $issue[] = $row[11];//fullname
                $issue[] = $row[12];//position
                $issues[] = $issue;
            }
            if(count($issues)) echo json_encode($issues);
        }
    }
    return;
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

getissues($connection1, $p, $q, $name, $appointed, $inprogress);
?>