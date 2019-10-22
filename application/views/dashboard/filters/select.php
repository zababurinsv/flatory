<div class="form-group">
    <label for="<?= $field_name ?>" class="control-label"><?= $title ?></label>
    <select name="<?= $field_name ?>" class="form-control">
        <option value="">Не выбрано</option>
        <?php foreach ($data as $it): ?>
        <option value="<?= array_get($it, 'value') ?>"><?= array_get($it, 'title') ?></option>
        <?php endforeach; ?>
    </select>
</div>