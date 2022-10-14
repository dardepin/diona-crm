<?php

function getissues($db, $page, $lim, $name, $status)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT * FROM issues INNER JOIN workers ON issues.worker_id = workers.worker_id WHERE issues.deleted = FALSE';
    if ($status != '') $sel .= ' AND issues.status = \'' . $status . '\'';
    if ($name != '') $sel .= ' AND workers.fullname ILIKE \'%' . $name . '%\'';
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
                $issue[] = $row[0];//issue_id
                $issue[] = $row[1];//worker_id
                $issue[] = $row[2];//status
                $issue[] = $row[3];//position
                $issue[] = $row[4];//creat time
                $issue[] = $row[5];//mod time
                $issue[] = $row[6];//issue date
                $issue[] = $row[7];//place
                $issue[] = $row[8];//issue
                $issue[] = $row[9];//notes
                $issue[] = $row[10];//urgent
                //11=deleted
                //12=worker-id
                $issue[] = $row[13];//fullname
                $issue[] = $row[14];//positions
                $issues[] = $issue;
            }
            if(count($issues)) echo json_encode($issues);
        }
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
    header('Location: login.php');
    exit();
}

$g = isset($_POST['g']) && is_numeric($_POST['g']) ? $_POST['g']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
$n = isset($_POST['n']) ? $_POST['n'] : '';
$s = isset($_POST['s']) ? $_POST['s'] : '';
if($s == 'Все статусы') $s = '';

getissues($connection1, $g, $q, $n, $s);
?>