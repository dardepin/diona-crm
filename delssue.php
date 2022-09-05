<?php

function delissue($db, $id)
{
    if($id == 0)
    {
        echo "issue_id не должен быть раен 0";
        return;
    }
    $del = 'DELETE FROM issues WHERE issue_id=' . $id . '';
    $res = pg_query($db, $del);
    if(!$res) echo "delissue: Ошибка БД #1: " . pg_last_error($db);
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
delissue($connection1, $id)

?>