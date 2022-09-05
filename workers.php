<!--- workers page --->
<!doctype html>
<html lang="en">
    <head>
        <?php include "./header.html";

        session_start();
        require_once("connect.php");
        $_POST['redir'] = 'workers.php';

        if(isset($_SESSION['username']) && (time() - $_SESSION['timeout'] < 900))
        {
            $username = $_SESSION['username'];
            $_SESSION['timeout'] = time();
        }
        else
        {
            session_destroy();
            header('Location: login.php?r=workers');
            exit();
        }
        ?>

    </head>

    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="issues.php"><img src="img/logo_blue.png" width="64" height="64" class="d-inline-block align-top" alt="">DIONA CRM</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-dropdown" aria-controls="navbar-dropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbar-dropdown">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/issues.php">Вызовы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/workers.php">Персонал</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" href="#">Еще что то</a>
                        </li>
                    </ul>
                </div><!--- collapse navbar-collapse --->
                <ul class="navbar-nav px-3">
                    <li class="nav-item text-nowrap">
                        <a class="nav-link" href="logout.php"><?php echo 'Выйти ' . $username . '?'; ?></a>
                    </li>
                </ul>
            </div><!--- container-fluid --->
        </nav><!--- navbar --->

        <br>
        <div class="container">
            <div class="btn-toolbar mb-3" role="toolbar" aria-label="Controls">
                <div class="btn-group">
                    <input type="checkbox" class="btn-check" name="options" id="check1" autocomplete="off" checked onclick="search(1)">
                    <label class="btn btn-outline-primary" for="check1">Сантехники</label>
                        
                    <input type="checkbox" class="btn-check" name="options" id="check2" autocomplete="off" checked onclick="search(1)">
                    <label class="btn btn-outline-primary" for="check2">Электрики</label>

                    <div class="input-group">
                        <input type="text" class="form-control" id="search-worker" onchange="search(1)" placeholder="Поиск по Ф.И.О." aria-label="Input group example" aria-describedby="btnGroupAddon">
                    </div>
                    <select class="form-control"  style="width:auto;" id="select-per-page" name="perpage" onchange="search(1)">
                        <option selected value=15>15</option>
                        <option value=30>30</option>
                        <option value=50>50</option>
                        <option value=100>100</option>
                    </select>
                </div><!--- btn-group --->
                <button type="button" class="btn btn-primary" id="add-worker-btn">Добавить сотрудника</button>
            </div> <!--- btn-toolbar --->
            <p class="workers-msg" id="workers-msg"></p>

            <nav aria-label="...">
                <ul class="pagination pagination-sm justify-content-center" id="workers-pages">
                </ul>
            </nav>

            <div id="workers-table"></div>
        </div> <!--- container --->

        <?php include "./footer.php" ?>

        <!--- add-worker modal --->
        <div class="modal fade" id="new-worker-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Ввести нового работника</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="new-worker-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <label for="new-worker-fullname">Ф.И.О</label>
                                <input type="text" class="form-control" id="new-worker-fullname" placeholder="Введите Ф.И.О." value = "" />
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="new-worker-position">Должность</label>
                                <select class="form-control" id="new-worker-position" name="positions"></select>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="new-worker-phone">Номер телефона (не обязательно)</label>
                                <input type="text" class="form-control" id="new-worker-phone" placeholder="Введите номер телефона." value = "" />
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="new-worker-email">Почтовый ящик (не обязательно)</label>
                                <input type="text" class="form-control" id="new-worker-email" placeholder="Введите адрес почты." value = "" />
                            </div>       
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary submitBtn" id="new-worker-submit-btn">Записать</button>
                    </div>
                    <p class="add-worker-msg" id="add-worker-msg"></p>
                </div>
            </div>
        </div>
            
        <!--- del-worker modal --->
        <div class="modal fade" id="del-worker-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Удалить работника?</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="del-worker-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <label for="del-worker-name">Ф.И.О. и должность</label>
                                <input type="hidden" class="form-control" id="del-worker-id" placeholder="0" value = "" readonly />
                                <input type="text" class="form-control" id="del-worker-name" placeholder="Ф.И.О. работника" value = "" readonly />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary submitBtn" id="del-worker-submit-btn">Удалить</button>
                    </div>
                    <p class="del-worker-msg" id="del-worker-msg"></p>
                </div>
            </div>
        </div>

        <!--- edit-worker modal --->
        <div class="modal fade" id="edit-worker-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Просмотр и редактирование работника</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="edit-worker-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="edit-worker-id" placeholder="0" value = "" readonly />
                                <label for="edit-worker-name">Ф.И.О.</label>
                                <input type="text" class="form-control" id="edit-worker-name" placeholder="Ф.И.О. работника" value = "" />
                                <br>
                                <label for="edit-worker-position">Должность</label>
                                <select class="form-control" id="edit-worker-position" name="positions">
                                </select>
                                <br>
                                <label for="edit-worker-phone">Телефон</label>
                                <input type="text" class="form-control" id="edit-worker-phone" placeholder="" value = "" />
                                <br>
                                <label for="edit-worker-email">Электронная почта</label>
                                <input type="text" class="form-control" id="edit-worker-email" placeholder="" value = "" />
                                <br>
                                <label for="edit-worker-date">Дата создания</label>
                                <input type="text" class="form-control" id="edit-worker-date" placeholder="" value = "" readonly />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-worker-submit-btn">Сохранить</button>
                    </div>
                    <p class="edit-worker-msg" id="edit-worker-msg"></p>
                </div>
            </div>
        </div>

        <!--- call worker modal --->
        <div class="modal fade" id="call-worker-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Оформить вызов на работника?</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="call-worker-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="call-worker-id" placeholder="0" value = "" readonly />
                                <label for="call-worker-name">Ф.И.О. и должность</label>
                                <input type="text" class="form-control" id="call-worker-name" placeholder="Ф.И.О. работника" value = "" readonly />
                                <br>
                                <label for="call-worker-time">Время вызова</label><br>
                                <input type="date" id="call-worker-time" name="call-worker-time" value="" min="" max="" required>
                                <br><br>
                                <label for="call-worker-place">Место вызова</label>
                                <textarea class="form-control" id="call-worker-place" rows="1"></textarea>
                                <br>
                                <label for="call-worker-text">Описание вызова</label>
                                <textarea class="form-control" id="call-worker-text" rows="4"></textarea>
                                <br>
                                <input type="checkbox" class="btn-check" name="options" id="urgent" autocomplete="off">
                                <label class="btn btn-outline-primary" for="urgent">Важно!</label>
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="call-worker-submit-btn">Создать</button>
                    </div>
                    <p class="call-worker-msg" id="call-worker-msg"></p>
                </div>
            </div>
        </div>
    </body>

<script>
function pages(params) //для пагинации
{
    $.ajax({
        async: false,
        type: 'POST',
        url: 'countworkers.php',
        data: params,
        beforeSend: function()
        {
            document.getElementById("workers-pages").innerHTML = "";
        },
        success: function(data) //всего, найдено, текущая страница, всего страниц
        {
            if(data != '')
            {
                var total = JSON.parse(data);
                var all = total[0]; var found = total[1];
                var pages = total[2]; var page = total[3];
                $('#workers-msg').html('<div class="alert alert-primary" role="alert">Всего: ' + all + ' работников, найдено: ' + found + ' работников, всего страниц: ' + pages + ', текущая страница: ' + page + '</div>');

                for (let i = 1; i <= pages; i++)
                {
                    if(i === page) document.getElementById("workers-pages").innerHTML += "<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">" + i + "</span></li>";
                    else document.getElementById("workers-pages").innerHTML += "<li onclick=\"search(this.id)\" class=\"page-item\" id=\"" + i + "\"><a class=\"page-link\" >" + i + "</a></li>";
                }
            }
            else $('#workers-msg').html('<div class="alert alert-primary" role="alert">Нет работников</div>');
        },
        error: function(xhr, status, error)
        {
            $('#workers-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return;
}
</script>

<script>
function getdate() //возвращает ["текущая дата", "+ месяц"]
{
    var today = new Date();
    var d = String(today.getDate()).padStart(2, '0');
    var m = String(today.getMonth() + 1).padStart(2, '0');
    var y = today.getFullYear();
    now = y + '-' + m + '-' + d;

    today.setMonth(today.getMonth() + 1);
    d = String(today.getDate()).padStart(2, '0');
    m = String(today.getMonth() + 1).padStart(2, '0');
    y = today.getFullYear();
    max = y + '-' + m + '-' + d;
    return [now, max];
}
</script>

<script>
function search(page) //поиск сотрудников по параметрам
{
    const name = $("#search-worker").val().trim();
    var q = $('#select-per-page').val();
        
    if(!Number.isInteger(page))
    {
        console.log("Page incorrect: " + page + " " + typeof page);
        page = parseInt(page);
    }
        
    var params = "p=" + (page - 1);
    params += '&q=' + q;
    if(name != '') params += "&name=" + name;
    if(document.getElementById('check1').checked) params += "&p1=true";
    if(document.getElementById('check2').checked) params += "&p2=true";
    console.log(params);

    pages(params);

    $.ajax ({
        type: 'POST',
        url: 'getworkers.php',
        data: params,
        beforeSend: function()
        {
            document.getElementById("workers-table").innerHTML = "";
        },
        success: function(data)
        {
            if(data == '\n') $('#workers-msg').html('<div class="alert alert-primary" role="alert">Нет работников</div>');
            else
            {
                //display table
                var workers = JSON.parse(data);
                var table = "<table class=\"table table-hover\">" + 
                "<thead class=\"thead-primary\"><tr class=\"table-primary\">" +
                "<th scope=\"col\">#</th>" +
                "<th scope=\"col\">Имя</th>" +
                "<th scope=\"col\">Должность</th>" +
                "<th scope=\"col\">Телефон</th>" +
                "<th scope=\"col\">Электронная почта</th>" +
                "<th scope=\"col\">Создать вызов</th>" +
                "<th scope=\"col\">Редактировать</th>" +
                "<th scope=\"col\">Удалить</th>" +
                "</tr></thead><tbody>";
                for (let i = 0; i < workers.length; i++)
                {
                    var callbutton = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"call-worker-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#call-worker-modal\" data-whatever=\"" + workers[i][0] + "\"><img class=\"img-responsive\" title=\"call\" src=\"img/call.svg\"/></button></td>";

                    var editbutton = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-worker-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-worker-modal\" data-whatever=\"" + workers[i][0] + "\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></button></td>";

                    var deletebutton  = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"delete-worker-btn\" data-bs-toggle=\"modal\" data-bs-target=\"#del-worker-modal\" data-whatever=\"" + workers[i][0] + "\"><img class=\"img-responsive\" title=\"delete\" src=\"img/delete.svg\"/></button></td>";
                    
                    table += "<tr><th scope=\"row\">" + workers[i][0] + "</th>" +
                    "<td>" + workers[i][1] + "</td>" +
                    "<td>" + workers[i][2] + "</td>" +
                    "<td>" + workers[i][3] + "</td>" +
                    "<td>" + workers[i][4] + "</td>" +
                    callbutton + editbutton + deletebutton + "</tr>";
                }
                table += "</tbody></table><br><br>";
                document.getElementById("workers-table").innerHTML = table;
            }
        },
        error: function(xhr, status, error)
        {
            $('#workers-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return;
}
</script>

<script>
function getpositions(msg_id) //возвращает список должностей или пишет ошибку в поле
{
    var positions;

    $.ajax({
        async: false,
        type: 'POST',
        url: 'getpositions.php',
        beforeSend: function()
        {
            $(msg_id).html("");
        },
        success: function(data)
        {
            positions = JSON.parse(data);
                //console.log(positions);
        },
        error: function(xhr, status, error)
        {
            $(msg_id).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return positions;
}
</script>

<script>
function getworker(worker_id, msg_id) //получает информацию об одном работнике
{
    var worker;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getworker.php',
        data: 'id=' + worker_id,
        beforeSend: function()
        {
            $(msg_id).html('');
        },
        success: function(data)
        {
            if(data == '') $(msg_id).html('<div class="alert alert-primary" role="alert">Работник с таким id не найден</div>');
            else worker = JSON.parse(data);
        },
        error: function(xhr, status, error)
        {
            $(msg_id).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return worker;
}
</script>

<script>
$(document).ready(function() //загружает список работников как только страница загрузится
{
    search(1);
});
</script>

<script>
$("#add-worker-btn").click(function() //при нажатии на кнопку "добавить работника"
{
    var new_worker_modal = new bootstrap.Modal(document.getElementById('new-worker-modal'));
    new_worker_modal.show();

    $('#new-worker-submit-btn').attr("disabled","disabled");
    $('#new-worker-modal-body').css('opacity', '.5');
    $("#new-worker-position").empty();
    var positions = getpositions('#add-worker-msg');
    if(positions != '')
    {
        for(p in positions) 
            $("#new-worker-position").append("<option value=" + positions[p] + ">" + positions[p] + "</option>");
    }
    $('#new-worker-submit-btn').removeAttr("disabled");
    $('#new-worker-modal-body').css('opacity', '');
});
</script>

<script>
$('#new-worker-submit-btn').click(function() //добавляет работника в бд, перезагружает таблицу, если ок
{
    const fullname = typeof $("#new-worker-fullname").val() === 'string' ? $("#new-worker-fullname").val().trim() : '';
    const position = typeof $("#new-worker-position").val() === 'string' ? $("#new-worker-position").val().trim() : '';
    const phone = typeof $("#new-worker-phone").val() === 'string' ? $("#new-worker-phone").val().trim() : '';
    const email = typeof $("#new-worker-email").val() === 'string' ? $("#new-worker-email").val().trim() : '';

    if(fullname == '')
    {
        $('#add-worker-msg').html('<div class="alert alert-primary" role="alert">Введите Ф.И.О. обязательно</div>');
        return false;
    }

    if(position == '')
    {
        $('#add-worker-msg').html('<div class="alert alert-primary" role="alert">Выберите должность обязательно</div>');
        return false;
    }

    var params = 'name=' + fullname + '&position=' + position;
    if(phone != '') params += "&phone=" + encodeURIComponent(phone);
    if(email != '') params += "&email=" + encodeURIComponent(email);

    $.ajax({
        type: 'POST',
        url: 'setworker.php',
        data: params,
        beforeSend: function()
        {
            $('#new-worker-submit-btn').attr("disabled","disabled");
            $('#new-worker-modal-body').css('opacity', '.5');
            $("#add-worker-msg").html("");
        },
        success: function(data)
        {
            if(data != '') $("#add-worker-msg").html('<div class="alert alert-primary" role="alert">' + data +'</div>');
            $('#new-worker-submit-btn').removeAttr("disabled");
            $('#new-worker-modal-body').css('opacity', '');
            search(1);
        },
        error: function(xhr, status, error)
        {
            $('#new-worker-submit-btn').removeAttr("disabled");
            $('#new-worker-modal-body').css('opacity', '');
            $('#add-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму удаления работника
{
    $('#del-worker-modal').on('show.bs.modal', function(e)
    {
        var worker_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="del-worker-id"]').val(worker_id);

        $('#del-worker-submit-btn').attr("disabled","disabled");
        $('#del-worker-modal-body').css('opacity', '.5');

        var worker = getworker(worker_id, '#del-worker-msg');
        if(worker != '') $(e.currentTarget).find('input[id="del-worker-name"]').val(worker[0] + ' ' + worker[1]);

        $('#del-worker-submit-btn').removeAttr("disabled");
        $('#del-worker-modal-body').css('opacity', '');
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму вызова работника
{
    $('#call-worker-modal').on('show.bs.modal', function(e)
    {
        var worker_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="call-worker-id"]').val(worker_id);

        $('#call-worker-submit-btn').attr("disabled","disabled");
        $('#call-worker-modal-body').css('opacity', '.5');

        var worker = getworker(worker_id, '#call-worker-msg');
        if(worker != '') $(e.currentTarget).find('input[id="call-worker-name"]').val(worker[0] + ' ' + worker[1]);

        var curr_date = getdate();
        const call_time = document.querySelector('input[type="date"]');
        call_time.value = curr_date[0]; call_time.min = curr_date[0]; call_time.max = curr_date[1];

        $('#call-worker-submit-btn').removeAttr("disabled");
        $('#call-worker-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#del-worker-submit-btn').click(function(e) //подтвердить удаление работника
{
    var worker_id = document.getElementById("del-worker-id").value;

    $.ajax ({
        type: 'POST',
        url: 'delworker.php',
        data: 'id=' + worker_id,
        beforeSend: function()
        {
            $('#del-worker-submit-btn').attr("disabled","disabled");
            $('#del-worker-modal-body').css('opacity', '.5');
            $("#del-worker-msg").html("");
        },
        success: function(data)
        {
            $('#del-worker-submit-btn').removeAttr("disabled");
            $('#del-worker-modal-body').css('opacity', '');
            if(data == '')
            {
                $('#del-worker-msg').html('<div class="alert alert-primary" role="alert">Работник #' + worker_id + ' удален</div>');
                search(1);
            }
            else $('#del-worker-msg').html('<div class="alert alert-primary" role="alert">' + data +'</div>');
        },
        error: function(xhr, status, error)
        {
            $('#del-worker-submit-btn').removeAttr("disabled");
            $('#del-worker-modal-body').css('opacity', '');
            $('#del-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму редактирования работника
{
    $('#edit-worker-modal').on('show.bs.modal', function(e)
    {
        var worker_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-worker-id"]').val(worker_id);

        $('#edit-worker-submit-btn').attr("disabled","disabled");
        $('#edit-worker-modal-body').css('opacity', '.5');
        $("#edit-worker-position").empty();

        var worker = getworker(worker_id, '#edit-worker-msg');
        var positions = getpositions('#edit-worker-msg');

        if(worker != '' && positions != '')
        {
            $(e.currentTarget).find('input[id="edit-worker-name"]').val(worker[0]);
            worker_position = worker[1];
            $(e.currentTarget).find('input[id="edit-worker-phone"]').val(worker[2]);
            $(e.currentTarget).find('input[id="edit-worker-email"]').val(worker[3]);
            $(e.currentTarget).find('input[id="edit-worker-date"]').val(worker[4]);

            for(p in positions)
            {
                if(worker_position === positions[p]) 
                $("#edit-worker-position").append("<option value=" + positions[p] + " selected>" + positions[p] + "</option>");
                else $("#edit-worker-position").append("<option value=" + positions[p] + ">" + positions[p] + "</option>");
            }
        }

        $('#edit-worker-submit-btn').removeAttr("disabled");
        $('#edit-worker-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#edit-worker-submit-btn').click(function(e) //подтвердить редактирование работника
{
    const worker_id = typeof $("#edit-worker-id").val() === 'string' ? $("#edit-worker-id").val().trim() : '';
    const fullname = typeof $("#edit-worker-name").val() === 'string' ? $("#edit-worker-name").val().trim() : '';
    const position = typeof $("#edit-worker-position").val() === 'string' ? $("#edit-worker-position").val().trim() : '';
    const phone = typeof $("#edit-worker-phone").val() === 'string' ? $("#edit-worker-phone").val().trim() : '';
    const email = typeof $("#edit-worker-email").val() === 'string' ? $("#edit-worker-email").val().trim() : '';

    if(worker_id == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(fullname == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Введите Ф.И.О. обязательно</div>');
        return false;
    }
    if(position == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Выберите должность обязательно</div>');
        return false;
    }

    var params = 'id=' + worker_id + '&name=' + fullname + '&position=' + position;
    if(phone != '') params += "&phone=" + encodeURIComponent(phone);
    if(email != '') params += "&email=" + encodeURIComponent(email);

    $.ajax ({
        type: 'POST',
        url: 'updworker.php',
        data: params,
        beforeSend: function()
        {
            $('#edit-worker-submit-btn').attr("disabled","disabled");
            $('#edit-worker-modal-body').css('opacity', '.5');
            $("#edit-worker-msg").html("");
        },
        success: function(data)
        {
            $('#edit-worker-submit-btn').removeAttr("disabled");
            $('#edit-worker-modal-body').css('opacity', '');

            if(data != '') $("#edit-worker-msg").html('<div class="alert alert-primary" role="alert">' + data +'</div>');
            else
            {
                search(1);
                $('#edit-worker-modal').modal('hide');
            }
        },
        error: function(xhr, status, error)
        {
            $('#edit-worker-submit-btn').removeAttr("disabled");
            $('#edit-worker-modal-body').css('opacity', '');
            $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$('#call-worker-submit-btn').click(function(e) //подтвердить вызов работника
{
    const call_time = document.querySelector('input[type="date"]').value;
    const worker_id = typeof $("#call-worker-id").val() === 'string' ? $("#call-worker-id").val().trim() : '';
    const place = typeof $("#call-worker-place").val() === 'string' ? $("#call-worker-place").val().trim() : '';
    const issue = typeof $("#call-worker-text").val() === 'string' ? $("#call-worker-text").val().trim() : '';
    //add notes

    var urgent = 0;
    if(document.getElementById('urgent').checked) urgent = 1;

    if(call_time == '')
    {
        $('#call-worker-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату</div>');
        return false;
    }
    if(worker_id == '')
    {
        $('#call-worker-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(place == '')
    {
        $('#call-worker-msg').html('<div class="alert alert-primary" role="alert">Напишите место вызова</div>');
        return false;
    }
    if(issue == '')
    {
        $('#call-worker-msg').html('<div class="alert alert-primary" role="alert">Напишите причину вызова</div>');
        return false;
    }

    var params = 'id=' + worker_id + '&t=' + encodeURIComponent(call_time) + '&p=' + encodeURIComponent(place) + '&i=' + encodeURIComponent(issue);
    if (urgent) params += '&u=' + urgent;

    $.ajax ({
        type: 'POST',
        url: 'setcall.php',
        data: params,
        beforeSend: function()
        {
            $('#call-worker-submit-btn').attr("disabled","disabled");
            $('#call-worker-modal-body').css('opacity', '.5');
            $("#call-worker-msg").html("");
        },
        success: function(data)
        {
            $('#call-worker-submit-btn').removeAttr("disabled");
            $('#call-worker-modal-body').css('opacity', '');
            if(data != '') $("#call-worker-msg").html('<div class="alert alert-primary" role="alert">' + data +'</div>');
        },
        error: function(xhr, status, error)
        {
            $('#call-worker-submit-btn').removeAttr("disabled");
            $('#call-worker-modal-body').css('opacity', '');
            $('#call-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>
</html>