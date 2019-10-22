<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form role="form" method="post" action="" id="object-form">
            <div class="form-group">
                <label for="layout_plan" class="control-label">Описание</label>
                <textarea class="form-control ckeditor" name="layout_plan" rows="3" placeholder="Описание"><?= array_get($object, 'layout_plan', ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="layout_plan_map" class="control-label">Код карты (размер должен быть <b>584x432</b>) <a data-target="#layout_plan_map" class="text-info space_left_xs js-panorama-open" data-name="Текущая карта">предварительный просмотр</a> (доступно после сохранения)</label>
                <textarea class="form-control" name="layout_plan_map" rows="3" placeholder="Код карты"><?= $layout_plan_map = array_get($object, 'layout_plan_map', ''); ?></textarea>
            </div>
            <div class="form-group" id="layout_plan_map" style="display: none;">
                <label class="control-label">Текущая карта</label>
                <div>
                    <?= $layout_plan_map ?>
                </div>
            </div>
        </form>
        <?= $albums ?>
    </div>
</div>
<?= $widget_storage ?>


