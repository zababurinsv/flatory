<?php
//vdump($list);
?>
<?php if (!empty($list)): ?>
    <a href="/admin/upload" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Загрузить</a>
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
                        <div class="file_icon">
                            <img src="/images/document-icon.png" class="thumbnail">
                            <i class="ext_<?= element('ext', $item, '') ?>"><?= element('ext', $item, '') ?></i>
                        </div>
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
