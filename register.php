<!--- register new users --->
<!doctype html>
<html lang="en">
    <head>
        <?php include "./header.html" ?>
    </head>

    <body class="text-center">
<?php
require("connect.php");

if(isset($_POST['username']) && isset($_POST['userpassword']))
{
    $username = $_POST['username'];
    $userpass = $_POST['userpassword'];

    $sel = "SELECT name FROM users WHERE name = '$username'";
    $res = pg_query($connection1, $sel);
    if(!$res) $fmsg = "Ошибка базы данных #1:" . pg_last_error($connection1);

    //new user
    else
    {
        if(pg_numrows($res) == 0)
        {
            if ((!preg_match("#[0-9]+#", $userpass)) || (!preg_match("#[a-z]+#", $userpass)) ||
            (!preg_match("#[A-Z]+#", $userpass)) || (strlen($userpass) < 8)) $msg = "Пароль слишком простой.";
            else
            {
                $pwhash = password_hash($userpass, PASSWORD_BCRYPT);
                if(is_null($pwhash))
                    $fmsg = "Функция password_hash вернула null.";
                else
                {
                    $ins = "INSERT INTO users (name, password) VALUES ('$username', '$pwhash')";
                    $res = pg_query($connection1, $ins);
                    if($res) $msg = "Пользователь $username зарегистрирован. Вы можетей войти от имени нового пользователя: <a href=\"login.php\">login</a> . Не забудьте удалить страницу регистрации.";
                    else $msg = "Неудачная регистрация, ошибка базы данных #2: " . pg_last_error($connection1);
                }
            }
        }
        else $msg = "Пользователь уже зарегистрирован";
    }
}
?>
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
                                <button class="btn btn-lg btn-primary" type="submit">РЕГИСТРАЦИЯ</button>
                            </div>
                        </div>
                        <?php if(isset($msg)) { ?><div class="alert alert-primary" role="alert"> <?php echo $msg; ?></div><?php }?>    
                    </div>
                </div>
            </div>
        </form>
        <?php include "./footer.php" ?>
    </body>
</html>
