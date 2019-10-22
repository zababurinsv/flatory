<div class="space_bottom">
    <?= $nav ?>
</div>
<div class="hpanel panel-body-cover" id="ws-mass-edit" style="display: none">
    <div class="panel-heading hbuilt">Массовое редактирование</div>
    <div class="panel-body" style="display: none;"><?= $mass_edit ?></div>
</div>
<div class="hpanel">
    <div class="panel-body">
<!--        <div class="upload_more" style="display: none">
            <a href="/admin/upload" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Загрузить еще</a>
        </div>-->
        <div class="upload-box">
            <form action="post" role="form" id="uploadform" enctype="multipart/form-data">
                <div class="upload-box__btn_select">
                    <input type="file" class="form-control" multiple="true">
                </div>
                <label for="file">Выберите файлы для загрузки</label>
            </form>
        </div>
        <div class="upload_more" style="display: none">
            <table class="table uploaded_list">
                <thead>
                    <tr>
                        <th><input type="checkbox" name="check_all_files"></th>
                        <th></th>
                        <th>Информация о файле</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<!--progress of upload-->
<script type="text/template" id="tpl__upload_file">
    <tr data-file="{{=it.name}}">
    <td></td>
    <td>
    <h4>{{=it.original_name}}</h4>
    </td>
    <td>
    <div class="progress">
    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"><span>0</span></div>
    </div>
    </td>
    <td></td>
    </tr>
</script>
<!--/progress of upload-->
<!--uploaded preview-->
<script type="text/template" id="tpl__upload_file_done">
    <td>
    <input type="checkbox" name="file_id[]" value="{{=it.file_id}}">
    </td>
    <td>
    <img src="/images/{{=it.src_preview}}" class="upload_preview">
    </td>
    <td>
    <ul class="list-unstyled">
    <li>Редактировать: <a href="/admin/storage/card/{{=it.name}}" target="_blank">{{=it.original_name}}.{{=it.ext}}</a></li>
    <li>Ссылка на файл: <a href="{{=it.path}}{{=it.name}}.{{=it.ext}}" target="_blank">{{=it.path}}{{=it.name}}.{{=it.ext}}</a></li>
    <li>Размер: {{=it.size}}</li>
    <li>Загружено: {{=it.updated}}</li>
    </ul>
    </td>
    <td>
    <a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right abort_upload" data-fid="{{=it.file_id}}" data-fon="{{=it.original_name}}.{{=it.ext}}">Отменить загрузку</a>
    </td>
</script>
<!--/uploaded preview-->
<script>
    (function () {
        // подписываемся на событие Загрузка файла - если редактор не был показан, показываем
        $.subscribe('FlUpload.uploadSuccess', function (e) {

            $('#ws-mass-edit').show();
        });
        // подписываемся на событие отмена загрузки файла произошла
        $.subscribe('FlUpload.uploadAbort', function (e) {
            // если все удалено, возвращаем загрузчик и прячем массовый редактор
            if (!$('.upload_more .uploaded_list tbody tr').length) {
                // редактор
                $('#ws-mass-edit').hide();
                // 
                $('.upload_more').hide();
                $('.upload_more').siblings('.upload-box').show();
            }

        });
    }());
</script>
