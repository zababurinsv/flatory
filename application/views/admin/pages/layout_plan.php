<div class="row">
    <div class="col-md-12">
        <form class="" role="form" method="post">
            <div class="form-group">
                <label for="layout_plan" class="control-label">Описание</label>
                <textarea class="form-control ckeditor" name="layout_plan" rows="3" placeholder="Описание"><?= array_get($object, 'layout_plan', ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="layout_plan_map" class="control-label">Код карты (размер должен быть <b>584x432</b>)</label>
                <textarea class="form-control" name="layout_plan_map" rows="3" placeholder="Код карты"><?= $layout_plan_map = array_get($object, 'layout_plan_map', ''); ?></textarea>
                <?php if ($layout_plan_map): ?>
                    <h4 class="space_top">Текущая карта</h4>
                    <div><?= $layout_plan_map ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </form>
    </div>
</div>


