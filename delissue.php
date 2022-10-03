<?php
function delissue($db, $id)
{
    if($id == 0)
    {
        echo 'issue_id не должен быть равен 0';
        return;
    }
    //$del = 'DELETE FROM issues WHERE issue_id = ' . $id . '';
    $upd = 'UPDATE issues SET deleted = TRUE, mod_time = current_timestamp WHERE issue_id = ' . $id;
    $res = pg_query($db, $upd);
    if(!$res) echo 'delissue: Ошибка БД #1: ' . pg_last_error($db);
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
delissue($connection1, $id)
?>