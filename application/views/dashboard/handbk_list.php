<?php if (isset($form) && $form): ?>
    <div class="hpanel panel-body-cover">
        <div class="panel-heading hbuilt">
            Фильтр
        </div>
        <div class="panel-body">
            <?= $form ?>
        </div>
    </div>
<?php endif; ?>
<div class="hpanel">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <a href="/admin/handbk/add/<?= $hb ?>" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
            </div>
            <div class="col-md-6">
                <?php if (isset($pagination)): ?>
                    <div class="pull-right">
                        <?= $pagination ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if (isset($list) && isset($list_fields)): ?>
                    <table class="table table-hover list_handbk">
                        <thead>
                        <tr>
                            <?php foreach ($list_fields as $key => $field): ?>
                                <th class="header"><a href="javascript:void(0)" class="sort_link" data-by="<?= $key ?>"><?= $key !== 'delete' ? $field : '' ?></a></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $key => $item): ?>
                            <tr class="on-hover-box">
                                <?php foreach ($list_fields as $field => $column_meta): ?>
                                    <?php if ($field === 'delete'): ?>
                                        <td class="list_element__<?= $field ?>" <?php if (is_array(array_get($column_meta, 'data'))) echo html_data_attr_to_string($column_meta['data'], $item); ?>>
                                            <a href="javascript:void(0)" class="btn btn-xs btn-danger only-on-hover glyphicon glyphicon-trash pull-right" data-action_global="delete" title="Удалить"></a>
                                        </td>
                                    <?php else: ?>
                                        <td class="list_element__<?= $field ?>">
                                            <?php if ($field === 'status'): $_status = array_get($status_list, $item[$field], []); ?>
                                                <span class="status status-<?= array_get($_status, 'alias', 'danger') ?>" title="<?= array_get($_status, 'title', 'Неизвестный статус') ?>"></span>
                                            <?php elseif ($field === 'metro_lines'): ?>
                                                <?php $lines_names = [];
                                                foreach ($item[$field] as $lines)
                                                    $lines_names[] = array_get($lines, 'name');
                                                echo implode(', ', $lines_names); ?>
                                            <?php elseif ($field === 'color'): ?>
                                                <?php if (array_get($item, $field)): ?>
                                                    <img src="/images/metro/metro.png" style="background: <?= $item[$field] ?>" />
                                                <?php elseif (array_get($item, 'metro_lines')): ?>
                                                    <?php foreach ($item['metro_lines'] as $lines): ?>
                                                        <img src="/images/metro/metro.png" style="background: <?= array_get($lines, 'color', '#fff') ?>" />
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php elseif ($field === 'glossary_relation'):
                                                if (element('glossary_id', $item[$field])):
                                                    ?>
                                                    <a href="/admin/glossary/edit/<?= $item[$field]['glossary_id'] ?>"><?= $item[$field]['name'] ?></a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= $item[$field] ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        /**
         * Add links for Name
         * @type String
         */
        var idField = '<?= array_search('ID', isset($list_fields) ? $list_fields : array() ) ?>',
            nameField = '<?= array_search('Название', isset($list_fields) ? $list_fields : array()) ?>',
            prefix = 'list_element__',
            uri = '<?= $uri ?>',
            hb = '<?= $hb ?>';

        $('.list_handbk tbody tr').each(function (a, b) {
            var id = Number($(b).find('.' + prefix + idField).text()),
                name = $(b).find('.' + prefix + nameField).text();
            $(b).find('.' + prefix + nameField).empty();

            $('<a>', {
                text: name,
                title: name,
                href: uri + '/' + hb + '/' + id
            }).appendTo($(b).find('.' + prefix + nameField));
        });

        <?php if (isset($sort) && $sort): ?>
        $(document).on('ready', function () {
            FlDashboardForm.setActiveSortBy(<?= json_encode($sort) ?>);
        });
        <?php endif; ?>
    }());
</script>