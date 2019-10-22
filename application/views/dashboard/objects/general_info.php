<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <div class="form-group">
                <?php foreach ($status_list as $it_status => $it_meta): ?>
                    <label class="radio-inline">
                        <input type="radio" name="status" value="<?= $it_status ?>"<?= $object_status === $it_status ? ' checked="checked"' : '' ?>>
                        <span class="status status-<?= array_get($it_meta, 'alias') ?>" title="<?= array_get($it_meta, 'title') ?>"></span>
                        <span><?= array_get($it_meta, 'title') ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <label for="name" class="control-label">Застройщик<span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars(array_get($object, 'name', ''), ENT_QUOTES) ?>">
            </div>
            <div class="form-group">
                <label for="alias" class="control-label">Срок Сдачи <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="alias" id="alias" class="form-control js-transition" value="<?= array_get($object, 'alias') ?>"/>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" data-action_global="do_alias" data-source="[name='name']" data-target="[name='alias']"><span class="glyphicon glyphicon-refresh"></span></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Класс жилья</label>
                <textarea class="form-control" name="class" rows="2"><?= array_get($object, 'class') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Тип жилья</label>
                <textarea class="form-control" name="type" rows="2"><?= array_get($object, 'type') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Метро</label>
                <textarea class="form-control" name="metro" rows="2"><?= array_get($object, 'metro') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">ГЕО</label>
                <textarea class="form-control" name="geo" rows="2"><?= array_get($object, 'geo') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Планироки</label>
                <textarea class="form-control" name="plans" rows="2"><?= array_get($object, 'plans') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Цена от</label>
                <textarea class="form-control" name="price" rows="2"><?= array_get($object, 'price') ?></textarea>
            </div>
            <div class="form-group">
                <label for="anons" class="control-label">Отделка</label>
                <textarea class="form-control" name="finish" rows="2"><?= array_get($object, 'finish') ?></textarea>
            </div>
            <div class="form-group">
                <label class="control-label">Теги</label>
                <ul id="methodTags" class="space_none"></ul>
                <input type="hidden" name="tags" id="object-tags" value="<?= element('tags', $object, '') ?>">
                <?php if ($tags): ?>
                    <!--set tags-->
                    <script>FlRegister.set('tags', <?= $tags ?>);</script>
                    <!--/set tags-->
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Дополнительная информация</label>
                <textarea class="form-control ckeditor" id="description" name="description" rows="3"><?= array_get($object, 'description') ?></textarea>
            </div>
            <div class="form-group">
                <label class="control-label">Фото <a href="javascript:void(0)" id="init_widget_storage" class="text-info space_left_xs">добавить</a></label>
                <div id="object-images">
                    <?= $view_images ?>
                </div>
            </div>
        </form>
        <script>
            (function () {
                // init tags
                $('#object-tags').tagit({
                    availableTags: FlRegister.get('tags'),
                    fieldName: 'tags',
                    caseSensitive: false,
                    singleField: true,
                    singleFieldDelimiter: '|',
                    singleFieldNode: $('#mySingleFieldNode'),
                    allowSpaces: true
                });
            }());
        </script>
    </div>
</div>

<?= $widget_storage ?>