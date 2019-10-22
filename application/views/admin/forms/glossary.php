<?php ?>
<div class="row">
    <div class="col-md-12">
        <form action="" class="form form-horizontal" method="post">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs space_bottom" id="myTabs" role="tablist">
                <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Запись</a></li>
                <li role="presentation"><a href="#meta" aria-controls="meta" role="tab" data-toggle="tab">Мета-теги</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="main">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Название<span class="text-danger space_left_xs">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" value="<?= element('name', $item, '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">Описание<span class="text-danger space_left_xs">*</span></label>
                        <div class="col-sm-10">
                            <textarea name="description" class="ckeditor" rows="10"><?= element('description', $item, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="parent_id" class="col-sm-2 control-label">Родительский элемент</label>
                        <div class="col-sm-10">
                            <select name="parent_id" class="form-control">
                                <option value="0">Нет</option>
                                <?php foreach ($parents_list as $it): ?>
                                    <option value="<?= element('glossary_id', $it); ?>"><?= element('name', $it); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (element('parent_id', $item, '')): ?>
                                <script>
                                    (function() {
                                        var parent_id = <?= (int) $item['parent_id'] ?>;
                                        $('[name="parent_id"] [value="' + parent_id + '"]').attr('selected', 'selected');
                                    }());
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="col-sm-2 control-label">Статус</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control">
                                <?php foreach ($statuses as $status_id => $title): ?>
                                    <option value="<?= $status_id ?>"><?= $title ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (element('status', $item, '')): ?>
                                <script>
                                    (function() {
                                        var status = <?= (int) $item['status'] ?>;
                                        $('[name="status"] [value="' + status + '"]').attr('selected', 'selected');
                                    }());
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Связь</label>
                        <div class="col-sm-5">
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
                        <div class="col-sm-5">
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
                </div>
                <div role="tabpanel" class="tab-pane" id="meta">
                    <div class="form-group">
                        <label for="meta_title" class="col-sm-2 control-label">Заголовок<br/>(title)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="meta_title" value="<?= element('meta_title', $item, '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meta_description" class="col-sm-2 control-label">Описание<br/>(description)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="meta_description" rows="3"><?= element('meta_description', $item, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords" class="col-sm-2 control-label">Ключевые слова<br/>(keywords)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="meta_keywords" rows="3"><?= element('meta_keywords', $item, '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-12 col-sm-10">
                    <button type="submit" class="btn btn-sm btn-success space_right_xs">Сохранить</button>
                    <button type="button" class="btn btn-sm btn-default sv_cl">Сохранить и закрыть</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#myTabs a').click(function(e) {
        e.preventDefault()
        $(this).tab('show')
    });
</script>

