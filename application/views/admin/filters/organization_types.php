<?php if ($organization_types): ?>
    <div class="form-group">
        <label for="organization_type_id" class="col-sm-3 control-label">Тип организации</label>
        <div class="col-sm-9">
            <?php foreach ($organization_types as $type): ?>
                <div class="checkbox checkbox-inline checkbox-normal">
                    <label>
                        <input type="checkbox" name="organization_type_id[]" value="<?= element('organization_type_id', $type) ?>"> <?= element('name', $type) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>