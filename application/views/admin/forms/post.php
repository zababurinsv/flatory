<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal space_top" action="" method="POST" >
            <!-- Nav tabs -->
            <ul class="nav nav-tabs space_bottom" id="myTabs" role="tablist">
                <li role="presentation" class="active"><a href="#post" aria-controls="post" role="tab" data-toggle="tab">Запись</a></li>
                <li role="presentation"><a href="#post-meta" aria-controls="post-meta" role="tab" data-toggle="tab">Мета-теги</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="post">
                    <?php if (isset($objects) && is_array($objects) && !!$objects && isset($type_object_relations) && is_array($type_object_relations)): ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Объект <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="object_id" class="form-control">
                                    <option value="">Не выбрано</option>
                                    <?php foreach ($objects as $o): ?>
                                        <option value="<?= array_get($o, array_get($type_object_relations, 'primary_key', ''), '') ?>"><?= array_get($o, array_get($type_object_relations, 'label', ''), '') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if (array_get($post, 'object_id')): ?>
                                <script>
                                    (function() {
                                        var o = <?= (int) $post['object_id'] ?>;
                                        $('[name="object_id"] [value="' + o + '"]').attr('selected', 'selected');
                                    }());
                                </script>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Заголовок <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" id="name" value="<?= element('name', $post, '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Алиас <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="alias" class="form-control js-transition" value="<?= element('alias', $post, '') ?>"/>
                        </div>
                    </div>
                    <?php if (isset($object_name) && !!$object_name): ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Объект </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control disabled" value="<?= $object_name ?>"  disabled="disabled"/>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="anons" class="col-sm-2 control-label">Анонс <?php if(isset($is_require_anons) && $is_require_anons): ?><span class="text-danger">*</span><?php endif; ?></label>
                        <div class="col-sm-10">
                            <textarea id="anons" name="anons" class="form-control" rows="3"><?= element('anons', $post, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">Текст</label>
                        <div class="col-sm-10">
                            <textarea id="content" class="ckeditor" name="content" cols="50" rows="15"><?= element('content', $post, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group space_bottom_xs">
                        <label for="" class="col-sm-2 control-label">Фото</label>
                        <div class="col-sm-4">
                            <?= $image_simple_upload ?>
                        </div>
                        <label for="" class="col-sm-1 control-label">Теги</label>
                        <div class="col-sm-5">
                            <ul id="methodTags"></ul>
                            <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= element('tags', $post, '') ?>">
                            <?php if ($tags): ?>
                                <!--set tags-->
                                <script>FlRegister.set('tags', <?= $tags ?>);</script>
                                <!--/set tags-->
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (array_get($post,'alias') && isset($is_access_preview) && !!$is_access_preview): ?>
                        <div class="form-group">
                            <div class="col-sm-10 col-sm-offset-2">
                                <a href="/<?= element('prefix', $category); ?>/preview/<?= $post['alias'] ?>" target="_blank">Предварительный просмотр</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="status" class="col-sm-2 control-label">Статус</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control">
                                <option value="<?= MY_Model::STATUS_ACTIVE ?>">Опубликовано</option>
                                <option value="<?= MY_Model::STATUS_NOT_PUBLISHED ?>">Черновик</option>
                            </select>
                            <?php if (element('status', $post, '')): ?>
                                <script>
                                    (function() {
                                        var status = <?= (int) $post['status'] ?>;
                                        $('[name="status"] [value="' + status + '"]').attr('selected', 'selected');
                                    }());
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="post-meta">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Заголовок<br/>(title)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" placeholder="" name="title" value="<?= element('title', $post, '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Описание<br/>(description)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="description" rows="3"><?= element('description', $post, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Ключевые слова<br/>(keywords)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="keywords" rows="3"><?= element('keywords', $post, '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-12 col-sm-10">
                    <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('#myTabs a').click(function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // init tags
    $('#methodTags').tagit({
        availableTags: FlRegister.get('tags'),
        fieldName: 'tags',
        caseSensitive: false,
        singleField: true,
        singleFieldDelimiter: '|',
        singleFieldNode: $('#mySingleFieldNode'),
        allowSpaces: true
    });

    $(document).on('ready', function() {
        if (typeof String.prototype.translit === 'function') {
            $('[name="name"]').on('input', function(e) {
                var t = $('[name="alias"]'), changeClass = 'bg-warning';
                t.addClass(changeClass);
                // set value
                t.val(FlDashboardForm.prepareAlias($(this).val().translit()));
                // rm class after interval
                setTimeout(function() {
                    t.removeClass(changeClass);
                }, 1000);
            });
        }
    });
</script>





