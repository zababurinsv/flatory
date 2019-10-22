<?php if(isset($filters) && $filters): ?>
<div class="row">
    <div class="col-md-12"><?= $filters ?></div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6"><?php echo isset($btn_nav) ? $btn_nav : ''; ?></div>
    <div class="col-md-6"><div class="pull-right"><?= $pagination ?></div></div>
</div>
<?php if (isset($list) && is_array($list)): ?>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col => $column_meta): // $_attrs = array_get($column_meta, 'attr', [])?>
                        <th><a href="javascript:void(0)" <?php if(($attr_title =  array_get($column_meta, 'attr.title'))) echo 'title="' . $attr_title . '" '; ?>class="sort_link" data-by="<?= $col ?>"><?= array_get($column_meta, 'title', '') ?></a></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($path_table_row_template) && !!$path_table_row_template): ?>
                        <?php $this->load->view($path_table_row_template, $list); ?>
                    <?php else: ?>
                        <?php foreach ($list as $it): ?>
                            <tr>
                                <?php foreach ($columns as $col => $column_meta): ?>
                                    <td><?php
                                        $val = array_get($it, $col, '');
                                        switch (array_get($column_meta, 'decorate')) {
                                            case 'date':
                                                echo!!$val ? date('d.m.Y', strtotime($val)) : '';
                                                break;
                                            default :
                                                echo $val;
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6"><div class="pull-right"><?= $pagination ?></div></div>
</div>