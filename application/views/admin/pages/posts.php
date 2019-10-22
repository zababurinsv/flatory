<?= $filters ?>
<div class="row">
    <div class="col-md-6">
        <a href="/admin/posts/add/<?= $type ?>" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
    </div>
    <div class="col-md-6">
        <?php if (isset($pagination)): ?>
        <div class="pull-right">
            <?= $pagination ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php foreach ($posts as $item): ?>
    <div class="row">
        <div class="col-md-12">
            <div style="width: 150px; min-height:150px; float: left">
                <img style="max-width:120px;max-height:150px;" src="<?= element('image', $item, '') ?>" class="thumbnail"/>
            </div>
            <div>
                <h3><?= element('name', $item, '') ?></h3>
                <small class="text-muted">Создано: <?= date("d.m.Y H:i:s", strtotime(element('created', $item, ''))) ?></small><br>
                <?php if((int)element('status', $item) === MY_Model::STATUS_ACTIVE): ?>
                <small class="text-muted">Опубликовано</small>
                <a href="/<?= $type ?>/<?= element('alias', $item, '') ?>/" target="_blank">Просмотр на сайте</a>
                <?php else: ?>
                <small class="text-muted">Черновик</small>
                <a href="/<?= $type ?>/preview/<?= element('alias', $item, '') ?>/" target="_blank">Предварительный просмотр</a>
                <?php endif; ?>
                <p><?= element('anons', $item, '') ?></p>

                <div class="modal fade" id="myModal<?= element('post_id', $item, ' ') ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Подтверждение действия</h4>
                            </div>
                            <div class="modal-body">
                                Вы действительно хотите удалить?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Отменить</button>
                                <a href="/admin/posts/delete/<?= element('post_id', $item, '') ?>" class="btn btn-sm btn-danger">Удалить</a>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <p>
                    <a href="/admin/posts/edit/<?= element('post_id', $item, '') ?>" class="btn btn-sm btn-primary" role="button">Изменить</a>
                    <a href="#" class="btn btn-sm btn-danger" role="button" data-toggle="modal" onclick="$('#myModal<?= element('post_id', $item, '') ?>').show().attr('class', 'modal fade in');return false;">Удалить</a>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>