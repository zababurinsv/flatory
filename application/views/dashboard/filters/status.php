<div class="form-group">
    <div>
        <label for="status" class="control-label">Статус</label>
    </div>
    <div class="space_left" style="min-height: 34px;">
        <?php foreach ($status as $val => $it): ?>
            <label class="checkbox-inline">
                <input type="checkbox" name="status[]" value="<?= $val ?>"> 
                <span class="status status-<?= array_get($it, 'alias', 'danger') ?>" title="<?= $_title = array_get($it, 'title', 'Неизвестный статус') ?>"></span> 
                <span><?= $_title ?></span>
            </label>
        <?php endforeach; ?>
    </div>
</div>