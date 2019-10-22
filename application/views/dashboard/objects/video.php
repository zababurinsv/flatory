<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <div class="form-group">
                <label for="">Видео</label>
                <textarea name="video" class="form-control ckeditor" rows="10"><?= array_get($object, 'video') ?></textarea>
            </div>
        </form>
    </div>
</div>
