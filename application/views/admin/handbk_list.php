<?php if(isset($form) && $form): ?>
<div class="row">
    <div class="col-md-6">
        <?= $form ?>
        <hr>
    </div>
    <div class="col-md-6"></div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6">
        <a href="/admin/handbk/add/<?= $hb ?>" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span> Добавить</a>
    </div>
    <div class="col-md-6">
        <?php if(isset($pagination)): ?> 
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
                        <th><a href="javascript:void(0)" class="sort_link" data-by="<?= $key ?>"><?= $field ?></a></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fields = array_keys($list_fields);
//                    vdump($list);
                    foreach ($list as $key => $item): ?>
                        <tr>
                            <?php
                                foreach ($fields as $field): ?>
                                    <td class="list_element__<?= $field ?>">
                                        <?php if($field === 'status'): 
                                            if($item[$field] == 1): ?>
                                            <div class="status_mark status_mark_success"><span class="glyphicon glyphicon-ok"></span></div>
                                            <?php else: ?>
                                            <div class="status_mark status_mark_danger"><span class="glyphicon glyphicon-remove"></span></div>
                                            <?php endif; ?>
                                        <?php elseif($field === 'metro_lines'): ?>
                                            <?php $lines_names = []; foreach ($item[$field] as $lines) $lines_names[] = array_get($lines, 'name'); echo implode(', ', $lines_names); ?>  
                                        <?php elseif($field === 'color'): ?>
                                            <?php if(array_get($item, $field)): ?>
                                            <img src="/images/metro/metro.png" style="background: <?= $item[$field] ?>" />
                                            <?php elseif(array_get($item, 'metro_lines')): ?>
                                            <?php foreach ($item['metro_lines'] as $lines): ?>  
                                            <img src="/images/metro/metro.png" style="background: <?= array_get($lines, 'color', '#fff') ?>" />
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php elseif($field === 'glossary_relation'): 
                                            if(element('glossary_id', $item[$field])):?>
                                            <a href="/admin/glossary/edit/<?= $item[$field]['glossary_id'] ?>"><?= $item[$field]['name'] ?></a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                        <?= $item[$field] ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                            <!--<td><a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right delete_item"><span class="glyphicon glyphicon-remove"></span></a></td>-->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<script>
    (function() {
        /**
         * Add links for Name
         * @type String
         */
        var idField = '<?= array_search('ID', isset($list_fields)? $list_fields : array() ) ?>',
                nameField = '<?= array_search('Название', isset($list_fields)? $list_fields : array()) ?>',
                prefix = 'list_element__',
                uri = '<?= $uri ?>',
                hb = '<?= $hb ?>';
       
        $('.list_handbk tbody tr').each(function(a, b) {
            var id = Number($(b).find('.' + prefix + idField).text()),
                name = $(b).find('.' + prefix + nameField).text();
                $(b).find('.' + prefix + nameField).empty();

            $('<a>', {
                text: name,
                title: name,
                href: uri + '/' + hb + '/' + id,
            }).appendTo($(b).find('.' + prefix + nameField));
        });
    }());
</script>