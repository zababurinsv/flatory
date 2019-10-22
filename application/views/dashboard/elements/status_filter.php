<?php
/**
 * Filter element status[]
 * @param array $status_list - list of statuses
 * @param array $data - form content 
 */
?>
<div class="form-group">
    <div>
        <label for="status" class="control-label">Статус</label>
    </div>
    <div class="space_left" style="min-height: 34px;">
        <?php foreach ($status_list as $val => $it): ?>
            <label class="checkbox-inline">
                <input type="checkbox" name="status[]" value="<?= $val ?>"> 
                <span class="status status-<?= array_get($it, 'alias', 'danger') ?>" title="<?= $_title = array_get($it, 'title', 'Неизвестный статус') ?>"></span> 
                <span><?= $_title ?></span>
            </label>
        <?php endforeach; ?>
    </div>
    <?php if(isset($data) && is_array($data) && !!$data): ?>
    <script>
        (function() {
            var d = <?= json_encode($data) ?>;
            
            if(typeof d.status === 'object'){
                for(var k in d.status) {
                    $('[name="status[]"][value="'+ d.status[k] +'"]').prop('checked', true);
                }
            }
        }());
    </script>
    <?php endif; ?>
</div>