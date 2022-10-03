<!--- positions page --->
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
            header('Location: login.php?r=positions');
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
                            <a class="nav-link" href="/workers.php">Персонал</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="/positions.php">Должности</a>
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
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-position" onchange="positions(1)" placeholder="Поиск должности" aria-label="Input group example" aria-describedby="btnGroupAddon">
                    </div>
                    <select class="form-control"  style="width:auto;" id="select-per-page" name="perpage" onchange="positions(1)">
                        <option selected value=15>15</option>
                        <option value=30>30</option>
                        <option value=50>50</option>
                        <option value=100>100</option>
                    </select>
                </div><!--- btn-group --->
                <button type="button" class="btn btn-primary" id="new-position-btn">Добавить должность</button>
            </div> <!--- btn-toolbar --->
            <p class="positions-msg" id="positions-msg"></p>

            <nav aria-label="...">
                <ul class="pagination pagination-sm justify-content-center" id="positions-pages"></ul>
            </nav>

            <div id="positions-table"></div>
        </div> <!--- container --->

        <?php include './footer.php' ?>

        <!--- new-position-modal --->
        <div class="modal fade" id="new-position-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Создать новую должность</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="new-position-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <label for="new-position-name">Должность</label>
                                <input type="text" class="form-control" id="new-position-name" placeholder="Должность" value = "" />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="new-position-submit-btn">Создать</button>
                    </div>
                    <p class="new-position-msg" id="new-position-msg"></p>
                </div>
            </div>
        </div>

        <!--- edit-position-modal --->
        <div class="modal fade" id="edit-position-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Редактировать должность</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="edi-position-modal-body">
                    <!--- контент окна ниже --->
                        <form role="form">
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="old-position-name" placeholder="0" value = "" readonly />
                                <label for="edit-position-name">Должность</label>
                                <input type="text" class="form-control" id="edit-position-name" placeholder="Должность" value = "" />
                            </div>
                        </form>
                    <!--- контент окна выше--->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Закрыть</button>
                        <button type="button" class="btn btn-primary submitBtn" id="edit-position-submit-btn">Сохранить</button>
                    </div>
                    <p class="edit-position-msg" id="edit-position-msg"></p>
                </div>
            </div>
        </div>

    </body>

<script>
$(document).ready(function() // загружает список должностей как только страница загрузится
{
    positions(1);
});
</script>

<script>
function getpages(params, msg) // получение страниц [всего, найдено, страниц, текущая страница]
{
    var pages = '';

    $.ajax ({
        async: false,
        type: 'POST',
        url: 'countpositions.php', // ничего не пишет, только возвращает количество в массиве
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
function positions(page) // поиск и отображение должностей по параметрам
{
    var search_position = document.getElementById('search-position').value;
    var q = $('#select-per-page').val();

    if(!Number.isInteger(page)) page = parseInt(page);

    var params = 'p=' + (page - 1) + '&q=' + q;
    if(search_position != '') params += '&n=' + search_position;//toLowerCase();
    console.log(params);

    var pages = getpages(params, '#positions-msg');
    var positions = getpositions(params, '#positions-msg');

    if(pages == '') return;
    document.getElementById('positions-pages').innerHTML = '';
    document.getElementById('positions-table').innerHTML = '';

    var allpositions = pages[0]; 
    if(allpositions == 0)
    {
        $('#positions-msg').html('<div class="alert alert-primary" role="alert">Нет должностей в справочнике</div>');
        return;
    }
    var foundpositions = pages[1];
    var totalpages = pages[2]; var currentpage = pages[3];
    $('#positions-msg').html('<div class="alert alert-primary" role="alert">Всего: ' + allpositions + ' должностей, отобрано: ' + foundpositions + ' должностей, всего страниц: ' + totalpages + ', текущая страница: ' + currentpage + '</div>');
    
    for (let i = 1; i <= totalpages; i++)
    {
        if(i === currentpage) document.getElementById('positions-pages').innerHTML += '<li class=\"page-item active\" aria-current=\"page\"><span class=\"page-link\">' + i + '</span></li>';
        else document.getElementById('positions-pages').innerHTML += '<li onclick=\"positions(this.id)\" class=\"page-item\" id=\"' + i + '\"><a class=\"page-link\" >' + i + '</a></li>';
    }

    if(positions == '') return;

    var table = '<table class=\"table table-hover\">' +
    '<thead class=\"thead-primary\"><tr class=\"table-primary\">' +
    '<td scope=\"col\">Должность</td>' +
    '<td scope=\"col\">Редактировать</td>' +
    '</tr></thead><tbody>';

    for(p in positions)
    {
        var editbutton = '<td><button type=\"button\" class=\"btn btn-primary\" id=\"edit-position-btn\" data-bs-toggle=\"modal\"  data-bs-target=\"#edit-position-modal\" data-whatever=\"' + positions[p] + '\"><img class=\"img-responsive\" title=\"edit\" src=\"img/edit.svg\"/></button></td>';

        table += '<tr><td scope=\"row\">' + positions[p] + '</td>' + editbutton + '</tr>';
    }
    table += '</tbody></table><br><br>';
    document.getElementById('positions-table').innerHTML = table;
}
</script>

<script>
$('#new-position-btn').click(function() //при нажатии на кнопку "создать должность"
{
    var new_position_modal = new bootstrap.Modal(document.getElementById('new-position-modal'));
    new_position_modal.show();
});
</script>

<script>
$('#new-position-submit-btn').click(function() //добавляет должность в бд, перезагружает таблицу
{
    $('#new-position-msg').html('');

    const position = typeof $('#new-position-name').val() === 'string' ? $('#new-position-name').val().trim() : '';
    if(position == '')
    {
        $('#new-position-msg').html('<div class="alert alert-primary" role="alert">Выберите должность обязательно</div>');
        return;
    }
    var params = 'p=' + position;

    $.ajax ({
        type: 'POST',
        url: 'newposition.php', // ничего не пишет при успехе
        data: params,
        beforeSend: function()
        {
            $('#new-position-submit-btn').attr('disabled','disabled');
        },
        success: function(responce)
        {
            $('#new-position-submit-btn').removeAttr('disabled');

            if(responce != '') $('#new-position-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            else
            {
                $('#new-position-modal').modal('hide');
                positions(1);
            }
        },
        error: function(xhr, status, error)
        {
            $('#new-position-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText + '</div>');
        }
    });
});
</script>

<script>
$(document).ready(function() //для вставки информации в форму редактирования должности
{
    $('#edit-position-modal').on('show.bs.modal', function(e)
    {
        var position = $(e.relatedTarget).data('whatever');
        $(e.currentTarget).find('input[id="edit-position-name"]').val(position);
        $(e.currentTarget).find('input[id="old-position-name"]').val(position);
    });
});
</script>

<script>
$('#edit-position-submit-btn').click(function() //редактирует должность в бд, перезагружает таблицу 
{
    $('#edit-position-msg').html('');

    const new_position = typeof $('#edit-position-name').val() === 'string' ? $('#edit-position-name').val().trim() : '';
    const old_position = typeof $('#old-position-name').val() === 'string' ? $('#old-position-name').val().trim() : '';

    if(new_position == '' || old_position == '')
    {
        $('#edit-position-msg').html('<div class="alert alert-primary" role="alert">Напишите должность обязательно</div>');
        return;
    }
    if(new_position == old_position)
    {
        $('#edit-position-modal').modal('hide');
        return;
    }
    var params = 'p=' + new_position + '&o=' + old_position;

    $.ajax ({
        type: 'POST',
        url: 'updposition.php',
        data: params,
        beforeSend: function()
        {
            $('#edit-position-submit-btn').attr('disabled','disabled');
        },
        success: function(responce)
        {
            $('#edit-position-submit-btn').removeAttr('disabled');
            if(responce != '') $('#edit-position-msg').html('<div class="alert alert-primary" role="alert">' + responce +'</div>');
            else
            {
                $('#edit-position-modal').modal('hide');
                positions(1);
            }
        },
        error: function(xhr, status, error)
        {
            $('#edit-position-msg').html('<div class="alert alert-primary" role="alert">' + xhr.status + ' ' + xhr.statusText + '</div>');
        }
    });
});
</script>
</html>