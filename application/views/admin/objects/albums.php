<?php
//vdump( $albums);
?>
<div class="row">
    <div class="col-md-12">
        <?= $form ?>
        <?php if ($albums): ?>
            <a href="javascript:void(0)" id="albums_sort_save" class="btn btn-xs btn-success pull-right" style="display: none;">Сохранить порядок альбомов</a>
            <i id="albums_sort_save_complete" class="pull-right text-success" style="margin-top: 10px; display: none;">Порядок альбомов сохранен.</i>
            <h4>Альбомы</h4>
            <div class="panel-group albums_container" id="albums_accordion" role="tablist" aria-multiselectable="false">
                <?php foreach ($albums as $album_id => $item): ?>
                    <div class="panel panel-default overflow-none album" data-id="<?= $album_id ?>">
                        <div class="panel-heading" role="tab" id="heading_<?= $album_id ?>">
                            <div class="panel-title">
                                <a href="javascript:void(0)" class="space_right album_move"><span class="glyphicon glyphicon-sort"></span></a>
                                <a class="collapsed" data-toggle="collapse" data-parent="#albums_accordion" href="#collapse_<?= $album_id ?>" aria-expanded="false" aria-controls="collapse_<?= $album_id ?>"><?= $item['name'] ?> <span class="badge"><?= $item['count'] ?></span></a>
                                <ul class="list-unstyled pull-right">
                                    <?php if($is_show_tag): ?>
                                    <li class="pull-left space_right"><input type="text" class="form-control input-sm" value="<?= '{{=it.album:'. $album_id .'}}' ?>" style="width: 115px;height: 25px;line-height: 1;font-size: 11px;"></li>
                                    <?php endif; ?>
                                    <li class="pull-left space_right edit_item"><a href="javascript:void(0)" title="Редактировать" class=""><span class="glyphicon glyphicon-edit"></span></a></li>
                                    <li class="pull-left delete_item"><a href="javascript:void(0)" title="Удалить" class="text-danger"><span class="glyphicon glyphicon-trash"></span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="collapse_<?= $album_id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_<?= $album_id ?>">
                            <form action="/admin/objects/post_album_update/<?= $item['object_id'] ?>/<?= $item['file_category_id'] ?>" method="post">
                                <div class="panel-body"><i style="display: none;"><?= $item['description'] ?></i><?= $item['content'] ?></div>
                                <div class="panel-footer">
                                    <div class="item_settings">
                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">Название <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="name" class="form-control" value="<?= element('name', $item, '') ?>">
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none;">
                                            <label for="" class="col-sm-2 control-label">Описание</label>
                                            <div class="col-sm-10">
                                                <textarea type="text" name="description" class="form-control" ><?= element('description', $item, '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="image_album_id" value="<?= $item['image_album_id'] ?>">
                                    <input type="hidden" name="album_update" value="1">
                                    <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
                                </div>
                            </form>
                            <input type="hidden" name="sort" value="<?= $item['sort'] ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Альбомов нет.</div>
        <?php endif; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $widget ?>
    </div>
</div>