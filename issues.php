<!--- workers page --->
<!doctype html>
<html lang="en">
    <head>
        <?php include './header.html';

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
                        <li class="nav-item active">
                            <a class="nav-link" href="/workers.php">Персонал</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="/positions.php">Должности</a>
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
            <div class="btn-toolbar mb-3" role="toolbar" aria-label="">
                <div class="btn-group me-2" role="group" aria-label="">
                    <select class="form-select form-select-sm" aria-label="Быстрый поиск по должности" onchange="issues1(1)" id="select-search-status">
                    </select>
                    <select class="form-control"  style="width:auto;" id="select-per-page" name="perpage" onchange="issues1(1)">
                        <option selected value=15>15</option>
                        <option value=30>30</option>
                        <option value=50>50</option>
                        <option value=100>100</option>
                    </select>
                </div> <!--- btn-group --->
                <div class="input-group">
                    <input type="text" class="form-control" id="search-worker" onchange="issues1(1)" placeholder="Поиск по Ф.И.О." aria-label="" aria-describedby="btnGroupAddon">
                    <button type="button" class="btn btn-primary" id="new-issue-btn">Создать задачу</button>
                </div>
            </div> <!--- btn-toolbar -->
            <p class="issues-msg" id="issues-msg"></p>

            <nav aria-label="...">
                <ul class="pagination pagination-sm justify-content-center" id="issues-pages"></ul>
            </nav>

            <div id="issues-table"></div>
        </div><!--- container --->

        <?php include './footer.php' ?>

        <!--- new-issue-modal --->
        <div class="modal fade" id="new-issue-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Создать новую задачу</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="new-issue-modal-body">
                    <!--- контент окна ниже --->
                        <div class="form-group">
                            <label for="new-issue-worker-select">Выберите работника</label>
                            <select class="form-control" onchange="changeposition('#new-issue-worker-select', '#new-issue-position-select', '#new-issue-msg')" id="new-issue-worker-select" name="workers"></select>
                            <br>
                            <label for="new-issue-position-select">Выберите должность работника</label>
                            <select class="form-control" id="new-issue-position-select" name="positions"></select>
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
                        <button type="button" class="btn btn-default" id="del-issue-cancel-btn" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="del-issue-submit-btn">Удалить</button>
                    </div>
                    <p class="del-issue-msg" id="del-issue-msg"></p>
                </div>
            </div>
        </div>

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
                                <label for="edit-issue-worker-select">Выберите работника</label>
                                <select class="form-control" onchange="changeposition('#edit-issue-worker-select', '#edit-issue-position-select', '#edit-issue-msg', '')" id="edit-issue-worker-select" name="workers"></select>
                                <br>
                                <label for="edit-issue-position-select">Выберите должность работника</label>
                                <select class="form-control" id="edit-issue-position-select" name="positions"></select>
                                <br>
                                <label for="edit-issue-status">Статус вызова</label>
                                <select class="form-control" id="edit-issue-status" onchange="changestatus()" name="statuses">
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
                    <!--- контент окна выше --->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="edit-issue-cancel-btn" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-issue-del-btn">Удалить</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-issue-submit-btn">Сохранить</button>
                    </div>
                    <p class="edit-issue-msg" id="edit-issue-msg"></p>
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
                            <label for="copy-issue-worker-select">Выберите работника</label>
                            <select class="form-control" onchange="changeposition('#copy-issue-worker-select', '#copy-issue-position-select', '#copy-issue-msg', '')" id="copy-issue-worker-select" name="workers"></select>
                            <br>
                            <label for="copy-issue-position-select">Выберите должность работника</label>
                            <select class="form-control" id="copy-issue-position-select" name="positions"></select>
                            <br>
                            <label for="copy-issue-status">Статус вызова</label>
                            <select class="form-control" id="copy-issue-status" onchange="changestatus()" name="statuses">
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
$(document).ready(function() // загружает список работников как только страница загрузится
{
    var statuses = getstatuses('', '#issues-msg');
    if(statuses != '')
    {
        //append 'all' first
        $('#select-search-status').append('<option value=Все статусы>Все статусы</option>');
        for(s in statuses) $('#select-search-status').append('<option value=' + statuses[s] + '>' + statuses[s] + '</option>');
    }
    else
    {
        $('#select-search-status').prop('disabled', 'disabled');
        $('#new-issue-btn').prop('disabled', 'disabled');
        return;
    }
    issues1(1);
});
</script>

<script>
function getstatuses(params, msg) // возвращает статусы
{
    var stauses = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getstatuses.php',
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if (responce != '') statuses = JSON.parse(responce);
            else $(msg).html('<div class="alert alert-primary" role="alert">Нет статусов в справочнике</div>');
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return statuses;
}
</script>

<script>
function getpages(params, msg) // получение страниц [всего, найдено, страниц, текущая страница]
{
    var pages = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'countissues.php', // ничего не пишет, только возвращает количество в массиве
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if(responce != '') pages = JSON.parse(responce);
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return pages;
}
</script>

<script>
function getissues(params, msg) // ищет задачи по параметрам
{
    issues = '';
    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getissues.php',
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if (responce != '') issues = JSON.parse(responce);
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return issues;
}
</script>

<script>
function issues1(page)
{
    var n = document.getElementById('search-worker').value;
    var q = $('#select-per-page').val();
    var s = $('#select-search-status option:selected').text();

    if(!Number.isInteger(page)) page = parseInt(page);
    var params = 'p=' + (page - 1) + '&q=' + q;
    if(n != '') params += '&n=' + n;//name
    if(s != '') params += '&s=' + s;//status
    console.log('getissues params: ' + params);

    var pages = getpages(params, '#issues-msg');
    var issues = getissues(params, '#issues-msg');

    if(pages == '') return;
    document.getElementById('issues-pages').innerHTML = '';
    document.getElementById('issues-table').innerHTML = '';

    var allissues = pages[0];
    if(allissues == 0)
    {
        $('#issues-msg').html('<div class="alert alert-primary" role="alert">Нет задач в справочнике</div>');
        return;
    }
    var foundissues = pages[1];
    var totalpages = pages[2]; var currentpage = pages[3];
    $('#issues-msg').html('<div class="alert alert-primary" role="alert">Всего: ' + allissues + ' задач, отобрано: ' + foundissues + ' задач, всего страниц: ' + totalpages + ', текущая страница: ' + currentpage + '</div>');

    for (let i = 1; i <= totalpages; i++)
    {
        if(i === currentpage) document.getElementById('issues-pages').innerHTML += '<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">' + i + '</span></li>';
        else document.getElementById('issues-pages').innerHTML += '<li onclick=\"issues(this.id)\" class=\"page-item\" id=\"' + i + '\"><a class=\"page-link\" >' + i + '</a></li>';
    }

    if(issues == '') return;
    var table = '<table class=\"table table-hover\">' +
                '<thead class=\"thead-primary\"><tr class=\"table-primary\">' +
                '<td scope=\"col\">Имя</th>' +
                '<td scope=\"col\">Должность</th>' +
                '<td scope=\"col\">Дата</th>' +
                '<td scope=\"col\">Статус</th>' +
                '<td scope=\"col\">Место</th>' +
                '<td scope=\"col\">Причина</th>' +
                '<td scope=\"col\">Важно</th>' +
                '<td scope=\"col\">Редактировать</th>' +
                '<td scope=\"col\">Копировать</th>' +
                '<td scope=\"col\">Удалить</th>' +
                '</tr></thead><tbody>';

    for(let i = 0; i < issues.length; i++)
    {
        var editbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-issue-modal\" data-whatever=\"' + issues[i][0] + '\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></td>';

        var copybutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"copy-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#copy-issue-modal\" data-whatever=\"' + issues[i][0] + '\"><img class=\"img-responsive\" title=\"edit\" src=\"img/copy.svg\"/></button></td>';

        var delbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"del-issue-btn\" data-bs-toggle=\"modal\" data-bs-target=\"#del-issue-modal\" data-whatever=\"' + issues[i][0] + '\"><img class=\"img-responsive\" title=\"delete\" src=\"img/delete.svg\"/></td>';

        var urgent = (issues[i][10] == 'f') ? '<td></td>' : '<td><button type=\"button\" class=\"btn\" disabled \"><img class=\"img-responsive\" title=\"delete\" src=\"img/alert.svg\" tite=\"urgent\"/></button></td>';

        table += '<tr><th scope=\"row\">' + issues[i][11] + '</th>' + //fullname
        '<td>' + issues[i][3] + '</td>' + //position
        '<td>' + issues[i][6] + '</td>' + //date
        '<td>' + issues[i][2] + '</td>' + //status
        '<td>' + issues[i][7].substring(0, 10) + ' ...</td>' + //place
        '<td>' + issues[i][8].substring(0, 10) + ' ...</td>' + //issue
        urgent + editbutton + copybutton + delbutton + '</tr>';
    }
    table += '</tbody></table><br><br>';
    document.getElementById('issues-table').innerHTML = table;
}
</script>

<script>
function getallworkers(select, msg) // возвращает список всех работников []
{
    var workers = '';
    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getallworkers.php',
        beforeSend: function()
        {
            $(msg).html('');
            $(select).empty();
        },
        success: function(responce)
        {
            if(responce != '') workers = JSON.parse(responce);
            else $(msg).html('<div class="alert alert-primary" role="alert">Нет работников в справочнике</div>');
        },
        error: function(xhr, status, error)
        {
            $(select).prop('disabled', 'disabled');
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return workers;
}
</script>

<script>
function getworker(worker_id, msg) //возвращает информацию об одном рабочем
{
    var worker;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getworker.php', //возвращает значение, только при успехе
        data: 'i=' + worker_id,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if(responce == '') $(msg).html('<div class="alert alert-primary" role="alert">Работник с таким id не найден</div>');
            else worker = JSON.parse(responce);
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return worker;
}
</script>

<script>
function changeposition(select1, select2, msg, position) //при выборе работника заменяет должности
{
    $(select2).empty();
    //get selected id
    var worker_id = $(select1).val();
    var worker = getworker(worker_id, msg);
    if(worker == '') return;
    var positions = worker[1].split(', ');
    for(p in positions)
    {
        if(position != '' && positions[p] == position) $(select2).append('<option value=' + positions[p] + ' selected>' + positions[p] + '</option>');
        else $(select2).append('<option value=' + positions[p] + '>' + positions[p] + '</option>');
    }

    return;
}
</script>

<script>
function changestatus() // включает или отключает поля в зависимости от статуса edit-issue-status
{
    var status = $('#edit-issue-status option:selected').text();

    if(status == 'Завершено' || status == 'Отказ')
    {
        $('#edit-issue-position-select').attr('disabled', 'disabled');
        $('#edit-issue-worker-select').attr('disabled', 'disabled');
        $('#edit-issue-place').prop('readonly', true);
        $('#edit-issue-text').prop('readonly',true);
        //$('edit-issue-text').readOnly = true;
        $('#edit-issue-time').prop('readonly',true);
        $('#edit-issue-urgent').prop('readonly',true);
    }
    else
    {
        $('#edit-issue-position-select').removeAttr('disabled');
        $('#edit-issue-worker-select').removeAttr('disabled');

        $('#edit-issue-place').prop('readonly', false);
        $('#edit-issue-text').prop('readonly',false);
        $('#edit-issue-time').prop('readonly',false);
        $('#edit-issue-urgent').prop('readonly',false);
    }
}
</script>

<script>
function getissue(issue_id, msg) //получает информацию об одном вызове
{
    var issue = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getissue.php',
        data: 'i=' + issue_id,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if(responce == '') $(msg).html('<div class="alert alert-primary" role="alert">Задача с таким id не найдена</div>');
            else issue = JSON.parse(responce);
            //console.log(issue);
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return issue;
}
</script>

<script>
function delissue(issue_id, msg) //удаляет задачу
{
    var deleted = false;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'delissue.php',
        data: 'i=' + issue_id,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if(responce != '') $(msg).html('<div class="alert alert-primary" role="alert">' + responce + '</div>');
            else
            {
                $(msg).html('<div class="alert alert-primary" role="alert">Задача удалена</div>');
                deleted = true;
            }
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return deleted;
}
</script>

<script>
$('#new-issue-btn').click(function() //при нажатии на кнопку "создать задачу"
{
    var new_issue_modal = new bootstrap.Modal(document.getElementById('new-issue-modal'));
    new_issue_modal.show();

    $('#new-issue-submit-btn').attr('disabled','disabled');
    $('#new-issue-modal-body').css('opacity', '.5');

    var allworkers = getallworkers('#new-issue-worker-select', '#new-issue-msg');
    if(allworkers == '') return;
    for(w in allworkers) $('#new-issue-worker-select').append('<option value=' + allworkers[w][0] + '>' + allworkers[w][1] + '</option>');

    changeposition('#new-issue-worker-select', '#new-issue-position-select', '#new-issue-msg', '');

    var curr_date = getdate();
    const issue_time = document.querySelector('input[id="new-issue-time"]');
    issue_time.value = curr_date[0]; issue_time.min = curr_date[0]; issue_time.max = curr_date[1];

    $('#new-issue-submit-btn').removeAttr('disabled');
    $('#new-issue-modal-body').css('opacity', '');
});
</script>

<script>
$('#new-issue-submit-btn').click(function(e) //подтвердить вызов работника
{
    var worker_id = $('#new-issue-worker-select').val();
    var position = $('#new-issue-position-select option:selected').text();
    var issue_time = document.querySelector('input[id="new-issue-time"]').value;
    const place = typeof $('#new-issue-place').val() === 'string' ? $('#new-issue-place').val().trim() : '';
    const issue = typeof $('#new-issue-text').val() === 'string' ? $('#new-issue-text').val().trim() : '';
    var urgent = ($('new-issue-urgent').checked) ? 1 : 0;

    if(worker_id == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(position == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Нет должности работника</div>');
        return false;   
    }
    if(issue_time == '')
    {
        $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату вызова</div>');
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
    var params = 'id=' + worker_id + '&o=' + position + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&i=' + encodeURIComponent(issue);
    if (urgent) params += '&u=' + urgent;
    console.log(params);

    $.ajax ({
        type: 'POST',
        url: 'newissue.php',
        data: params,
        beforeSend: function()
        {
            $('#new-issue-submit-btn').attr('disabled','disabled');
            $('#new-issue-modal-body').css('opacity', '.5');
            $('#new-issue-msg').html('');
        },
        success: function(responce)
        {
            $('#new-issue-submit-btn').removeAttr('disabled');
            $('#new-issue-modal-body').css('opacity', '');
            if(responce != '') $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            else $('#new-issue-modal').modal('hide');

            issues1(1);
        },
        error: function(xhr, status, error)
        {
            $('#new-issue-submit-btn').removeAttr('disabled');
            $('#new-issue-modal-body').css('opacity', '');
            $('#new-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
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
        $('#del-issue-submit-btn').attr('disabled','disabled');
        $('#del-issue-modal-body').css('opacity', '.5');

        var issue = getissue(issue_id, '#del-issue-msg');
        if(issue != '')
        {
            $(e.currentTarget).find('textarea[id="del-issue-text"]').val(issue[11] + ', ' + issue[3] + ': ' + issue[8]);
            $('#del-issue-submit-btn').removeAttr('disabled');
            $('#del-issue-modal-body').css('opacity', '');
        }
    });
});
</script>

<script>
$('#del-issue-submit-btn').click(function(e) //подтвердить удаление задачи
{
    const issue_id = typeof $('#del-issue-id').val() === 'string' ? $('#del-issue-id').val().trim() : '';
    $('#del-issue-submit-btn').attr('disabled','disabled');
    $('#del-issue-modal-body').css('opacity', '.5');

    if(delissue(issue_id, '#del-issue-msg'))
    {
        document.querySelector('#del-issue-cancel-btn').innerText = 'Закрыть';
        $('#del-issue-modal-body').css('opacity', '');
        issues1(1);
    }
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму редактирования задачи
{
    $('#edit-issue-modal').on('show.bs.modal', function(e)
    {
        $('#edit-issue-submit-btn').attr('disabled','disabled');
        $('#edit-issue-del-btn').attr('disabled','disabled');
        $('#edit-issue-modal-body').css('opacity', '.5');
        $('#edit-issue-status').empty();

        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-issue-id"]').val(issue_id);

        var allworkers = getallworkers('#edit-issue-worker-select', '#edit-issue-msg');
        if(allworkers == '') return;
        var issue = getissue(issue_id, '#edit-issue-msg');
        if(issue == '') return;
        var statuses = getstatuses('', '#edit-issue-msg');
        if(statuses == '') return;
        
        /*console.log(issue);
        console.log(allworkers);*/

        for(w in allworkers)
        {
            if(allworkers[w][0] == issue[1]) // compare id's
            $('#edit-issue-worker-select').append('<option value=' + allworkers[w][0] + ' selected>' + allworkers[w][1] + '</option>');
            else $('#edit-issue-worker-select').append('<option value=' + allworkers[w][0] + '>' + allworkers[w][1] + '</option>');
        }
        changeposition('#edit-issue-worker-select', '#edit-issue-position-select', '#edit-issue-msg', issue[3]);

        (issue[10] == 't') ? $('#edit-issue-urgent').prop('checked', true) : $('#edit-issue-urgent').prop('checked', false);

        status = issue[2];

        for(s in statuses)
        {
            if(status == statuses[s])
            $('#edit-issue-status').append('<option value=' + statuses[s] + ' selected>' + statuses[s] + '</option>');
            else $('#edit-issue-status').append('<option value=' + statuses[s] + '>' + statuses[s] + '</option>');
        }

        var dates= getdate();

        const issue_time = document.querySelector('input[id="edit-issue-time"]');
        issue_time.value = issue[6]; issue_time.min = dates[0]; issue_time.max = dates[1];

        $(e.currentTarget).find('textarea[id="edit-issue-place"]').val(issue[7]);
        $(e.currentTarget).find('textarea[id="edit-issue-text"]').val(issue[8]);
        $(e.currentTarget).find('textarea[id="edit-issue-notes"]').val(issue[9]);

        changestatus();

        $('#edit-issue-submit-btn').removeAttr('disabled');
        $('#edit-issue-del-btn').removeAttr('disabled');
        $('#edit-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#edit-issue-submit-btn').click(function(e) //подтвердить редактирование вызова
{
    var issue_id = typeof $('#edit-issue-id').val() === 'string' ? $('#edit-issue-id').val().trim() : '';
    var worker_id = $('#edit-issue-worker-select option:selected').val();
    var position = $('#edit-issue-position-select option:selected').text();
    var status = $('#edit-issue-status option:selected').text();
    var issue_time = document.querySelector('input[id="edit-issue-time"]').value;
    var place = typeof $('#edit-issue-place').val() === 'string' ? $('#edit-issue-place').val().trim() : '';
    var issue = typeof $('#edit-issue-text').val() === 'string' ? $('#edit-issue-text').val().trim() : '';
    var notes = typeof $('#edit-issue-notes').val() === 'string' ? $('#edit-issue-notes').val().trim() : '';
    var urgent = 0;
    if(document.getElementById('edit-issue-urgent').checked) urgent = 1;

    if(issue_id == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID вызова</div>');
        return false;
    }
    if(worker_id == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(position == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет должности работника</div>');
        return false;
    }
    if(status == '')
    {
        $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">Нет статуса вызова</div>');
        return false;
    }
    if(issue_time == '')
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

    //console.log(issue_id + ' ' + worker_id + ' ' +  position + ' ' + status + ' ' + issue_time + ' ' + ' ' + place + ' ' + issue + ' ' + urgent);
    var params = 'i=' + issue_id + '&w=' +  worker_id + '&d=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&t=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status) + '&o=' + position;
    if(notes != '') params += '&n=' + notes;
    if (urgent) params += '&u=' + urgent;

    $.ajax ({
        type: 'POST',
        url: 'updissue.php',
        data: params,
        beforeSend: function()
        {
            $('#edit-issue-submit-btn').attr('disabled','disabled');
            $('#edit-issue-modal-body').css('opacity', '.5');
            $('#edit-issue-msg').html('');
        },
        success: function(responce)
        {
            $('#edit-issue-submit-btn').removeAttr('disabled');
            $('#edit-issue-modal-body').css('opacity', '');
            if(responce =='')
            {
                $('#edit-issue-modal').modal('hide');
                issues1(1);
            }
            else $('#edit-issue-msg').html('<div class="alert alert-primary" role="alert">' + data + '</div>');
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
$('#edit-issue-del-btn').click(function(e) //подтвердить удаление вызова
{
    var issue_id = typeof $('#edit-issue-id').val() === 'string' ? $('#edit-issue-id').val().trim() : '';

    document.querySelector('#edit-issue-cancel-btn').innerText = 'Закрыть';
    $('#edit-issue-submit-btn').attr('disabled','disabled');
    $('#edit-issue-modal-body').css('opacity', '.5');
    $('#edit-issue-msg').html('');

    if(delissue(issue_id, '#edit-issue-msg'))
    {
        $('#new-issue-modal').modal('hide');
        issues1(1);
    }
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму копирования задачи
{
    $('#copy-issue-modal').on('show.bs.modal', function(e)
    {
        $('#copy-issue-submit-btn').attr('disabled','disabled');
        $('#copy-issue-modal-body').css('opacity', '.5');
        $('#copy-issue-status').empty();

        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="copy-issue-id"]').val(issue_id);

        var allworkers = getallworkers('#copy-issue-worker-select', '#copy-issue-msg');
        if(allworkers == '') return;
        var issue = getissue(issue_id, '#copy-issue-msg');
        if(issue == '') return;
        var statuses = getstatuses('', '#copy-issue-msg');
        if(statuses == '') return;
        
        /*console.log(issue);
        console.log(allworkers);*/

        for(w in allworkers)
        {
            if(allworkers[w][0] == issue[1]) // compare id's
            $('#copy-issue-worker-select').append('<option value=' + allworkers[w][0] + ' selected>' + allworkers[w][1] + '</option>');
            else $('#copy-issue-worker-select').append('<option value=' + allworkers[w][0] + '>' + allworkers[w][1] + '</option>');
        }
        changeposition('#copy-issue-worker-select', '#copy-issue-position-select', '#copy-issue-msg', issue[3]);

        (issue[10] == 't') ? $('#copy-issue-urgent').prop('checked', true) : $('#copy-issue-urgent').prop('checked', false);

        status = issue[2];

        for(s in statuses)
        {
            if(status == statuses[s])
            $('#copy-issue-status').append('<option value=' + statuses[s] + ' selected>' + statuses[s] + '</option>');
            else $('#copy-issue-status').append('<option value=' + statuses[s] + '>' + statuses[s] + '</option>');
        }

        var dates= getdate();

        const issue_time = document.querySelector('input[id="copy-issue-time"]');
        issue_time.value = issue[6]; issue_time.min = dates[0]; issue_time.max = dates[1];

        $(e.currentTarget).find('textarea[id="copy-issue-place"]').val(issue[7]);
        $(e.currentTarget).find('textarea[id="copy-issue-text"]').val(issue[8]);
        $(e.currentTarget).find('textarea[id="copy-issue-notes"]').val(issue[9]);

        changestatus();

        $('#copy-issue-submit-btn').removeAttr('disabled');
        $('#copy-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#copy-issue-submit-btn').click(function(e) //подтвердить копирование вызова
{
    var worker_id = $('#copy-issue-worker-select option:selected').val();

    var position = $('#copy-issue-position-select option:selected').text();
    var status = $('#copy-issue-status option:selected').text();
    var issue_time = document.querySelector('input[id="copy-issue-time"]').value;
    var place = typeof $('#copy-issue-place').val() === 'string' ? $('#copy-issue-place').val().trim() : '';
    var issue = typeof $('#copy-issue-text').val() === 'string' ? $('#copy-issue-text').val().trim() : '';
    var notes = typeof $('#copy-issue-notes').val() === 'string' ? $('#copy-issue-notes').val().trim() : '';
    var urgent = 0;
    if(document.getElementById('copy-issue-urgent').checked) urgent = 1;

    if(worker_id == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(position == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Нет должности работника</div>');
        return false;   
    }
    if(issue_time == '')
    {
        $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">Назначьте дату вызова</div>');
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

    var params = 'id=' + worker_id + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&i=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status) + '&o=' + position;
    if(notes != '') params += '&n=' + notes;
    if (urgent) params += '&u=' + urgent;
    console.log(params);

    $.ajax ({
        type: 'POST',
        url: 'newissue.php',
        data: params,
        beforeSend: function()
        {
            $('#copy-issue-submit-btn').attr('disabled','disabled');
            $('#copy-issue-modal-body').css('opacity', '.5');
            $('#copy-issue-msg').html('');
        },
        success: function(responce)
        {
            $('#copy-issue-submit-btn').removeAttr('disabled');
            $('#copy-issue-modal-body').css('opacity', '');
            if(responce != '') $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            else $('#copy-issue-modal').modal('hide');

            issues1(1);
        },
        error: function(xhr, status, error)
        {
            $('#copy-issue-submit-btn').removeAttr('disabled');
            $('#copy-issue-modal-body').css('opacity', '');
            $('#copy-issue-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>
</html>