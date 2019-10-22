<?php $status_list = array_get($content_vars, 'status_list', []); ?>
<?php foreach ($list as $it): ?>
    <tr class="on-hover-box">
        <?php
        foreach ($columns as $col => $column_meta):
            $_val = array_get($it, $col, '');
            ?>
            <td data-col="<?= $col ?>"<?php if(is_array(array_get($column_meta, 'data'))) echo html_data_attr_to_string($column_meta['data'], $it); ?>>
                <?php if ($col === 'status'): $_status = array_get($status_list, $_val, []); ?>
                    <span class="status status-<?= array_get($_status, 'alias', 'danger') ?>" title="<?= array_get($_status, 'title', 'Неизвестный статус') ?>"></span>
                <?php elseif ($col === 'delete'): ?>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger only-on-hover glyphicon glyphicon-trash pull-right" data-action_global="delete" title="Удалить"></a>
                <?php elseif ($col === 'name'): ?>
                    <a href="/admin/posts/edit/<?= (int) array_get($it, 'post_id') ?>"><?= $_val ?></a>
                    <?php
                else:
                    switch (array_get($column_meta, 'decorate')) {
                        case 'date':
                            echo!!$_val ? date('d.m.Y', strtotime($_val)) : '';
                            break;
                        default :
                            echo $_val;
                    }
                endif;
                ?>
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>