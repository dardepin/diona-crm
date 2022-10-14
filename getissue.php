<?php

function getissue($db, $id)
{
    if($id == 0) return;

    $sel = 'SELECT * FROM issues JOIN workers ON issues.worker_id = workers.worker_id WHERE issues.issue_id = ' . $id . ' AND issues.deleted = FALSE';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records == 1)
        {
            $issue = array();
            while($row = pg_fetch_row($res))
            {
                $issue[] = $row[0];//issue_id
                $issue[] = $row[1];//worker_id
                $issue[] = $row[2];//status
                $issue[] = $row[3];//position
                $issue[] = $row[4];//create time
                $issue[] = $row[5];//mod date
                $issue[] = $row[6];//issue date
                $issue[] = $row[7];//place
                $issue[] = $row[8];//issue
                $issue[] = $row[9];//notes
                $issue[] = $row[10];//urgent
                //11=deleted
                //12=worker_id
                $issue[] = $row[13];//fullname
                $issue[] = $row[18];//fired
                $issue[] = $row[19];//deleted
            }
            if($issue) echo json_encode($issue);
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

getissue($connection1, $id);
?>