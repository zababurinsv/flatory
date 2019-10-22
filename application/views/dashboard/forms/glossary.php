<?php ?>
<div class="hpanel">
    <div class="panel-body">
        <form id="page-form" action="" method="post" >

            <div class="form-group">
                <div>
                    <label for="status" class="control-label">Статус</label>
                </div>
                <div class="">
                    <?php foreach ($status as $val => $it): ?>
                        <label class="checkbox-inline">
                            <input type="radio" name="status" value="<?= $val ?>"> 
                            <span class="status status-<?= array_get($it, 'alias', 'danger') ?>" title="<?= $_title = array_get($it, 'title', 'Неизвестный статус') ?>"></span> 
                            <span><?= $_title ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <script>
                    (function () {
                        var s = <?= (int) array_get($item, 'status') ?>;
                        $('[name="status"][value="' + s + '"]').prop('checked', 'checked');
                    }());
                </script>
            </div>

            <div class="form-group">
                <label for="name" class="control-label">Название<span class="text-danger space_left_xs">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= element('name', $item, '') ?>">
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Описание<span class="text-danger space_left_xs">*</span></label>
                <textarea name="description" class="ckeditor" rows="10"><?= element('description', $item, '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="parent_id" class="control-label">Родительский элемент</label>
                <select name="parent_id" class="form-control">
                    <option value="0">Нет</option>
                    <?php foreach ($parents_list as $it): ?>
                        <option value="<?= element('glossary_id', $it); ?>"><?= element('name', $it); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (element('parent_id', $item, '')): ?>
                    <script>
                        (function () {
                            var parent_id = <?= (int) $item['parent_id'] ?>;
                            $('[name="parent_id"] [value="' + parent_id + '"]').attr('selected', 'selected');
                        }());
                    </script>
                <?php endif; ?>
            </div>

            <div class="form-group row">
                <div class="col-sm-12">
                    <label for="" class="control-label">Связь</label>
                </div>

                <div class="col-sm-6">
                    <select name="handbk_id" class="form-control">
                        <option value="">Не выбрано</option>
                        <?php foreach ($handbks as $it): ?>
                            <option value="<?= element('handbk_id', $it) ?>"><?= element('name', $it) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (element('handbk_id', $item, '')): ?>
                        <script>
                            FlRegister.set('handbk_id', <?= (int) $item['handbk_id'] ?>);
                        </script>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <select name="object_id" class="form-control">
                        <option value="">Не выбрано</option>
                    </select>
                    <img id="handbk_object_loader" src="/images/loader_sm.gif" style="position: absolute; right: -36px; top: 0; display: none;">
                    <?php if (element('object_id', $item, '')): ?>
                        <script>
                            FlRegister.set('object_id', <?= (int) $item['object_id'] ?>);
                        </script>
                    <?php endif; ?>
                </div>
            </div>


            <div class="form-group">
                <label for="meta_title" class="control-label">Заголовок (title)</label>
                    <input type="text" class="form-control" name="meta_title" value="<?= element('meta_title', $item, '') ?>" />
            </div>
            <div class="form-group">
                <label for="meta_description" class="control-label">Описание (description)</label>
                    <textarea class="form-control" name="meta_description" rows="3"><?= element('meta_description', $item, '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="meta_keywords" class="control-label">Ключевые слова (keywords)</label>
                    <textarea class="form-control" name="meta_keywords" rows="3"><?= element('meta_keywords', $item, '') ?></textarea>
            </div>
        </form>
        <?= $albums ?>
    </div>
</div>
<?= $widget_storage ?>

