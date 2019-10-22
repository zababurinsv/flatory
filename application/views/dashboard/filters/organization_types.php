<?php if ($organization_types): ?>
    <div class="form-group">
        <label for="organization_type_id" class="control-label space_none">Тип организации</label>
        <div style="min-height: 34px;">
            <?php foreach ($organization_types as $type): ?>
                <div class="checkbox checkbox-inline checkbox-normal" style="margin-top: 0;">
                    <label>
                        <input type="checkbox" name="organization_type_id[]" value="<?= element('organization_type_id', $type) ?>"> <?= element('name', $type) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>