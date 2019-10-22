<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <div class="form-group">
                <label for="title" class="control-label">Заголовок (title)</label>
                <input type="text" class="form-control" name="title" value="<?= (isset($title)) ? htmlspecialchars($title, ENT_QUOTES) : '' ?>" />
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Описание (description)</label>
                <textarea class="form-control" name="description" rows="3"><?= (isset($description)) ? $description : '' ?></textarea>
            </div>
            <div class="form-group">
                <label for="keywords" class="control-label">Ключевые слова (keywords)</label>
                <textarea class="form-control" name="keywords" rows="3"><?= (isset($keywords)) ? $keywords : '' ?></textarea>
            </div>
        </form>
    </div>
</div>
