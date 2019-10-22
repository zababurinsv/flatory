<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" method="post" id="object-form">
            <?php foreach ($organizations as $type_id => $it): ?>
                    <div class="form-group">
                        <label for=""><?= $it['name'] ?></label>
                        <select name="organizations[<?= $type_id ?>][]" class="form-control js-select2" multiple="multiple">
                            <?php foreach ($it['list'] as $item): ?>
                            <option value="<?= $item['organization_id'] ?>"><?= $item['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            <?php endforeach; ?>
            <script>
                // select current
                (function (){
                    var o = <?= json_encode($organizations) ?>, s;
                    for(var tid in o){
                        s = $('[name="organizations['+ tid +'][]"]');
                        for(var it in o[tid].current){
                            s.find('[value="'+ o[tid].current[it].organization_id +'"]').attr('selected', 'selected');
                        }
                    }
                    
                }());
            </script>
        </form>
    </div>
</div>

