<?php
//vdump($list);
?>
<?php if (!empty($list)): ?>
    <?php if (isset($pagination)): ?> 
        <div class="pull-right">
            <?= $pagination ?>
        </div>
    <?php endif; ?>
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" name="check_all_files"></th>
                <th></th>
                <th><span class="glyphicon glyphicon-tint text-info"></span></th>
                <th><a href="javascript:void(0)" class="sort_link" data-by="original_name">Название файла</a></th>
                <th>Описание</th>
                <th>Источник</th>
                <th>Раздел</th>
                <th>Теги</th>
                <th><a href="javascript:void(0)" class="sort_link" data-by="created">Дата создания</a></th>
                <th><a href="javascript:void(0)" class="sort_link" data-by="updated">Дата изменения</a></th>
                <th><a href="javascript:void(0)" class="sort_link" data-by="total_size">Вес</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
                <tr>
                    <td style="width:20px"><input type="checkbox" name="file_id[]" value="<?= element('file_id', $item, 0) ?>"></td>
                    <td style="width:100px">
                        <a href="/images/original/<?= element('file_name', $item, 'no_photo.jpg') ?>" class="fancybox-thumbs" title="<?= element('alt', $item, '') ?>">
                            <img src="/images/thumbs/<?= element('file_name', $item, 'plug.jpg') ?>" class="thumbnail">
                        </a>
                    </td>
                    <td style="width:20px">
                        <?php if(element('is_watermark', $item, FALSE)): ?>
                        <span class="glyphicon glyphicon-tint text-info"></span>
                        <?php endif; ?>
                    </td>
                    <td><a href="<?= $path . 'card/' . element('name', $item, '') ?>"><?= element('original_name', $item, '') ?></a></td>
                    <td><?= element('alt', $item, '') ?></td>
                    <td><?= element('description', $item, '') ?></td>
                    <td>
                        <ul class="list-unstyled">
                            <?php foreach (element('categories', $item, array()) as $it): ?>
                                <li><?= $it ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <?php foreach (element('tags', $item, array()) as $it): ?>
                                <li><?= $it ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td><?= date("d.m.Y H:i:s", strtotime(element('created', $item))) ?></td>
                    <td><?= date("d.m.Y H:i:s", strtotime(element('updated', $item))) ?></td>
                    <td><?= byte_format(element('total_size', $item, 0)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="row">
        <div class="col-md-6">
            <!--<a href="/admin/upload" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Загрузить</a>-->
        </div>
        <div class="col-md-6">
            <?php if (isset($pagination)): ?> 
                <div class="pull-right">
                    <?= $pagination ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?> 
    <div class="alert alert-info">Упс! Ничего не найдено.</div>
    <script type="text/javascript">
        $(document).on('ready', function() {
            $('.panel-body-cover .panel-heading').trigger('click');
        });
    </script>
<?php endif; ?> 