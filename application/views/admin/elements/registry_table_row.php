<?php foreach ($list as $it): ?>
    <tr>
        <?php
        foreach ($columns as $col => $column_meta):
            $_val = array_get($it, $col, '');
            ?>
            <td data-col="<?= $col ?>">
                <?php if ($col === 'name'): ?>
                <a href="/admin/registry/edit/<?= (int)array_get($it, 'registry_id') ?>"><?= $_val ?></a>
                <?php elseif($col === 'delete'): ?>
                <button type="button" title="Удалить" class="btn btn-xs btn-danger d_it pull-right" data-id="<?= (int)array_get($it, 'registry_id') ?>" data-name="<?= array_get($it, 'name') ?>">
                        <span class="glyphicon glyphicon-trash"></span>
                </button>
                <?php
                else:
                    switch (array_get($column_meta, 'decorate')) {
                        case 'date':
                            echo !!$_val ? date('d.m.Y', strtotime($_val)) : '';
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