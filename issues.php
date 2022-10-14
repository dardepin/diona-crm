<!--- isuues page --->
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
    </body>

    <!--- modals --->
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
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary submitBtn" id="new-issue-submit-btn">Создать</button>
                </div>
                <p class="new-issue-msg" id="new-issue-msg"></p>
            </div>
        </div>
    </div>

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
                        <input type="hidden" class="form-control" id="del-issue-id" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-worker-id" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-position" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-status" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-date" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-place" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-text" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-notes" placeholder="" value = "" readonly />
                        <input type="hidden" class="form-control" id="del-issue-urgent" placeholder="" value = "" readonly />

                        <label for="del-issue-comment">Описание вызова</label>
                        <textarea class="form-control" id="del-issue-comment" rows="3" readonly></textarea>
                    </div>
                <!--- контент окна выше--->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="del-issue-cancel-btn" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary submitBtn" id="del-issue-submit-btn">Удалить</button>
                </div>
                <p class="del-issue-msg" id="del-issue-msg"></p>
            </div>
        </div>
    </div>

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
                            <label for="edit-issue-status-select">Статус вызова</label>
                            <select class="form-control" id="edit-issue-status-select" onchange="changestatus()" name="statuses"></select>
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
                    <button type="button" class="btn btn-default" id="edit-issue-cancel-btn" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary submitBtn" id="edit-issue-del-btn">Удалить</button>
                    <button type="button" class="btn btn-primary submitBtn" id="edit-issue-submit-btn">Сохранить</button>
                </div>
                <p class="edit-issue-msg" id="edit-issue-msg"></p>
            </div>
        </div>
    </div>

</html>

<script>
function msg(field, message)
{
    if(message == '') $(field).html('');
    else $(field).html('<div class="alert alert-primary" role="alert">' + message + '</div>');
    return;
}
</script>

<script>
function getstatuses(params, field) //возвращает статусы
{
    var stauses = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getstatuses.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if (responce != '') statuses = JSON.parse(responce);
            else msg(field, 'Нет статусов в справочнике');
        },
        error: function(xhr, status, error)
        {
            msg(field,  xhr.status + ' ' + xhr.statusText);
        }
    });
    return statuses;
}
</script>

<script>
function getpages(params, field) //получение страниц [всего, найдено, страниц, текущая страница]
{
    var pages = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'countissues.php', // ничего не пишет, только возвращает количество в массиве
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if(responce != '') pages = JSON.parse(responce);
            else msg(field, 'Не удалось получить количество страниц/вызовов');
        },
        error: function(xhr, status, error)
        {
            msg(field, xhr.status + ' ' + xhr.statusText);
        }
    });
    return pages;
}
</script>

<script>
function issues1(page)
{
    var field = '#issues-msg';

    var n = $('#search-worker').val();
    var q = $('#select-per-page').val();
    var s = $('#select-search-status option:selected').text();

    if(!Number.isInteger(page)) page = parseInt(page);
    var params = 'g=' + (page - 1) + '&q=' + q;
    if(n != '') params += '&n=' + n;//name
    if(s != '') params += '&s=' + s;//status

    var pages = getpages(params, field);
    if(pages == '') return;
    var issues = getissues(params, field);

    $('#issues-pages').html('');
    $('#issues-table').html('');
    var allissues = pages[0];
    if(allissues == 0)
    {
        msg(field, 'Нет задач');
        return;
    }
    var foundissues = pages[1];
    var totalpages = pages[2]; var currentpage = pages[3];
    msg(field, 'Всего: ' + allissues + ' задач, отобрано: ' + foundissues + ' задач, всего страниц: ' + totalpages + ', текущая страница: ' + currentpage);

    for (let i = 1; i <= totalpages; i++)
    {
        if(i === currentpage) $('#issues-pages').append('<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">' + i + '</span></li>');
        else $('#issues-pages').append('<li onclick=\"issues(this.id)\" class=\"page-item\" id=\"' + i + '\"><a class=\"page-link\" >' + i + '</a></li>');
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
                '<td scope=\"col\">Удалить</th>' +
                '</tr></thead><tbody>';
    for(let i = 0; i < issues.length; i++)
    {
        var editbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-issue-modal\" data-whatever=\"' + issues[i][0] + '\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></td>';

        var delbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"del-issue-btn\" data-bs-toggle=\"modal\" data-bs-target=\"#del-issue-modal\" data-whatever=\"' + issues[i][0] + '\"><img class=\"img-responsive\" title=\"delete\" src=\"img/delete.svg\"/></td>';

        var urgent = (issues[i][10] == 'f') ? '<td></td>' : '<td><button type=\"button\" class=\"btn\" disabled \"><img class=\"img-responsive\" title=\"delete\" src=\"img/alert.svg\" tite=\"urgent\"/></button></td>';

        table += '<tr><th scope=\"row\">' + issues[i][11] + '</th>' + //fullname
        '<td>' + issues[i][3] + '</td>' + //position
        '<td>' + issues[i][6] + '</td>' + //date
        '<td>' + issues[i][2] + '</td>' + //status
        '<td>' + issues[i][7].substring(0, 10) + ' ...</td>' + //place
        '<td>' + issues[i][8].substring(0, 10) + ' ...</td>' + //issue
        urgent + editbutton + delbutton + '</tr>';
    }
    table += '</tbody></table><br><br>';
    $('#issues-table').html(table);
}
</script>

<script>
function getissues(params, field) // ищет задачи по параметрам
{
    issues = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getissues.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if (responce != '') issues = JSON.parse(responce);
            else msg(field, 'Нет задач');
        },
        error: function(xhr, status, error)
        {
            msg(field, xhr.status + ' ' + xhr.statusText);
        }
    });

    return issues;
}
</script>

<script>
function getworkers(params, field)
{
    var workers = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getworkers.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if (responce != '') workers = JSON.parse(responce);
            else msg(field, 'Нет работников в справочнике');
        },
        error: function(xhr, status, error)
        {
            msg(field,  xhr.status + ' ' + xhr.statusText);
        }
    });
    return workers;
}
</script>

<script>
function getworker(params, field) //получает информацию об одном работнике
{
    var worker = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getworker.php', //возвращает значение только при успехе
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if(responce == '') msg(field, 'Работник с таким id не найден');
            else worker = JSON.parse(responce);
        },
        error: function(xhr, status, error)
        {
            msg(field,  xhr.status + ' ' + xhr.statusText);
        }
    });
    return worker;
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
function changeposition(select1, select2, field, position) //при выборе работника заменяет должности
{
    $(select2).empty();
    var worker_id = $(select1).val();
    var worker = getworker('i=' + worker_id, field);
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
function newissue(params, field) //создает задачу
{
    var created = false;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'newissue.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if(responce != '') msg(field,  responce);
            else
            {
                msg(field, 'Задача создана!');
                created = true;
            }
        },
        error: function(xhr, status, error)
        {
            msg(field, xhr.status + ' ' + xhr.statusText);
        }
    });
    return created;
}
</script>

<script>
function getissue(params, field) //получает информацию об одном вызове
{
    var issue = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getissue.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if(responce != '') issue = JSON.parse(responce);
            else msg(field, 'Задача с таким id не найдена');
        },
        error: function(xhr, status, error)
        {
            msg(field,  xhr.status + ' ' + xhr.statusText);
        }
    });
    return issue;
}
</script>

<script>
function changestatus() //включает или отключает поля в зависимости от статуса edit-issue-status
{
    var change = false;
    var status = $('#edit-issue-status-select option:selected').text();

    if(status == 'Завершено' || status == 'Отказ')
        change = true;
    $('#edit-issue-position-select').prop('disabled', change);
    $('#edit-issue-worker-select').prop('disabled', change);
    $('#edit-issue-place').prop('readonly', change);
    $('#edit-issue-text').prop('readonly',change);
    $('#edit-issue-time').prop('readonly',change);
    $('#edit-issue-urgent').prop('disabled', change);
}
</script>

<script>
function updissue(params, field) //обновляет задачу
{
    var updated = false;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'updissue.php',
        data: params,
        beforeSend: function()
        {
            msg(field, '');
        },
        success: function(responce)
        {
            if(responce == '') updated = true;
            else msg(field, responce);
        },
        error: function(xhr, status, error)
        {
            msg(field,  xhr.status + ' ' + xhr.statusText);
        }
    });
    return updated;
}
</script>

<script>
$(document).ready(function() //загружает список работников как только страница загрузится
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
$('#new-issue-btn').click(function() //при нажатии на кнопку "создать задачу"
{
    var field = '#new-issue-msg';

    var new_issue_modal = new bootstrap.Modal($('#new-issue-modal'));
    new_issue_modal.show();

    $('#new-issue-worker-select').html('');
    $('#new-issue-position-select').html('');

    $('#new-issue-text').val('');
    $('#new-issue-place').val('');
    $('#new-issue-cancel-btn').html('Отмена');

    $('#new-issue-submit-btn').prop('disabled', true);
    $('#new-issue-modal-body').css('opacity', '.5');

    var workers = getworkers('', field); // no fired and deleted
    if(workers == '') return;
    for(w in workers)
    {
        if(workers[w][5] == 't' || workers[w][6] == 't') continue; //skip fired and deleted
        $('#new-issue-worker-select').append('<option value=' + workers[w][0] + '>' + workers[w][1] + '</option>');
    }

    changeposition('#new-issue-worker-select', '#new-issue-position-select', field, '');

    var curr_date = getdate();
    const issue_time = document.querySelector('input[id="new-issue-time"]');
    issue_time.value = curr_date[0]; issue_time.min = curr_date[0]; issue_time.max = curr_date[1];

    $('#new-issue-submit-btn').prop('disabled', false);
    $('#new-issue-modal-body').css('opacity', '');
});
</script>

<script>
$('#new-issue-submit-btn').click(function(e) //подтвердить новую задачу
{
    var field = '#new-issue-msg';

    var worker_id = $('#new-issue-worker-select').val();
    var position = $('#new-issue-position-select option:selected').text();
    var issue_time =$('#new-issue-time').val();
    var place = typeof $('#new-issue-place').val() === 'string' ? $('#new-issue-place').val().trim() : '';
    var issue = typeof $('#new-issue-text').val() === 'string' ? $('#new-issue-text').val().trim() : '';
    var urgent = ($('#new-issue-urgent')[0].checked) ? 1 : 0;

    if(worker_id == '')
    {
        msg(field, 'Нет ID работника');
        return false;
    }
    if(position == '')
    {
        msg(field, 'Нет должности работника');
        return false;   
    }
    if(issue_time == '')
    {
        msg(field, 'Назначьте дату вызова');
        return false;
    }
    if(place == '')
    {
        msg(field, 'Напишите место вызова');
        return false;
    }
    if(issue == '')
    {
        msg(field, 'Напишите причину вызова');
        return false;
    }

    var params = 'w=' + worker_id + '&o=' + encodeURIComponent(position) + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&x=' + encodeURIComponent(issue);
    if (urgent) params += '&u=' + urgent;
    console.log(params);

    $('#new-issue-submit-btn').prop('disabled', true);
    $('#new-issue-modal-body').css('opacity', '.5');

    if(newissue(params, field))
    {
        $('#new-issue-cancel-btn').html('Закрыть');
        issues1(1);
        //$('#new-issue-modal').modal('hide');
    }
    $('#new-issue-submit-btn').prop('disabled', false);
    $('#new-issue-modal-body').css('opacity', '');
});
</script>

<script>
$(document).ready(function() //при нажатии на кнопку "редактирование задачи"
{
    $('#edit-issue-modal').on('show.bs.modal', function(e)
    {
        var field = '#edit-issue-msg';

        $('#edit-issue-submit-btn').prop('disabled', true);
        $('#edit-issue-del-btn').prop('disabled', true);
        $('#edit-issue-modal-body').css('opacity', '.5');

        $('#edit-issue-position-select').empty();
        $('#edit-issue-worker-select').empty();
        $('#edit-issue-status-select').empty();

        $('#edit-issue-position-select').prop('disabled', true);
        $('#edit-issue-worker-select').prop('disabled', true);
        $('#edit-issue-status-select').prop('disabled', true);
        $('#edit-issue-place').prop('readonly', true);
        $('#edit-issue-text').prop('readonly',true);
        $('#edit-issue-time').prop('readonly',true);
        $('#edit-issue-notes').prop('readonly',true);
        $('#edit-issue-urgent').prop('disabled',true);

        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-issue-id"]').val(issue_id);

        var workers = getworkers('', field);
        if(workers == '') return;
        var issue = getissue('i=' + issue_id, field);
        if(issue == '') return;
        var statuses = getstatuses('', field);
        if(statuses == '') return;
        console.log(issue);

        for(w in workers)
        {
            if(workers[w][5] == 't' || workers[6] == 't')
            {
                if(workers[w][0] == issue[1]) $('#edit-issue-worker-select').append('<option value=' + workers[w][0] + ' selected>' + workers[w][1] + '</option>');
            }
            else if(workers[w][0] == issue[1]) $('#edit-issue-worker-select').append('<option value=' + workers[w][0] + ' selected>' + workers[w][1] + '</option>');
            else $('#edit-issue-worker-select').append('<option value=' + workers[w][0] + '>' + workers[w][1] + '</option>');
        }

        status = issue[2];
        for(s in statuses)
        {
            if(status == statuses[s])
            $('#edit-issue-status-select').append('<option value=' + statuses[s] + ' selected>' + statuses[s] + '</option>');
            else $('#edit-issue-status-select').append('<option value=' + statuses[s] + '>' + statuses[s] + '</option>');
        }

        changeposition('#edit-issue-worker-select', '#edit-issue-position-select', field, issue[3]);

        var dates= getdate();
        var issue_time = document.querySelector('input[id="edit-issue-time"]');
        issue_time.value = issue[6]; issue_time.min = dates[0]; issue_time.max = dates[1];

        $(e.currentTarget).find('textarea[id="edit-issue-place"]').val(issue[7]);
        $(e.currentTarget).find('textarea[id="edit-issue-text"]').val(issue[8]);
        $(e.currentTarget).find('textarea[id="edit-issue-notes"]').val(issue[9]);

        (issue[10] == 't') ? $('#edit-issue-urgent').prop('checked', true) : $('#edit-issue-urgent').prop('checked', false);

        if(issue[12] == 't' || issue[13] == 't')
        { msg(field, 'Работник был уволен или удален. Задача доступна только для удаления'); $('#edit-issue-del-btn').prop('disabled', false); return; }

        $('#edit-issue-position-select').prop('disabled',false);
        $('#edit-issue-worker-select').prop('disabled',false);
        $('#edit-issue-status-select').prop('disabled',false);
        $('#edit-issue-place').prop('readonly', false);
        $('#edit-issue-text').prop('readonly',false);
        $('#edit-issue-notes').prop('readonly',false);
        $('#edit-issue-time').prop('readonly',false);
        $('#edit-issue-urgent').prop('disabled',false);

        changestatus();

        $('#edit-issue-submit-btn').prop('disabled', false);
        $('#edit-issue-del-btn').prop('disabled', false);
        $('#edit-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#edit-issue-submit-btn').click(function(e) //подтвердить редактирование вызова
{
    var field = '#edit-issue-msg';

    var issue_id = typeof $('#edit-issue-id').val() === 'string' ? $('#edit-issue-id').val().trim() : '';
    var worker_id = $('#edit-issue-worker-select').val();
    var status = $('#edit-issue-status-select option:selected').text();
    var position = $('#edit-issue-position-select option:selected').text();
    var issue_time = document.querySelector('input[id="edit-issue-time"]').value;
    var place = typeof $('#edit-issue-place').val() === 'string' ? $('#edit-issue-place').val().trim() : '';
    var issue = typeof $('#edit-issue-text').val() === 'string' ? $('#edit-issue-text').val().trim() : '';
    var notes = typeof $('#edit-issue-notes').val() === 'string' ? $('#edit-issue-notes').val().trim() : '';
    var urgent = ($('#edit-issue-urgent')[0].checked) ? 1 : 0;

    if(issue_id == '')
    {
        msg(field, 'Нет ID вызова');
        return false;
    }

    if(worker_id == '')
    {
        msg(field, 'Нет ID работника');
        return false;
    }
    if(position == '')
    {
        msg(field, 'Нет должности работника');
        return false;   
    }
    if(issue_time == '')
    {
        msg(field, 'Назначьте дату вызова');
        return false;
    }
    if(place == '')
    {
        msg(field, 'Напишите место вызова');
        return false;
    }
    if(issue == '')
    {
        msg(field, 'Напишите причину вызова');
        return false;
    }
    if(status == '')
    {
        msg(field, 'Не статуса вызова');
        return false;
    }

    $('#edit-issue-submit-btn').prop('disabled', true);
    $('#edit-issue-del-btn').prop('disabled', true);
    $('#edit-issue-modal-body').css('opacity', '.5');

    var params = 'i=' + issue_id + '&w=' +  worker_id + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&x=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status) + '&o=' + encodeURIComponent(position);
    if(notes != '') params += '&n=' + notes;
    if (urgent) params += '&u=' + urgent;
    if(updissue(params, '#edit-issue-msg'))
    {
        $('#edit-issue-modal').modal('hide');
        issues1(1);
    }

    $('#edit-issue-submit-btn').prop('disabled', false);
    $('#edit-issue-del-btn').prop('disabled', false);
    $('#edit-issue-modal-body').css('opacity', '');
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму удаления задачи
{
    $('#del-issue-modal').on('show.bs.modal', function(e)
    {
        var field = '#del-issue-msg';
        var issue_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="del-issue-id"]').val(issue_id);

        $('#del-issue-submit-btn').prop('disabled', true);
        $('#del-issue-modal-body').css('opacity', '.5');

        var issue = getissue('i=' + issue_id, field);
        if(issue != '')
        {
            $(e.currentTarget).find('input[id="del-worker-id"]').val(issue[1]);
            $(e.currentTarget).find('input[id="del-issue-position"]').val(issue[3]);
            $(e.currentTarget).find('input[id="del-issue-status"]').val(issue[2]);
            $(e.currentTarget).find('input[id="del-issue-date"]').val(issue[6]);
            $(e.currentTarget).find('input[id="del-issue-place"]').val(issue[7]);
            $(e.currentTarget).find('input[id="del-issue-text"]').val(issue[8]);
            $(e.currentTarget).find('input[id="del-issue-notes"]').val(issue[9]);
            $(e.currentTarget).find('input[id="del-issue-urgent"]').val(issue[10]);

            $(e.currentTarget).find('textarea[id="del-issue-comment"]').val(issue[11] + ', ' + issue[3] + ': ' + issue[8]);

            $('#del-issue-submit-btn').prop('disabled', false);
            $('#del-issue-modal-body').css('opacity', '');
        }
    });
});
</script>

<script>
$('#del-issue-submit-btn').click(function(e) //подтвердить удаление вызова
{
    var field = '#del-issue-msg';

    var issue_id = typeof $('#del-issue-id').val() === 'string' ? $('#del-issue-id').val().trim() : '';
    var worker_id = typeof $('#del-worker-id').val() === 'string' ? $('#del-worker-id').val().trim() : '';
    var status = typeof $('#del-issue-status').val() === 'string' ? $('#del-issue-status').val().trim() : '';
    var position = typeof $('#del-issue-position').val() === 'string' ? $('#del-issue-position').val().trim() : '';
    var issue_time = typeof $('#del-issue-date').val() === 'string' ? $('#del-issue-date').val().trim() : '';
    var place = typeof $('#del-issue-place').val() === 'string' ? $('#del-issue-place').val().trim() : '';
    var issue = typeof $('#del-issue-text').val() === 'string' ? $('#del-issue-text').val().trim() : '';
    var notes = typeof $('#del-issue-notes').val() === 'string' ? $('#del-issue-notes').val().trim() : '';
    var urgent = typeof $('#del-issue-urgent').val() === 'string' ? $('#del-issue-urgent').val().trim() : '';

    if(issue_id == '')
    {
        msg(field, 'Нет ID вызова');
        return false;
    }

    if(worker_id == '')
    {
        msg(field, 'Нет ID работника');
        return false;
    }
    if(position == '')
    {
        msg(field, 'Нет должности работника');
        return false;   
    }
    if(issue_time == '')
    {
        msg(field, 'Назначьте дату вызова');
        return false;
    }
    if(place == '')
    {
        msg(field, 'Напишите место вызова');
        return false;
    }
    if(issue == '')
    {
        msg(field, 'Напишите причину вызова');
        return false;
    }
    if(status == '')
    {
        msg(field, 'Не статуса вызова');
        return false;
    }

    $('#del-issue-submit-btn').prop('disabled', true);
    $('#del-issue-cancel-btn').prop('disabled', true);
    $('#del-issue-modal-body').css('opacity', '.5');

    var params = 'd=1' + '&i=' + issue_id + '&w=' +  worker_id + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&x=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status) + '&o=' + encodeURIComponent(position);
    if(notes != '') params += '&n=' + notes;
    if (urgent == 't') params += '&u=1';

    if(updissue(params, field))
    {
        $('#del-issue-modal').modal('hide');
        issues1(1);
    }

    $('#del-issue-submit-btn').prop('disabled', false);
    $('#del-issue-cancel-btn').prop('disabled', false);
    $('#del-issue-modal-body').css('opacity', '');
});
</script>

<script>
$('#edit-issue-del-btn').click(function(e) //подтвердить редактирование вызова
{
    var field = '#edit-issue-msg';

    var issue_id = typeof $('#edit-issue-id').val() === 'string' ? $('#edit-issue-id').val().trim() : '';
    var worker_id = $('#edit-issue-worker-select').val();
    var status = $('#edit-issue-status-select option:selected').text();
    var position = $('#edit-issue-position-select option:selected').text();
    var issue_time = document.querySelector('input[id="edit-issue-time"]').value;
    var place = typeof $('#edit-issue-place').val() === 'string' ? $('#edit-issue-place').val().trim() : '';
    var issue = typeof $('#edit-issue-text').val() === 'string' ? $('#edit-issue-text').val().trim() : '';
    var notes = typeof $('#edit-issue-notes').val() === 'string' ? $('#edit-issue-notes').val().trim() : '';
    var urgent = ($('#edit-issue-urgent')[0].checked) ? 1 : 0;

    if(issue_id == '')
    {
        msg(field, 'Нет ID вызова');
        return false;
    }

    if(worker_id == '')
    {
        msg(field, 'Нет ID работника');
        return false;
    }
    if(position == '')
    {
        msg(field, 'Нет должности работника');
        return false;   
    }
    if(issue_time == '')
    {
        msg(field, 'Назначьте дату вызова');
        return false;
    }
    if(place == '')
    {
        msg(field, 'Напишите место вызова');
        return false;
    }
    if(issue == '')
    {
        msg(field, 'Напишите причину вызова');
        return false;
    }
    if(status == '')
    {
        msg(field, 'Не статуса вызова');
        return false;
    }

    $('#edit-issue-submit-btn').prop('disabled', true);
    $('#edit-issue-del-btn').prop('disabled', true);
    $('#edit-issue-modal-body').css('opacity', '.5');

    var params = 'd=1' + '&i=' + issue_id + '&w=' +  worker_id + '&t=' + encodeURIComponent(issue_time) + '&p=' + encodeURIComponent(place) + '&x=' + encodeURIComponent(issue) + '&s=' + encodeURIComponent(status) + '&o=' + encodeURIComponent(position);
    if(notes != '') params += '&n=' + notes;
    if (urgent) params += '&u=' + urgent;
    if(updissue(params, '#edit-issue-msg'))
    {
        $('#edit-issue-modal').modal('hide');
        issues1(1);
    }

    $('#edit-issue-submit-btn').prop('disabled', false);
    $('#edit-issue-del-btn').prop('disabled', false);
    $('#edit-issue-modal-body').css('opacity', '');
});
</script>