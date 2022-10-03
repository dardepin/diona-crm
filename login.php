<!--- login as users --->
<!doctype html>
<html lang="en">
    <head>
        <?php include "./header.html" ?>
    </head>

    <body class="text-center">
        <form class="form-signin" method="POST">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 offset-md-4">
                        <div class="card text-center card  bg-default mb-3">
                            <div class="card-header">
                                <img class="img-fluid mx-auto" src="img/logo2.png" alt="dionalogo" width="800" height="600">
                            </div>
                            <div class="card-body">
                                <input type="text" id="username" class="form-control input-sm chat-input" placeholder="Пользователь" name = "username" />
                                </br>
                                <input type="password" id="userpassword" class="form-control input-sm chat-input" placeholder="Пароль" name="userpassword" />
                            </div>
                            <div class="card-footer text-muted">
                                <button class="btn btn-lg btn-primary" type="submit">ВОЙТИ</button>
                            </div>
<?php
session_start();
require("connect.php");

if(isset($_POST['username']) && isset($_POST['userpassword']))
{
    $username = $_POST['username'];
    $userpass = $_POST['userpassword'];

    $sel = "SELECT name, password FROM users WHERE name='$username'";
    $res = pg_query($connection1, $sel);
    if (!$res) echo '<div class="alert alert-primary" role="alert">Ошибка базы данных #1: ' . pg_last_error($connection1) . '</div>';
    if(pg_numrows($res) == 0) echo '<div class="alert alert-primary" role="alert">Неверное имя пользователя или пароль</div>';

    else
    {
        $row = pg_fetch_row($res);
        $pwhash = $row[1];

        if(password_verify($userpass, $pwhash))
        {
            $_SESSION['username'] = $username;
            $_SESSION['timeout'] = time();

            if(isset($_GET['r'])) header('Location:' . $_GET['r'] . '.php');
            else header('Location: issues.php');
            
            exit();
        }
        else echo '<div class="alert alert-primary" role="alert">Неверное имя пользователя или пароль</div>';
    }
}
?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php include "./footer.php" ?>
    </body>
</html>