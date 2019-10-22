<a href="javascript:void(0)" class="btn btn-default" id="init_widget_storage">Загрузить</a>
<div class="panel panel-default widget_storage" style="display: none;">
    <div class="panel-heading">
        <ul class="nav nav-pills widget_storage__nav">
            <li class="active"><a href="javascript:void(0)" data-section="upload">Загрузить</a></li>
            <li><a href="javascript:void(0)" data-section="storage">Выбрать</a></li>
            <li class="pull-right"><a href="javascript:void(0)" id="destroy_widget_storage" class="text-danger glyphicon glyphicon-remove"></a></li>
        </ul>
    </div>
    <div class="panel-body widget_storage__content">
        <div class="widget_storage__upload" style="display: none"><?= $upload ?></div>
        <div class="widget_storage__storage" style="display: none"><?= $storage ?></div>
    </div>
    <div class="panel-footer">
        <a href="javascript:void(0)" class="btn btn-default" id="widget_storage__add">Добавить</a>
    </div>
</div>
