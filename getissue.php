<?php

function getissue($db, $id)
{
    if($id == 0) return;

    $sel = 'SELECT * FROM issues AS i JOIN workers AS w ON i.worker_id = w.worker_id WHERE i.issue_id=' . $id . '';

    $res = pg_query($db, $sel);
    if($res)
    {
        $records = pg_numrows($res);
        if($records == 1)
        {
            $issue = array();
            while($row = pg_fetch_row($res))
            {
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
            }
            if($issue) echo json_encode($issue);
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

$id = isset($_POST['id']) && is_numeric($_POST['id']) ? $_POST['id']:0;

getissue($connection1, $id);
?>