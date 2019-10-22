<?php foreach ($list as $it): ?>
    <tr>
        <?php
        foreach ($columns as $col => $column_meta):
            $_val = array_get($it, $col, '');
            ?>
            <td>
                <?php if ($col === 'name'): ?>
                <a href="/admin/posts/edit/<?= (int)array_get($it, 'post_id') ?>"><?= $_val ?></a>
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