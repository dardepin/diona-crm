<?php
function getworkers($db, $page, $lim, $name, $position, $id)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;

    $sel = 'SELECT * FROM workers WHERE deleted = false ';
    if($id != '' || $name != '' || $position != '') $sel .= ' AND ';
    if($id != '')
    {
        $sel .= ' worker_id = ' . $id;
        if($name != '' || $position != '') $sel .= ' AND ';
    }
    if ($name != '')
    {
        $sel .= ' fullname ILIKE \'%' . $name . '%\'';
        if($position != '') $sel .= ' AND ';
    }
    if($position != '')
        $sel .= ' array[\'' . $position . '\']::positions[] <@ "current_positions"';
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
                $worker[] = str_replace(',', ', ', trim(trim($row[2], '"'), '{}'));//current_positions, need to split
                $worker[] = $row[3];//phone
                $worker[] = $row[4];//email
                $workers[] = $worker;
            }
            if(count($workers)) echo json_encode($workers);
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
    header('Location: login.php?r=positions');
    exit();
}

if(isset($_POST['i'])) $n = $_POST['i']; else $i = ''; // id
$p = isset($_POST['p']) && is_numeric($_POST['p']) ? $_POST['p']:0;
$q = isset($_POST['q']) && is_numeric($_POST['q']) ? $_POST['q']:15;
if(isset($_POST['n'])) $n = $_POST['n']; else $n = '';
if(isset($_POST['s'])) $s = $_POST['s']; else $s = ''; // position
if($s == 'Все должности') $s = '';

getworkers($connection1, $p, $q, $n, $s, $i);
?>