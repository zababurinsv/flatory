<?php
//vdump(array_get($handbk, 'params.fields'));
?>
<form action="" class="form" method="post">
    <div class="form-group">
        <label for="" class="control-label">Название <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= isset($post) ? array_get($post, 'name') : '' ?>">
    </div>
    <?php if (($params_fields = array_get($handbk, 'params.fields'))): ?>
        <?php foreach ($params_fields as $key => $item): ?>
            <div class="form-group">
                <label for="<?= $field_name = array_get($item, 'field_name') ?>" class="control-label"><?= array_get($item, 'label') ?> 
                    <?php if(array_get($item, 'require')): ?>
                    <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>
                <?php if(array_get($item, 'tag') === 'input'): ?>
                <input type="<?= array_get($item, 'type') ?>" name="params[<?= $field_name ?>]" class="form-control" value="<?= isset($post) ? array_get($post, 'params.' . $field_name) : '' ?>">
                <?php elseif(array_get($item, 'tag') === 'select'): $_post_val = array_get($post, 'params.' . $field_name);?>
                <select name="params[<?= $field_name ?>]" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php if(($item_data = array_get($item, 'data', []))): ?>
                    <?php foreach ($item_data as $_value => $_title): ?>
                    <option value="<?= $_value ?>"<?= $_post_val == $_value ? 'selected="selected"' : '' ?>><?= $_title ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="form-group">
        <button type="submit" class="btn btn-success">Сохранить</button>
    </div>
</form>