
<div>
    <!--<a class="text-info space_left_xs">добавить</a>-->
    <label class="control-label">Альбомы</label>
    <!--style="display: none;"-->

    <div class="row">
        <div class="col-md-4">
            <div class="js-add-album" >
                <?= $form ?>
            </div>
            <ul class="list-group list-unstyled albums_container">
                <?php foreach ($albums as $album_id => $item): ?>
                    <li class="list-group-item album cursor-pointer clearfix" data-id="<?= $album_id ?>" aria-controls="album_<?= $album_id ?>" role="tab" data-toggle="tab">
                        <div class="pull-right">
                            <i class="pull-right delete_item space_left_xs cursor-pointer" title="Удалить" style="margin-top: 2px;"><span class="glyphicon glyphicon-trash"></span></i>
                            <div style="width: 75%; min-width: 100px; position: relative;">
                                <input type="text" class="form-control input-xs js-copy pull-right" value="<?= '{{=it.album:' . $album_id . '}}' ?>" style="padding-left: 5px; padding-right: 5px;">
                                <button type="button" class="btn btn-xs btn-warning js-copy-btn" style="position: absolute; left: 0px; width: 100%; min-width: 100px; bottom: -40px; display: none;">Копировать <span class="glyphicon glyphicon-copy"></span></button>
                            </div>
                            <input type="hidden" name="sort" value="<?= $item['sort'] ?>">
                        </div>
                        <div data-album-name><?= $item['name'] ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a id="albums_sort_save" class="btn btn-default" style="display: none; width: 100%;">Сохранить порядок альбомов</a>
            <div id="albums_sort_save_complete" class="alert alert-success text-center" style="display: none;">Порядок альбомов сохранен.</div>
        </div>
        <div class="col-md-8">

            <div class="row form-group clearfix" data-album-controls style="display: none;">
                <div class="col-md-7 col-sm-5">                    
                    <input type="text" name="change_album_name" class="form-control" value="">
                </div>
                <div class="col-md-5 col-sm-7">
                    <button class="btn btn-default pull-right space_left_xs" data-album-action="save">Сохранить альбом</button>
                    <button class="btn btn-primary pull-right" id="init_widget_storage">Добавить фото</button>    
                </div>
            </div>
            <div class="tab-content" id="albums_accordion">
                <?php foreach ($albums as $album_id => $item): ?>
                    <div role="tabpanel" class="tab-pane" id="album_<?= $album_id ?>">
                        <form action="/admin/objects/post_album_update/<?= $item['object_id'] ?>/<?= $item['file_category_id'] ?>" method="post">
                            <?= $item['content'] ?>
                            <input type="hidden" name="name" value="<?= $item['name'] ?>">
                            <input type="hidden" name="image_album_id" value="<?= $album_id ?>">
                            <input type="hidden" name="album_update" value="1">
                        </form>
                    </div>
                <?php endforeach; ?>
                <!--                <div role="tabpanel" class="tab-pane" id="album_2">photo album-2</div>
                                <div role="tabpanel" class="tab-pane" id="album_3">photo album-3</div>-->
            </div>
        </div>
    </div>
    <script>
        $(function () {

            $('[name="change_album_name"]').on('change', function (e) {
                $('#albums_accordion .active').find('[name="name"]').val($(this).val());
            });

            $('[data-toggle="tab"]').on('click', function (e) {
                var name = $(this).find('[data-album-name]').text(),
                        target = $(this).attr('aria-controls');

                if (!target) {
                    $('.tab-pane').removeClass('active');
                    $('[data-album-controls]').hide();
                    return;
                }

                $(this).last().addClass('active');
                $('.tab-pane').removeClass('active');
                $('#' + target).addClass('active');

                $('[name="change_album_name"]').val(name);
                $('[data-album-controls]').show();
            });
        });
    </script>
</div>