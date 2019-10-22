<div class="modal fade modal__storage">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Каталог файлов</h4>
            </div>
            <div class="modal-body">
                <div class="modal_filters"></div>
                <div class="widget_storage__storage">
                    <?= $storage ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-sm btn-primary">Выбрать</button>
            </div>
        </div>
    </div>
</div>