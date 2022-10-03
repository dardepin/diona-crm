<?php

function getworker($db, $id)
{
    if($id == 0) return;
    $sel = 'SELECT * FROM workers WHERE worker_id=' . $id . ' AND deleted = FALSE';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records == 1)
        {
            $worker = array();
            while($row = pg_fetch_row($res))
            {
                $worker[] = $row[1];//name
                $worker[] = str_replace(',', ', ', str_replace('"', '', trim($row[2], '{}')));//positions
                $worker[] = $row[3];//phone
                $worker[] = $row[4];//email
                $worker[] = $row[5];//created
            }
            if($worker) echo json_encode($worker);
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

$id = isset($_POST['i']) && is_numeric($_POST['i']) ? $_POST['i']:0;

getworker($connection1, $id);
?>