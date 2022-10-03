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
                    <select class="form-select form-select-sm" aria-label="Быстрый поиск по должности" onchange="workers(1)" id="select-search-position">
                    </select>
                    <select class="form-control"  style="width:auto;" id="select-per-page" name="perpage" onchange="workers(1)">
                        <option selected value=15>15</option>
                        <option value=30>30</option>
                        <option value=50>50</option>
                        <option value=100>100</option>
                    </select>
                </div> <!--- btn-group --->
                <div class="input-group">
                    <input type="text" class="form-control" id="search-worker" onchange="workers(1)" placeholder="Поиск по Ф.И.О." aria-label="" aria-describedby="btnGroupAddon">
                    <button type="button" class="btn btn-primary" id="new-worker-btn">Добавить сотрудника</button>
                </div>
            </div> <!--- btn-toolbar -->
            <p class="workers-msg" id="workers-msg"></p>

            <nav aria-label="...">
                <ul class="pagination pagination-sm justify-content-center" id="workers-pages"></ul>
            </nav>

            <div id="workers-table"></div>
        </div><!--- container --->

        <?php include './footer.php' ?>

        <!--- modals --->
        <!--- new-worker modal --->
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
                                <label for="new-worker-name">Ф.И.О</label>
                                <input type="text" class="form-control" id="new-worker-name" placeholder="Введите Ф.И.О." value = "" />
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="new-worker-positions">Должности (можно выбрать несколько)</label>
                                <select class="form-control" id="new-worker-positions" name="positions" multiple></select>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" id="new-worker-cancel-btn"data-bs-dismiss="modal">Отмена</button>
                                <button type="button" class="btn btn-primary submitBtn" id="new-worker-submit-btn">Сохранить</button>
                            </div>
                            <p class="new-worker-msg" id="new-worker-msg"></p>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                </div>
            </div>
        </div>

        <!--- del-worker-modal --->
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
                                <label for="del-worker-name">Ф.И.О. и должности</label>
                                <input type="hidden" class="form-control" id="del-worker-id" placeholder="0" value = "" readonly />
                                <input type="text" class="form-control" id="del-worker-name" placeholder="Ф.И.О. работника" value = "" readonly />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" id="del-worker-cancel-btn" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary submitBtn" id="del-worker-submit-btn">Удалить</button>
                    </div>
                    <p class="del-worker-msg" id="del-worker-msg"></p>
                </div>
            </div>
        </div>

        <!--- edit-worker-modal --->
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
                                <label for="edit-worker-position">Должности</label>
                                <select class="form-control" id="edit-worker-positions" name="positions" multiple>
                                </select>
                                <br>
                                <label for="edit-worker-phone">Телефон</label>
                                <input type="text" class="form-control" id="edit-worker-phone" placeholder="" value = "" />
                                <br>
                                <label for="edit-worker-email">Электронная почта</label>
                                <input type="text" class="form-control" id="edit-worker-email" placeholder="" value = "" />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal" id="edit-worker-cancel-btn">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-worker-delete-btn">Удалить</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-worker-submit-btn">Сохранить</button>
                    </div>
                    <p class="edit-worker-msg" id="edit-worker-msg"></p>
                </div>
            </div>
        </div>

        <!--- new-issue-modal --->
        <div class="modal fade" id="new-issue-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Оформить вызов на работника?</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="new-issue-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="new-issue-id" placeholder="0" value = "" readonly />
                                <label for="new-issue-name">Ф.И.О. работника</label>
                                <input type="text" class="form-control" id="new-issue-name" placeholder="Ф.И.О. работника" value = "" readonly />
                                <br>
                                <label for="new-issue-position">Должность</label>
                                <select class="form-control" id="new-issue-position" name="position">
                                </select>
                                <br>
                                <label for="new-issue-time">Время вызова</label><br>
                                <input type="date" id="new-issue-time" name="new-issue-time" value="" min="" max="" required>
                                <br>
                                <label for="new-issue-place">Место вызова</label>
                                <textarea class="form-control" id="new-issue-place" rows="1"></textarea>
                                <br>
                                <label for="call-worker-text">Описание вызова</label>
                                <textarea class="form-control" id="new-issue-text" rows="4"></textarea>
                                <br>
                                <input type="checkbox" class="btn-check" name="options" id="new-issue-urgent" autocomplete="off">
                                <label class="btn btn-outline-primary" for="new-issue-urgent">Важно!</label>
                            </div>
                        </form>
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

    </body>

<script>
$(document).ready(function() // загружает список работников как только страница загрузится
{
    var positions = getpositions('', '#workers-msg');
    if(positions != '')
    {
        //append 'all' first
        $('#select-search-position').append('<option value=Все должности>Все должности</option>');
        for(p in positions) $('#select-search-position').append('<option value=' + positions[p] + '>' + positions[p] + '</option>');
    }
    else
    {
        $('#select-search-position').prop('disabled', 'disabled');
        $('#new-worker-btn').prop('disabled', 'disabled');
        return;
    }
    workers(1);
});
</script>

<script>
function getpositions(params, msg) // возвращает список позиций []
{
    var positions = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getpositions.php',
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if (responce != '') positions = JSON.parse(responce);
            else $(msg).html('<div class="alert alert-primary" role="alert">Нет должностей в справочнике</div>');
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return positions;
}
</script>

<script>
function getpages(params, msg) // получение страниц [всего, найдено, страниц, текущая страница]
{
    var pages = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'countworkers.php', // ничего не пишет, только возвращает количество в массиве
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
function getworkers(params, msg) // возвращает список работников [[],[]]
{
    var workers = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'getworkers.php',
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if (responce != '') workers = JSON.parse(responce);
        },
        error: function(xhr, status, error)
        {
            $(msg).html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
    return workers;
}
</script>

<script>
function getworker(worker_id, msg) //получает информацию об одном работнике
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
function delworker(params, msg) // удаляет работника, возвращает true при успехе. не перезагружает таблицу
{
    var deleted = false;

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'delworker.php',
        data: params,
        beforeSend: function()
        {
            $(msg).html('');
        },
        success: function(responce)
        {
            if(responce != '') $(msg).html('<div class="alert alert-primary" role="alert">' + responce + '</div>');
            else
            {
                $(msg).html('<div class="alert alert-primary" role="alert">Работник удален</div>');
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
function workers(page) // поиск и отображение сотрудников по параметрам
{
    var n = document.getElementById('search-worker').value;
    var q = $('#select-per-page').val();
    var s = $('#select-search-position option:selected').text();

    if(!Number.isInteger(page)) page = parseInt(page);
    var params = 'p=' + (page - 1) + '&q=' + q;
    if(n != '') params += '&n=' + n;
    if(s != '') params += '&s=' + s;
    console.log('getworker params: ' + params);

    var pages = getpages(params, '#workers-msg');
    var workers = getworkers(params, '#workers-msg');

    if(pages == '') return;
    document.getElementById('workers-pages').innerHTML = '';
    document.getElementById('workers-table').innerHTML = '';

    var allworkers = pages[0];
    if(allworkers == 0)
    {
        $('#workers-msg').html('<div class="alert alert-primary" role="alert">Нет работников в справочнике</div>');
        return;
    }
    var foundworkers = pages[1];
    var totalpages = pages[2]; var currentpage = pages[3];
    $('#workers-msg').html('<div class="alert alert-primary" role="alert">Всего: ' + allworkers + ' работников, отобрано: ' + foundworkers + ' работников, всего страниц: ' + totalpages + ', текущая страница: ' + currentpage + '</div>');

    for (let i = 1; i <= totalpages; i++)
    {
        if(i === currentpage) document.getElementById('workers-pages').innerHTML += '<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">' + i + '</span></li>';
        else document.getElementById('workers-pages').innerHTML += '<li onclick=\"workers(this.id)\" class=\"page-item\" id=\"' + i + '\"><a class=\"page-link\" >' + i + '</a></li>';
    }

    if(workers == '') return;
    var table = '<table class=\"table table-hover\">' + 
                '<thead class=\"thead-primary\"><tr class=\"table-primary\">' +
                '<td scope=\"col\">Имя</td>' +
                '<td scope=\"col\">Должности</td>' +
                '<td scope=\"col\">Телефон</td>' +
                '<td scope=\"col\">Электронная почта</td>' +
                '<td scope=\"col\">Создать вызов</td>' +
                '<td scope=\"col\">Редактировать</td>' +
                '<td scope=\"col\">Удалить</td>' +
                '</tr></thead><tbody>';
    for (let i = 0; i < workers.length; i++)
    {
        var callbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"new-issue-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#new-issue-modal\" data-whatever=\"' + workers[i][0] + '\"><img class=\"img-responsive\" title=\"call\" src=\"img/call.svg\"/></button></td>';

        var editbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-worker-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-worker-modal\" data-whatever=\"' + workers[i][0] + '\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></button></td>';

        var deletebutton  = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"delete-worker-btn\" data-bs-toggle=\"modal\" data-bs-target=\"#del-worker-modal\" data-whatever=\"' + workers[i][0] + '\"><img class=\"img-responsive\" title=\"delete\" src=\"img/delete.svg\"/></button></td>';

        table += '<tr>' +
        '<td>' + workers[i][1] + '</td>' +
        '<td>' + workers[i][2].replace(/"/g, '').replace(',', ' , ') + '</td>' +
        '<td>' + workers[i][3] + '</td>' +
        '<td>' + workers[i][4] + '</td>' +
        callbutton + editbutton + deletebutton + '</tr>';
    }
    table += '</tbody></table><br><br>';
    document.getElementById('workers-table').innerHTML = table;

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
$('#new-worker-btn').click(function() // при нажатии на кнопку "добавить работника"
{
    var new_worker_modal = new bootstrap.Modal(document.getElementById('new-worker-modal'));
    var positions = getpositions('', '#new-worker-msg');
    
    $('#new-worker-positions').empty();
    if(positions == '') $('#new-worker-submit-btn').prop('disabled', 'disabled');
    else for(p in positions) $('#new-worker-positions').append('<option value=' + positions[p] + '>' + positions[p] + '</option>');

    new_worker_modal.show();
});
</script>

<script>
$('#new-worker-submit-btn').click(function() //добавляет работника в бд, перезагружает таблицу, если ок
{
    const name = typeof $('#new-worker-name').val() === 'string' ? $('#new-worker-name').val().trim() : '';
    var positions = $('#new-worker-positions option:selected').toArray().map(item => item.text).join();
    const phone = typeof $('#new-worker-phone').val() === 'string' ? $('#new-worker-phone').val().trim() : '';
    const email = typeof $('#new-worker-email').val() === 'string' ? $('#new-worker-email').val().trim() : '';

    if(name == '')
    {
        $('#new-worker-msg').html('<div class="alert alert-primary" role="alert">Введите Ф.И.О. обязательно</div>');
        return false;
    }
    if(positions == '')
    {
        $('#new-worker-msg').html('<div class="alert alert-primary" role="alert">Выберите должность обязательно</div>');
        return false;
    }

    var params = 'n=' + name + '&p=' + positions;
    if(phone != '') params += '&t=' + encodeURIComponent(phone);
    if(email != '') params += '&e=' + encodeURIComponent(email);
    console.log('newworker params: ' + params);

    $.ajax ({
        type: 'POST',
        url: 'newworker.php',
        data: params,
        beforeSend: function()
        {
            $('#new-worker-submit-btn').attr('disabled','disabled');
            $('#new-worker-modal-body').css('opacity', '.5');
            $('#new-worker-msg').html('');
        },
        success: function(responce)
        {
            if(responce != '') $('#new-worker-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            $('#new-worker-submit-btn').removeAttr('disabled');
            $('#new-worker-modal-body').css('opacity', '');
            workers(1);
            //$('#new-worker-modal').modal('hide');
        },
        error: function(xhr, status, error)
        {
            $('#new-worker-submit-btn').removeAttr('disabled');
            $('#new-worker-modal-body').css('opacity', '');
            $('#new-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
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

        $('#del-worker-submit-btn').attr('disabled','disabled');
        $('#del-worker-modal-body').css('opacity', '.5');

        var worker = getworker(worker_id, '#del-worker-msg');
        if(worker == '') return;

        $(e.currentTarget).find('input[id="del-worker-name"]').val(worker[0] + ': ' + worker[1]);

        $('#del-worker-submit-btn').removeAttr('disabled');
        $('#del-worker-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#del-worker-submit-btn').click(function(e) //подтвердить удаление работника
{
    var worker_id = document.getElementById('del-worker-id').value;
    $('#del-worker-submit-btn').attr('disabled','disabled');
    $('#del-worker-modal-body').css('opacity', '.5');
    if(delworker('i=' + worker_id, '#del-worker-msg'))
    {
        document.querySelector('#del-worker-cancel-btn').innerText = 'Закрыть';
        $('#del-worker-modal-body').css('opacity', '');
        workers(1);
    }
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму просмотра работника
{
    $('#edit-worker-modal').on('show.bs.modal', function(e)
    {
        var worker_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-worker-id"]').val(worker_id);

        $('#edit-worker-submit-btn').attr('disabled','disabled');
        $('#edit-worker-delete-btn').attr('disabled','disabled');
        $('#edit-worker-modal-body').css('opacity', '.5');
        $('#edit-worker-positions').empty();

        var worker = getworker(worker_id, '#edit-worker-msg');
        if(worker == '') return;
        var positions = getpositions('', '#edit-worker-msg');
        if(positions == '') return;

        $(e.currentTarget).find('input[id="edit-worker-name"]').val(worker[0]);
        if(worker[2] != '') $(e.currentTarget).find('input[id="edit-worker-phone"]').val(worker[2]);
        if(worker[3] != '') $(e.currentTarget).find('input[id="edit-worker-email"]').val(worker[3]);

        //positions
        var worker_positions = worker[1].split(', ');
        /*console.log('workers positions: ' + worker_positions); //["Сантехник", "Электрик"]
        console.log('all positions: ' + positions);*/

        for(p in positions)
        {
            if(worker_positions.includes(positions[p])) $('#edit-worker-positions').append('<option value=' + positions[p] + ' selected>' + positions[p] + '</option>');
            else $('#edit-worker-positions').append('<option value=' + positions[p] + '>' + positions[p] + '</option>');
        }

        $('#edit-worker-submit-btn').removeAttr('disabled');
        $('#edit-worker-delete-btn').removeAttr('disabled');
        $('#edit-worker-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#edit-worker-submit-btn').click(function(e) //подтвердить редактирование работника
{
    var worker_id = document.getElementById('edit-worker-id').value;
    const name = typeof $('#edit-worker-name').val() === 'string' ? $('#edit-worker-name').val().trim() : '';
    var positions = $('#edit-worker-positions option:selected').toArray().map(item => item.text).join();
    console.log(positions);
    const phone = typeof $('#edit-worker-phone').val() === 'string' ? $('#edit-worker-phone').val().trim() : '';
    const email = typeof $('#edit-worker-email').val() === 'string' ? $('#edit-worker-email').val().trim() : '';
    if(worker_id == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    if(name == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Введите Ф.И.О. обязательно</div>');
        return false;
    }
    if(positions == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Выберите должность обязательно</div>');
        return false;
    }
    var params = 'i=' + worker_id + '&n=' + name + '&p=' + positions;
    if(phone != '') params += '&t=' + encodeURIComponent(phone);
    if(email != '') params += '&e=' + encodeURIComponent(email);
    console.log(params);

    $.ajax ({
        type: 'POST',
        url: 'updworker.php',
        data: params,
        beforeSend: function()
        {
            $('#edit-worker-submit-btn').attr('disabled','disabled');
            $('#edit-worker-delete-btn').attr('disabled','disabled');
            $('#edit-worker-modal-body').css('opacity', '.5');
            $('#edit-worker-msg').html('');
        },
        success: function(responce)
        {
            if(responce != '')  $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            else
            {
                workers(1);
                $('#edit-worker-modal').modal('hide');
            }
        },
        error: function(xhr, status, error)
        {
            $('#edit-worker-submit-btn').removeAttr('disabled');
            $('#edit-worker-modal-body').css('opacity', '');
            $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText +'</div>');
        }
    });
});
</script>

<script>
$('#edit-worker-delete-btn').click(function(e) //подтвердить удаление работника через форму просмотра
{
    var worker_id = document.getElementById('edit-worker-id').value;
    if(worker_id == '')
    {
        $('#edit-worker-msg').html('<div class="alert alert-primary" role="alert">Нет ID работника</div>');
        return false;
    }
    $('#edit-worker-submit-btn').attr('disabled','disabled');
    $('#edit-worker-cancek-btn').attr('disabled','disabled');
    $('#edit-worker-modal-body').css('opacity', '.5');
    if(delworker('i=' + worker_id, '#edit-worker-msg'))
    {
        $('#edit-worker-modal-body').css('opacity', '');
        $('#edit-worker-modal').modal('hide');
        workers(1);
    }
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму вызова работника
{
    $('#new-issue-modal').on('show.bs.modal', function(e)
    {
        var worker_id = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="new-issue-id"]').val(worker_id);

        $('#new-issue-submit-btn').attr('disabled','disabled');
        $('#new-issue-modal-body').css('opacity', '.5');
        $('#new-issue-position').empty();

        var worker = getworker(worker_id, '#new-issue-msg');
        if(worker == '') return;

        $(e.currentTarget).find('input[id="new-issue-name"]').val(worker[0]);

        var positions = worker[1].split(', ');
        for(p in positions) $('#new-issue-position').append('<option value=' + positions[p] + '>' + positions[p] + '</option>');

        var curr_date = getdate();
        const call_time = document.querySelector('input[id="new-issue-time"]');
        call_time.value = curr_date[0]; call_time.min = curr_date[0]; call_time.max = curr_date[1];


        $('#new-issue-submit-btn').removeAttr('disabled');
        $('#new-issue-modal-body').css('opacity', '');
    });
});
</script>

<script>
$('#new-issue-submit-btn').click(function(e) //подтвердить вызов работника
{
    var worker_id = document.getElementById('new-issue-id').value;
    //const position = $('#new-issue-position option:selected').text();
    var position = $('#new-issue-position option:selected').text();
    const issue_time = document.querySelector('input[id="new-issue-time"]').value;
    const place = typeof $('#new-issue-place').val() === 'string' ? $('#new-issue-place').val().trim() : '';
    const issue = typeof $('#new-issue-text').val() === 'string' ? $('#new-issue-text').val().trim() : '';
    var urgent = (document.getElementById('new-issue-urgent').checked) ? 1 : 0;

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
</html>