<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" method="post" class="row" id="object-form">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Файлы <a href="javascript:void(0)" id="init_widget_storage" class="text-info space_left_xs">добавить</a></label>
                    <?php if (empty($files)): ?>
                        <div class="alert alert-info">Документов нет.</div>
                        <div class="space_bottom"></div>
                    <?php endif; ?>
                    <ul class="list-unstyled cart_image_added">
                        <?php foreach ($files as $file): ?>
                            <li class="on-hover-box doc-list-item">
                                <a href="/admin/storage/card/<?= array_get($file, 'name') ?>" target="_blank" title="<?= array_get($file, 'original_name') ?>">
                                    <i class="icon-ext icon-ext-<?= array_get($file, 'ext') ?>"></i>
                                    <?= !!array_get($file, 'alt') ? $file['alt'] : array_get($file, 'original_name') ?>
                                </a>
                                <a href="javascript:void(0)" title="Удалить" class="glyphicon glyphicon-trash space_left only-on-hover" data-delete-parent="li"></a>
                                <input type="hidden" name="files[]" value="<?= array_get($file, 'file_id') ?>" />
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group js-form-links">
                    <label>Ссылки <a href="javascript:void(0)" class="text-info space_left_xs" data-add>добавить</a></label>
                    <?php if (empty($links)): ?>
                        <div class="alert alert-info">Ссылок нет.</div>
                        <div class="space_bottom"></div>
                        <?= $this->load->view($this->template_dir . 'forms/links', array('link' => ['link_id' => 'add_0']), TRUE); ?>
                    <?php else: ?>
                        <?php foreach ($links as $link): ?>
                            <?= $this->load->view($this->template_dir . 'forms/links', array('link' => $link), TRUE); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </form>
        <script type="text/template" id="card__item_view__tile">
            <li class="on-hover-box doc-list-item">
            <a href="/admin/storage/card/{{=it.name}}" target="_blank" title="{{=it.original_name}}">
            <i class="icon-ext icon-ext-{{=it.ext}}"></i>
            {{? !!it.alt }}{{=it.alt}}{{??}}{{=it.original_name}}{{?}}
            </a>
            <a href="javascript:void(0)" title="Удалить" class="glyphicon glyphicon-trash space_left only-on-hover" data-delete-parent="li"></a>
            <input type="hidden" name="files[]" value="{{=it.file_id}}" />
            </li>
        </script>
        <script type="text/javascript">
            $(document).on('ready', function () {

                function _listeners() {
                    // удаление родительского элемента
                    $('[data-delete-parent]').off('click').on('click', function (e) {
                        
                        if(!confirm('Вы уверены?'))
                            return;
                        
                        $(this).parent($(this).data('delete-parent')).remove();
                    });
                }
                _listeners();
                
                // remove added & removing files
                FlUpload.extends.uploadAbortSuccess(function (fileId) {
                    $('.cart_image_added').find('[name="files[]"][value="' + fileId + '"]').parents('tr').remove();
                });
                // вешаем на событие "добавленгие элемента" - при добавлении элемента в список, обновляем событие удаление родителя
                FlObjectCard.onRenderTile(function (data){
                    _listeners();
                });
            });
        </script>
    </div>
</div>
<?= $widget_storage ?>