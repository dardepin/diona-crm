<?php

function getworkers($db, $page, $lim, $name, $position)
{
    if($page < 0) $page = 0;
    $begin = $page * $lim;
    $sel = 'SELECT * FROM workers WHERE deleted = false';
    if($lim != 0) //pagination, include fired workers
    {
        if($name != '')
            $sel .= ' AND fullname ILIKE \'%' . $name . '%\'';
        if($position != '')
        {
            if($position == 'Уволенные работники') $sel .= ' AND fired = TRUE';
            else $sel .= ' AND array[\'' . $position . '\']::positions[] <@ "current_positions"';
        }
        $sel .= ' LIMIT ' . $lim . ' OFFSET ' . $begin;
    }

    $res = pg_query($db, $sel);//getallworkers
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
                $worker[] = $row[6];//fired
                $worker[] = $row[7];//deleted

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
$g = 0; $q = 0; $n = ''; $o = '';
if(isset($_POST['g']) && is_numeric($_POST['g']) && isset($_POST['q']) && is_numeric($_POST['q']))
{
    $g = $_POST['g'];
    $q = $_POST['q'];
    $n = (isset($_POST['n'])) ? $_POST['n']:'';
    $o = (isset($_POST['o'])) ? $_POST['o']:'';
    if($o == 'Все должности') $o = '';
}

getworkers($connection1, $g, $q, $n, $o);
?>