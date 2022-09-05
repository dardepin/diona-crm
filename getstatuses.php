<?php 
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
 
$sel = 'SELECT unnest(enum_range(NULL::statuses))'; 

$positions = array();
 
$res = pg_query($connection1, $sel); 
if($res) 
{ 
    $records = pg_numrows($res); 
    if($records > 0) 
    { 
        while($row = pg_fetch_row($res)) 
        {
            $positions[] = $row[0];
        } 
    }
    if ($positions) echo json_encode($positions);
}
?>