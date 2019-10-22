<div class="row">
    <div class="col-md-6">
        <a href="/admin/users/add/<?= $type ?>/" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
    </div>
</div>
<div class="row">
    <table border="0">
    <?php foreach ($users as $item):?>
        <tr>
            <td style="width: 150px; height: 150px;">
                <div class="col-sm-2"><img style="max-width:120px;max-height:150px" src="<?= element('image', $item, '') ?>" class="thumbnail"/></div>
            </td>
            <td>
                <div class="col-sm-9">
                    <h3><?= element('company_name', $item, '') ?></h3>

                    <div class="modal fade" id="myModal<?= element('id', $item, '') ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="$('#myModal<?= element('id', $item, '') ?>').hide().attr('class','modal fade');return false;">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Подтверждение действия</h4>
                                </div>
                                <div class="modal-body">
                                    Вы действительно хотите удалить <?= element('company_name', $item, '') ?>?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal" onclick="$('#myModal<?= element('id', $item, '') ?>').hide().attr('class','modal fade');return false;">Отменить</button>
                                    <a href="/admin/users/delete/<?= $type ?>/<?= element('id', $item, '') ?>" class="btn btn-sm btn-danger">Удалить</a>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

                    <p>
                        <a href="/admin/users/edit/<?= $type ?>/<?= element('id', $item, '') ?>" class="btn btn-sm btn-primary" role="button">Изменить</a>
                        <!--a href="#" onclick="$('.modal-footer a').attr('href','/admin/delete_news/<?= element('id', $item, '') ?>');return false;" class="btn btn-danger" role="button" data-toggle="modal" data-target="#myModal<?= element('id', $item, '') ?>">Удалить</a-->
                        <a href="#" class="btn btn-sm btn-danger" role="button" data-toggle="modal" onclick="$('#myModal<?= element('id', $item, '') ?>').show().attr('class','modal fade in');return false;">Удалить</a>
                    </p>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</div>
