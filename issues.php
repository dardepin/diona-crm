<!--- main page --->
<!doctype html>
<html lang="en">
    <head>
        <?php include "./header.html";

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
                            <a class="nav-link active" href="/issues.php">Вызовы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/workers.php">Персонал</a>
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
        </nav><!--- nav --->

        <br>
        <div class="container">
            <div class="btn-toolbar mb-3" role="toolbar" aria-label="Controls">
                <div class="btn-group">
                    <input type="checkbox" class="btn-check" name="options" id="check1" autocomplete="off" checked onclick="search(1)">
                    <label class="btn btn-outline-primary" for="check1">Назначенные</label>
                        
                    <input type="checkbox" class="btn-check" name="options" id="check2" autocomplete="off" checked onclick="search(1)">
                    <label class="btn btn-outline-primary" for="check2">В процессе</label>

                    <div class="input-group">
                        <input type="text" class="form-control" id="search-worker" onchange="search(1)" placeholder="Поиск задачи по Ф.И.О." aria-label="Input group example" aria-describedby="btnGroupAddon">
                    </div>
                    <select class="form-control"  style="width:auto;" id="select-per-page" name="perpage" onchange="search(1)">
                        <option selected value=15>15</option>
                        <option value=30>30</option>
                        <option value=50>50</option>
                        <option value=100>100</option>
                    </select>
                </div><!--- btn-group --->
                <button type="button" class="btn btn-primary" id="add-issue-btn">Добавить задачу</button>
            </div> <!--- btn-toolbar --->
            <p class="workers-msg" id="issues-msg"></p>

            <nav aria-label="...">
                <ul class="pagination pagination-sm justify-content-center" id="issues-pages">
                </ul>
            </nav>

            <div id="issues-table"></div>
        </div> <!--- container --->

        <?php include "./footer.php" ?>

        <!--- edit-issue-modal --->
        <div class="modal fade" id="edit-issue-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Просмотр и редактирование вызова</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="edit-issue-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="edit-issue-id" placeholder="0" value = "" readonly />
                                <label for="edit-issue-name">Ф.И.О. работника и должность</label>
                                <input type="text" class="form-control" id="edit-issue-name" placeholder="Ф.И.О. работника и должность" value = "" readonly />
                                <br>
                                <label for="edit-issue-status">Статус вызова</label>
                                <select class="form-control" id="edit-issue-status" name="statuses">
                                </select>
                                <label for="edit-issue-time">Время вызова</label><br>
                                <input type="date" id="edit-issue-time" name="edit-issue-time" value="" min="" max="" required>
                                <br><br>
                                <label for="edit-issue-place">Место вызова</label>
                                <textarea class="form-control" id="edit-issue-place" rows="1"></textarea>
                                <br>
                                <label for="edit-issue-text">Описание вызова</label>
                                <textarea class="form-control" id="edit-issue-text" rows="4"></textarea>
                                <br>
                                <label for="edit-issue-text">Комментарии</label>
                                <textarea class="form-control" id="edit-issue-notes" rows="3"></textarea>
                                <br>
                                <input type="checkbox" class="btn-check" name="options" id="edit-issue-urgent" autocomplete="off">
                                <label class="btn btn-outline-primary" for="edit-issue-urgent">Важно!</label>
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-issue-submit-btn">Сохранить</button>
                    </div>
                    <p class="edit-issue-msg" id="edit-issue-msg"></p>
                </div>
            </div>
        </div>

        <!--- new-issue-modal --->
        <div class="modal fade" id="new-issue-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Создать новый вызов</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="new-issue-modal-body">
                    <!--- контент окна ниже --->
                        <div class="form-group">
                            <label for="new-issue-workers-select">Выберите работника</label>
                            <select class="form-control" id="new-issue-workers-select" name="workers"></select>
                            <br>
                            <label for="new-issue-time">Время вызова</label><br>
                            <input type="date" id="new-issue-time" name="new-issue-time" value="" min="" max="" required>
                            <br><br>
                            <label for="new-issue-place">Место вызова</label>
                            <textarea class="form-control" id="new-issue-place" rows="1"></textarea>
                            <br>
                            <label for="new-issue-text">Описание вызова</label>
                            <textarea class="form-control" id="new-issue-text" rows="4"></textarea>
                            <br>
                            <input type="checkbox" class="btn-check" name="options" id="new-issue-urgent" autocomplete="off">
                            <label class="btn btn-outline-primary" for="new-issue-urgent">Важно!</label>
                        </div>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="new-issue-submit-btn">Создать</button>
                    </div>
                    <p class="new-issue-msg" id="new-issue-msg"></p>
                </div>
            </div>
        </div>

        <!--- copy-issue-modal --->
        <div class="modal fade" id="copy-issue-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Копировать вызов</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="copy-issue-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="copy-issue-id" placeholder="0" value = "" readonly />
                                <input type="hidden" class="form-control" id="copy-worker-id" placeholder="0" value = "" readonly />
                                <label for="copy-issue-name">Ф.И.О. работника и должность</label>
                                <input type="text" class="form-control" id="copy-issue-name" placeholder="Ф.И.О. работника и должность" value = "" readonly />
                                <br>
                                <label for="copy-issue-status">Статус вызова</label>
                                <select class="form-control" id="copy-issue-status" name="statuses">
                                </select>
                                <label for="copy-issue-time">Время вызова</label><br>
                                <input type="date" id="copy-issue-time" name="copy-issue-time" value="" min="" max="" required>
                                <br><br>
                                <label for="copy-issue-place">Место вызова</label>
                                <textarea class="form-control" id="copy-issue-place" rows="1"></textarea>
                                <br>
                                <label for="copy-issue-text">Описание вызова</label>
                                <textarea class="form-control" id="copy-issue-text" rows="4"></textarea>
                                <br>
                                <label for="copy-issue-text">Комментарии</label>
                                <textarea class="form-control" id="copy-issue-notes" rows="3"></textarea>
                                <br>
                                <input type="checkbox" class="btn-check" name="options" id="copy-issue-urgent" autocomplete="off">
                                <label class="btn btn-outline-primary" for="copy-issue-urgent">Важно!</label>
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="copy-issue-submit-btn">Копировать</button>
                    </div>
                    <p class="copy-issue-msg" id="copy-issue-msg"></p>
                </div>
            </div>
        </div>

        <!--- del-issue-modal --->
        <div class="modal fade" id="del-issue-modal" role="dialog">
            <div class="modal-dialog">
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Удалить вызов?</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="del-issue-modal-body">
                    <!--- контент окна ниже --->
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="del-issue-id" placeholder="0" value = "" readonly />
                            <label for="del-issue-text">Описание вызова</label>
                            <textarea class="form-control" id="del-issue-text" rows="3" readonly></textarea>
                        </div>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="del-issue-submit-btn">Удалить</button>
                    </div>
                    <p class="del-issue-msg" id="del-issue-msg"></p>
                </div>
            </div>
        </div>
    </body>

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
function getstatuses(msg_id) //возвращает список статусов или пишет ошибку в поле
{
    var statuses;

    $.ajax({
        async: false,
        type: 'POST',
        url: 'getstatuses.php',
        beforeSend: function()
        {
            $(msg_id).html("");
        },
        success: function(data)
        {
            statuses = JSON.parse(data);
            console.log(statuses);
        },
        error: function(xhr, status, error)
        {
            $(msg_id).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return statuses;
}
</script>

<script>
function getissue(issue_id, msg_id) //получает информацию об одном вызове
{
    var issue;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getissue.php',
        data: 'id=' + issue_id,
        beforeSend: function()
        {
            $(msg_id).html('');
        },
        success: function(data)
        {
            if(data == '') $(msg_id).html('<div class="alert alert-primary" role="alert">Задача с таким id не найдена</div>');
            else issue = JSON.parse(data);
            console.log(issue);
        },
        error: function(xhr, status, error)
        {
            $(msg_id).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return issue;
}
</script>

<script>
function pages(params) //для пагинации
{
    $.ajax({
        async: false,
        type: 'POST',
        url: 'countissues.php',
        data: params,
        beforeSend: function()
        {
            document.getElementById("issues-pages").innerHTML = "";
        },
        success: function(data)
        {
            if(data != '')
            {
                var total = JSON.parse(data);
                var all = total[0]; var found = total[1];
                var pages = total[2]; var page = total[3];
                $('#issues-msg').html('<div class="alert alert-primary" role="alert">Всего: ' + all + ' задач, найдено: ' + found + ' задач, всего страниц: ' + pages + ', текущая страница: ' + page + '</div>');

                for (let i = 1; i <= pages; i++)
                {
                    if(i === page) document.getElementById("issues-pages").innerHTML += "<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">" + i + "</span></li>";
                    else document.getElementById("workers-pages").innerHTML += "<li onclick=\"search(this.id)\" class=\"page-item\" id=\"" + i + "\"><a class=\"page-link\" >" + i + "</a></li>";
                }
            }
            else $('#issues-msg').html('<div class="alert alert-primary" role="alert">Нет задач</div>');
        },
        error: function(xhr, status, error)
        {
            $('#issues-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return;
}
</script>

<script>
function search(page) //поиск задач по параметрам
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
        url: 'getissues.php',
        data: params,
        beforeSend: function()
        {
            document.getElementById("issues-table").innerHTML = "";
        },
        success: function(data)
        {
            if(data == '') $('#issues-msg').html('<div class="alert alert-primary" role="alert">Нет задач</div>');
            else
            {
                // display table
                var issues = JSON.parse(data);
                console.log(issues);
                var table = "<table class=\"table table-hover\">" +
                "<thead class=\"thead-primary\"><tr class=\"table-primary\">" +
                "<th scope=\"col\">#</th>" +
                "<th scope=\"col\">Имя</th>" +
                "<th scope=\"col\">Должность</th>" +
                "<th scope=\"col\">Дата</th>" +
                "<th scope=\"col\">Статус</th>" +
                "<th scope=\"col\">Место</th>" +
                "<th scope=\"col\">Причина</th>" +
                "<th scope=\"col\">Важно</th>" +
                "<th scope=\"col\">Редактировать</th>" +
                "<th scope=\"col\">Копировать</th>" +
                "<th scope=\"col\">Удалить</th>" +
                // copy?
                "</tr></thead><tbody>";

                for(let i = 0; i < issues.length; i++)
                {
                    var editbutton = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-issue-modal\" data-whatever=\"" + issues[i][0] + "\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></button></td>";

                    var copybutton = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"copy-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#copy-issue-modal\" data-whatever=\"" + issues[i][0] + "\"><img class=\"img-responsive\" title=\"edit\" src=\"img/copy.svg\"/></button></td>";

                    var delbutton  = "<td><button type=\"button\" class=\"btn btn-primary\" id=\"del-issue-btn\" data-bs-toggle=\"modal\" data-bs-target=\"#del-issue-modal\" data-whatever=\"" + issues[i][0] + "\"><img class=\"img-responsive\" title=\"delete\" src=\"img/delete.svg\"/></button></td>";

                    var urgent = (issues[i][9] == 'f') ? "<td></td>" : "<td><button type=\"button\" class=\"btn\" disabled \"><img class=\"img-responsive\" title=\"delete\" src=\"img/alert.svg\"/></button></td>";

                    table += "<tr><th scope=\"row\">" + issues[i][0] + "</th>" +
                    "<td>" + issues[i][10] + "</td>" + //fullname
                    "<td>" + issues[i][11] + "</td>" + //position
                    "<td>" + issues[i][5] + "</td>" + //date
                    "<td>" + issues[i][2] + "</td>" + //status
                    "<td>" + issues[i][6].substring(0, 10) + " ...</td>" + //place
                    "<td>" + issues[i][7].substring(0, 10) + " ...</td>" + //issue
                    urgent + editbutton + copybutton + delbutton + "</tr>";
                }
                table += "</tbody></table><br><br>";
                document.getElementById("issues-table").innerHTML = table;
            }
        },
        error: function(xhr, status, error)
        {
            $('#issues-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return;
}
</script>

<script>
$(document).ready(function() //загружает список задач как только страница загрузится
{
    search(1);
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму редактирования/просмотра вызова работника. нельзя сменить работника и должность
{
    $('#edit-issue-modal').on('show.bs.modal', function(e)
    {
        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-issue-id"]').val(issue_id);

        $('#edit-issue-submit-btn').attr("disabled","disabled");
        $('#edit-issue-modal-body').css('opacity', '.5');
        $("#edit-issue-status").empty();

        var dates = getdate();
        var statuses = getstatuses('#edit-issue-msg');
        var issue = getissue(issue_id, '#edit-issue-msg');

        if(statuses != '' && issue != '')
        {
            var issue_status = issue[2];

            $(e.currentTarget).find('input[id="edit-issue-name"]').val(issue[10] + ' ' + issue[11]);
            $(e.currentTarget).find('textarea[id="edit-issue-place"]').val(issue[6]);
            $(e.currentTarget).find('textarea[id="edit-issue-text"]').val(issue[7]);
            $(e.currentTarget).find('textarea[id="edit-issue-notes"]').val(issue[8]);

            const call_time = document.querySelector('input[id="edit-issue-time"]');
            call_time.value = issue[5]; call_time.min = dates[0]; call_time.max = dates[1];

            (issue[9] == 't') ? $("#edit-issue-urgent").prop("checked", true) : $("#edit-issue-urgent").prop("checked", false);

            for(s in statuses)
            {
                if(issue_status == statuses[s])
                $("#edit-issue-status").append("<option value=" + statuses[s] + " selected>" + statuses[s] + "</option>");
                else $("#edit-issue-status").append("<option value=" + statuses[s] + ">" + statuses[s] + "</option>");
            }

            if(issue_status == 'Завершено' || issue_status == 'Отказ')
            {
                document.getElementById("edit-issue-place").readOnly = true;
                document.getElementById("edit-issue-text").readOnly = true;
                document.getElementById("edit-issue-time").readOnly = true;
                document.getElementById("edit-issue-urgent").disabled = true;
            }
            else
            {
                document.getElementById("edit-issue-place").readOnly = false;
                document.getElementById("edit-issue-text").readOnly = false;
                document.getElementById("edit-issue-time").readOnly = false;
                document.getElementById("edit-issue-urgent").disabled = false;
            }
        }
        $('#edit-issue-submit-btn').removeAttr("disabled");
        $('#edit-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму копирования вызова. нельзя сменить работника и должность
{
    $('#copy-issue-modal').on('show.bs.modal', function(e)
    {
        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="copy-issue-id"]').val(issue_id);

        $('#copy-issue-submit-btn').attr("disabled","disabled");
        $('#copy-issue-modal-body').css('opacity', '.5');
        $("#copy-issue-status").empty();

        var dates = getdate();
        var statuses = getstatuses('#copy-issue-msg');
        var issue = getissue(issue_id, '#copy-issue-msg');

        if(statuses != '' && issue != '')
        {
            $(e.currentTarget).find('input[id="copy-worker-id"]').val(issue[1]);
            var issue_status = issue[2];
            $(e.currentTarget).find('input[id="copy-issue-name"]').val(issue[10] + ' ' + issue[11]);
            $(e.currentTarget).find('textarea[id="copy-issue-place"]').val(issue[6]);
            $(e.currentTarget).find('textarea[id="copy-issue-text"]').val(issue[7]);
            $(e.currentTarget).find('textarea[id="copy-issue-notes"]').val(issue[8]);

            const call_time = document.querySelector('input[id="copy-issue-time"]');
            call_time.value = issue[5]; call_time.min = dates[0]; call_time.max = dates[1];

            (issue[9] == 't') ? $("#copy-issue-urgent").prop("checked", true) : $("#copy-issue-urgent").prop("checked", false);

            for(s in statuses)
            {
                if(issue_status == statuses[s])
                $("#copy-issue-status").append("<option value=" + statuses[s] + " selected>" + statuses[s] + "</option>");
                else $("#copy-issue-status").append("<option value=" + statuses[s] + ">" + statuses[s] + "</option>");
            }

            if(issue_status == 'Завершено' || issue_status == 'Отказ')
            {
                document.getElementById("copy-issue-place").readOnly = true;
                document.getElementById("copy-issue-text").readOnly = true;
                document.getElementById("copy-issue-time").readOnly = true;
                document.getElementById("copy-issue-urgent").disabled = true;
            }
            else
            {
                document.getElementById("copy-issue-place").readOnly = false;
                document.getElementById("copy-issue-text").readOnly = false;
                document.getElementById("copy-issue-time").readOnly = false;
                document.getElementById("copy-issue-urgent").disabled = false;
            }
        }
        $('#copy-issue-submit-btn').removeAttr("disabled");
        $('#copy-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#edit-issue-submit-btn').click(function(e) //подтвердить редактирование вызова
{
    const issue_id = typeof $("#edit-issue-id").val() === 'string' ? $("#edit-issue-id").val().trim() : '';

    var issue_status = $("#edit-issue-status option:selected").text();

    const issue_date = document.querySelector('input[id="edit-issue-time"]').value;
    const place = typeof $("#edit-issue-place").val() === 'string' ? $("#edit-issue-place").val().trim() : '';
    const issue = typeof $("#edit-issue-text").val() === 'string' ? $("#edit-issue-text").val().trim() : '';
    const notes = typeof $("#edit-issue-notes").val() === 'string' ? $("#edit-issue-notes").val().trim() : '';
    var urgent = 0;
    if(document.getElementById('edit-issue-urgent').checked) urgent = 1;

    if(issue_status == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет статуса вызова</div>');
        return false;
    }

    if(issue_id == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID вызова</div>');
        return false;
    }
    if(issue_date == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату</div>');
        return false;
    }
    if(place == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите место вызова</div>');
        return false;
    }
    if(issue == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите причину вызова</div>');
        return false;
    }

    var params = 'id=' + issue_id + '&t=' + encodeURIComponent(issue_date) + '&p=' + encodeURIComponent(place) + '&i=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(issue_status);
    if(notes != '') params += '&n=' + notes;
    if (urgent) params += '&u=' + urgent;

    $.ajax ({
        type: 'POST',
        url: 'updissue.php',
        data: params,
        beforeSend: function()
        {
            $('#edit-issue-submit-btn').attr('disabled","disabled');
            $('#edit-issue-modal-body').css('opacity', '.5');
            $('#edit-issue-msg').html('');
        },
        success: function(data)
        {
            $('#edit-issue-submit-btn').removeAttr('disabled');
            $('#edit-issue-modal-body').css('opacity', '');
            if(data != '') $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">' + data + '</div>');
            else
            {
                search(1);
                $('#edit-issue-modal').modal('hide');
            }
        },
        error: function(xhr, status, error)
        {
            $('#edit-issue-submit-btn').removeAttr('disabled');
            $('#edit-issue-modal-body').css('opacity', '');
            $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText + '</div>');
        }
    });
});
</script>

<script>
$('#copy-issue-submit-btn').click(function(e) //подтвердить копирование вызова
{
    const issue_time = document.querySelector('input[id="copy-issue-time"]').value;
    const issue_id = typeof $("#copy-issue-id").val() === 'string' ? $("#copy-issue-id").val().trim() : '';
    const worker_id = typeof $("#copy-worker-id").val() === 'string' ? $("#copy-worker-id").val().trim() : '';
    const place = typeof $("#copy-issue-place").val() === 'string' ? $("#copy-issue-place").val().trim() : '';
    const issue = typeof $("#copy-issue-text").val() === 'string' ? $("#copy-issue-text").val().trim() : '';
    const notes = typeof $("#copy-issue-notes").val() === 'string' ? $("#copy-issue-notes").val().trim() : '';
    var status = $("#copy-issue-status option:selected").text();
    var urgent = 0;
    if(document.getElementById('copy-issue-urgent').checked) urgent = 1;

    if(issue_time == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату</div>');
        return false;
    }
    if(status == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Нет статуса задачи</div>');
        return false;
    }
    if(worker_id == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(place == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите место вызова</div>');
        return false;
    }
    if(issue == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите причину вызова</div>');
        return false;
    }

    var params = 'id=' + worker_id + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&i=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status);
    if(notes != '') params += '$n=' + notes;
    if(urgent) params += '&u=' + urgent;

    $.ajax ({
        type: 'POST',
        url: 'setcall.php',
        data: params,
        beforeSend: function()
        {
            $('#copy-issue-submit-btn').attr("disabled","disabled");
            $('#copy-issue-modal-body').css('opacity', '.5');
            $("#copy-issue-msg").html("");
        },
        success: function(data)
        {
            $('#copy-issue-btn').removeAttr("disabled");
            $('#copy-issue-body').css('opacity', '');
            if(data != '') $("#copy-issue-msg").html('<div class="alert alert-primary" role="alert">' + data +'</div>');
            search(1);
        },
        error: function(xhr, status, error)
        {
            $('#copy-issue-submit-btn').removeAttr("disabled");
            $('#copy-issue-modal-body').css('opacity', '');
            $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$("#add-issue-btn").click(function() //при нажатии на кнопку "создать задачу"
{
    var new_issue_modal = new bootstrap.Modal(document.getElementById('new-issue-modal'));
    new_issue_modal.show();

    var curr_date = getdate();
    const issue_time = document.querySelector('input[id="new-issue-time"]');
    issue_time.value = curr_date[0]; issue_time.min = curr_date[0]; issue_time.max = curr_date[1];

    $.ajax ({
        type: 'POST',
        url: 'getallworkers.php',
        beforeSend: function()
        {
            $('#new-issue-submit-btn').attr("disabled","disabled");
            $('#new-issue-modal-body').css('opacity', '.5');
            $("#new-issue-workers-select").empty();
        },
        success: function(data)
        {
            $('#new-issue-modal-body').css('opacity', '');
            if(data == '') $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Нет работников</div>');
            else
            {
                var workers = JSON.parse(data);
                console.log(workers);
                for(w in workers)
                    $("#new-issue-workers-select").append("<option value=" + workers[w][0] + ">" + workers[w][1] + ", " +  workers[w][2] + "</option>");

                $('#new-issue-submit-btn').removeAttr("disabled");
            }
        },
        error: function(xhr, status, error)
        {
            $('#new-issue-modal-body').css('opacity', '');
            $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$('#new-issue-submit-btn').click(function(e) //подтвердить новую задачу
{
    const call_time = document.querySelector('input[id="new-issue-time"]').value;
    var worker_id = $("#new-issue-workers-select").val();

    //console.log(call_time + " " + worker_id);
    const place = typeof $("#new-issue-place").val() === 'string' ? $("#new-issue-place").val().trim() : '';
    const issue = typeof $("#new-issue-text").val() === 'string' ? $("#new-issue-text").val().trim() : '';
    var urgent = 0;
    if(document.getElementById('new-issue-urgent').checked) urgent = 1;

    if(call_time == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату</div>');
        return false;
    }
    if(worker_id == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(place == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите место вызова</div>');
        return false;
    }
    if(issue == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Напишите причину вызова</div>');
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
            $('#new-issue-submit-btn').attr("disabled","disabled");
            $('#new-issue-modal-body').css('opacity', '.5');
        },
        success: function(data)
        {
            if(data != '') $("#new-issue-msg").html('<div class="alert alert-primary" role="alert">' + data +'</div>');
            $('#new-issue-submit-btn').removeAttr("disabled");
            $('#new-issue-modal-body').css('opacity', '');
            search(1);
        },
        error: function(xhr, status, error)
        {
            $('#new-issue-submit-btn').removeAttr("disabled");
            $('#new-issue-modal-body').css('opacity', '');
            $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму копирования задачи
{
    $('#copy-issue-modal').on('show.bs.modal', function(e)
    {
        var issue_id = $(e.relatedTarget).data('whatever');
        console.log(issue_id);

        var dates = getdate();
        var statuses = getstatuses('#copy-issue-msg');
        var issue = getissue(issue_id, '#copy-issue-msg');

        if(statuses != '' && issue != '')
        {
            var issue_status = issue[2];

            $(e.currentTarget).find('input[id="copy-issue-name"]').val(issue[10] + ' ' + issue[11]);
            $(e.currentTarget).find('textarea[id="copy-issue-place"]').val(issue[6]);
            $(e.currentTarget).find('textarea[id="copy-issue-text"]').val(issue[7]);
            $(e.currentTarget).find('textarea[id="copy-issue-notes"]').val(issue[8]);

            const call_time = document.querySelector('input[id="copy-issue-time"]');
            call_time.value = issue[5]; call_time.min = dates[0]; call_time.max = dates[1];

            (issue[9] == 't') ? $("#copy-issue-urgent").prop("checked", true) : $("#copy-issue-urgent").prop("checked", false);

            for(s in statuses)
            {
                if(issue_status == statuses[s])
                $("#copy-issue-status").append("<option value=" + statuses[s] + " selected>" + statuses[s] + "</option>");
                else $("#copy-issue-status").append("<option value=" + statuses[s] + ">" + statuses[s] + "</option>");
            }

            if(issue_status == 'Завершено' || issue_status == 'Отказ')
            {
                document.getElementById("copy-issue-place").readOnly = true;
                document.getElementById("copy-issue-text").readOnly = true;
                document.getElementById("copy-issue-time").readOnly = true;
                document.getElementById("copy-issue-urgent").disabled = true;
            }
            else
            {
                document.getElementById("copy-issue-place").readOnly = false;
                document.getElementById("copy-issue-text").readOnly = false;
                document.getElementById("copy-issue-time").readOnly = false;
                document.getElementById("copy-issue-urgent").disabled = false;
            }
        }
        $('#copy-issue-submit-btn').removeAttr("disabled");
        $('#copy-issue-modal-body').css('opacity', '');

    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму удаления задачи
{
    $('#del-issue-modal').on('show.bs.modal', function(e)
    {
        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="del-issue-id"]').val(issue_id);
        console.log(issue_id);
        $('#del-issue-submit-btn').attr("disabled","disabled");
        $('#del-issue-modal-body').css('opacity', '.5');

        var issue = getissue(issue_id, '#del-issue-msg');
        if(issue != '')
        {
            $(e.currentTarget).find('textarea[id="del-issue-text"]').val(issue[10] + ' ' + issue[11] + issue[6] + ': ' + issue[7]);
            $('#del-issue-submit-btn').removeAttr("disabled");
            $('#del-issue-modal-body').css('opacity', '');
        }
    });
});
</script>

<script>
$('#del-issue-submit-btn').click(function(e) //подтвердить удаление задачи
{
    const issue_id = typeof $("#del-issue-id").val() === 'string' ? $("#del-issue-id").val().trim() : '';

    $.ajax ({
        type: 'POST',
        url: 'delissue.php',
        data: 'id=' +issue_id,
        beforeSend: function()
        {
            $('#del-issue-submit-btn').attr("disabled","disabled");
            $('#del-issue-modal-body').css('opacity', '.5');
            $("#del-issue-msg").html("");
        },
        success: function(data)
        {
            if(data == '')
            {
                $('#del-issue-modal').modal('hide');
                search(1);
            }
            else $('#del-issue-msg').html('<div class="alert alert-primary" role="alert">' + data +'</div>');
        },
        error: function(xhr, status, error)
        {
            $("#del-issue-msg").html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>
</html>