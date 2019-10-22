<div class="alert alert-info text-center">
    <h3>Внимание, размеры панорам должны быть <b>584x432</b></h3>
</div>
<form action="" method="post" class="space_bottom_xxl">
    <?php foreach ($panoram_types as $field => $name): ?>
        <div class="form-group">
            <label for="<?= $field ?>" class="control-label">Код панорамы <?= $name ?></label>
            <textarea class="form-control" name="<?= $field ?>" rows="3" placeholder="Код панорамы <?= $name ?>"><?= array_get($object, $field, ''); ?></textarea>
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-success">Сохранить</button>
</form>
<ul class="list-inline">
<?php foreach ($panoram_types as $field => $name): ?>
    <?php if (array_get($object, $field)): ?> 
        <li>
        <h4 class="space_top">Текущая панорама <?= $name ?></h4>
        <div><?= $object[$field] ?></div>
        </li>
    <?php endif; ?>
<?php endforeach; ?>
</ul>
