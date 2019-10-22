<?php
//vdump($file_involves);
?>
<?php if ((int) element('file_type_id', $file, 0) === Storage_Files::FILE_IMAGE): ?>
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">Настройки изображения</div>
                <div class="panel-body">

                    <div class="col-md-3">
                        <img src="/images/original/<?= $file['file_name'] ?>" alt="" class="thumbnail space_bottom">
                        <ul class="list-unstyled">
                            <li><b>Размеры:</b> <?= $file['x'] . 'x' . $file['y'] ?></li>
                            <li><b>Размер файла:</b> <?= byte_format($file['size']) ?></li>
                            <li><b>Размер всех файлов:</b> <?= byte_format($total_size) ?></li>
                            <li><b>Дата создания:</b> <?= date("d.m.Y H:i", strtotime($file['created'])) ?></li>
                            <li><b>Дата обновления:</b> <?= date("d.m.Y H:i", strtotime($file['updated'])) ?></li>
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <!--edit size-->
                        <b class="space_top">Редактировать размеры</b>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Размер <span class="glyphicon glyphicon-link"></span></th>
                                    <th>Размер файла</th>
                                    <th>Водяной знак</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($file_proportions as $item): ?>
                                    <tr data-proportion="<?= $item['proportion_id'] ?>">
                                        <th><a href="/images/<?= $item['x'] . 'x' . $item['y'] . '/' . $file['file_name'] ?>" target="_blank"><?= $item['x'] . 'x' . $item['y'] ?></a></th>
                                        <th><?= byte_format($item['size']) ?></th>
                                        <th><input type="checkbox" name="is_watermark" data-watermark="<?= (int) $item['is_watermark'] ?>" <?php if ($item['is_watermark']): ?>checked="checked"<?php endif; ?>></th>
                                        <th>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-danger delete_proportion pull-right space_left"><span class="glyphicon glyphicon-trash"></span></a>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-warning save_proportion pull-right disabled">Сохранить</a>
                                        </th>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($file_proportions)): ?>
                            <div class="alert alert-info" id="image_no_size">Изображение доступно только в оригинале. Добавьте необходимый размер.</div>
                        <?php endif; ?>
                        <!--/edit size-->
                        <?php if (!empty($proportions)): ?>
                            <!--add size-->
                            <b class="space_top">Добавить размер</b>
                            <form action="" id="image_add_size">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Размер</th>
                                            <th>Водяной знак</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select name="proportion_id" class="form-control">
                                                    <?php foreach ($proportions as $item): ?>
                                                        <option value="<?= $item['proportion_id'] ?>"><?= $item['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input type="checkbox" name="is_watermark" value="1"></td>
                                            <td>
                                                <input type="hidden" name="file_id" value="<?= $file['file_id'] ?>">
                                                <a href="javascript:void(0)" class="btn btn-success pull-right"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                            <!--/ add size-->
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Участие файла</div>
                <div class="panel-body">
                    <?php if (!empty($file_involves)): ?>
                    <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Раздел</th>
                                    <th colspan="2">Путь</th>
                                </tr>
                            </thead>
                            <?php foreach ($file_involves as $involves): ?>
                                <tr>
                                    <th><?= $involves['name'] ?></th>
                                    <th><a href="<?= $involves['uri_adm'] ?>" target="_blank">Админ</a></th>
                                    <th><a href="<?= $involves['uri'] ?>" target="_blank">Сайт</a></th>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                    <div class="alert alert-info">Файл нигде не используется.</div>
                    <a href="javascript:void(0)" class="btn btn-danger center-block delete_item" data-file_id="<?= $file['file_id'] ?>">Удалить безвозвратно</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <form action="post" id="storage_card">
        <div class="col-md-7">        
            <div class="form-group">
                <label>Название</label>
                <input type="text" name="original_name" class="form-control" value="<?= $file['original_name'] ?>">
            </div>
            <div class="form-group">
                <label>Описание</label>
                <textarea name="alt" class="form-control" ><?= $file['alt'] ?></textarea>
            </div>
            <div class="form-group">
                <label>Источник</label>
                <textarea name="description" class="form-control ckeditor" ><?= $file['description'] ?></textarea>
            </div>
            <div class="form-group">
                <input type="hidden" name="file_id"  value="<?= $file['file_id'] ?>">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
        <div class="col-md-5">
            <?php if ((int) element('file_type_id', $file, 0) !== Storage_Files::FILE_IMAGE): ?>
            <div class="panel panel-default">
                <div class="panel-heading">Участие файла</div>
                <div class="panel-body">
                    <?php if (!empty($file_involves)): ?>
                    <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Раздел</th>
                                    <th colspan="2">Путь</th>
                                </tr>
                            </thead>
                            <?php foreach ($file_involves as $involves): ?>
                                <tr>
                                    <th><?= $involves['name'] ?></th>
                                    <th><a href="<?= $involves['uri_adm'] ?>" target="_blank">Админ</a></th>
                                    <th><a href="<?= $involves['uri'] ?>" target="_blank">Сайт</a></th>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                    <div class="alert alert-info">Файл нигде не используется.</div>
                    <a href="javascript:void(0)" class="btn btn-danger center-block delete_item">Удалить безвозвратно</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label>Теги</label>
                <ul id="methodTags"></ul>
                <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= $image_tags ?>">
            </div>
        </div>
    </form>
</div>
<!--set tags-->
<script>
<?php if ($tags): ?>
        FlRegister.set('tags', <?= $tags ?>);
<?php endif; ?>
</script>
<!--/set tags-->