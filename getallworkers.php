<?php

function getallworkers($db)
{
    $sel = 'SELECT * FROM workers WHERE deleted = FALSE ORDER BY fullname';

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

getallworkers($connection1);
?>