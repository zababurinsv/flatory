<div class="hpanel">
    <div class="panel-body">
        <form id="page-form" action="" method="post" >
            <!-- Nav tabs -->
            <!--            <ul class="nav nav-tabs space_bottom" id="myTabs" role="tablist">
                            <li class="active"><a href="#post" aria-controls="post" role="tab" data-toggle="tab-post">Запись</a></li>
                            <li><a href="#post-meta" aria-controls="post-meta" role="tab" data-toggle="tab-post">Мета-теги</a></li>
                        </ul>-->

            <!-- Tab panes -->
            <div class="_tab-content">
                <div role="tab-post" class="_tab-pane active" id="post">
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
                                var s = <?= (int) array_get($post, 'status') ?>;
                                $('[name="status"][value="' + s + '"]').prop('checked', 'checked');
                            }());
                        </script>
                    </div>

                    <?php if (isset($objects) && is_array($objects) && !!$objects && isset($type_object_relations) && is_array($type_object_relations)): ?>
                        <div class="form-group">
                            <label for="" class=" control-label">Объект <span class="text-danger">*</span></label>
                            <select name="object_id" class="form-control">
                                <option value="">Не выбрано</option>
                                <?php foreach ($objects as $o): ?>
                                    <option value="<?= array_get($o, array_get($type_object_relations, 'primary_key', ''), '') ?>"><?= array_get($o, array_get($type_object_relations, 'label', ''), '') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (array_get($post, 'object_id')): ?>
                                <script>
                                    (function () {
                                        var o = <?= (int) $post['object_id'] ?>;
                                        $('[name="object_id"] [value="' + o + '"]').attr('selected', 'selected');
                                    }());
                                </script>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="" class="control-label">Заголовок <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= element('name', $post, '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Алиас <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="alias" class="form-control js-transition" value="<?= element('alias', $post, '') ?>"/>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" data-action_global="do_alias" data-source="[name='name']" data-target="[name='alias']"><span class="glyphicon glyphicon-refresh"></span></button>
                            </span>
                        </div>
                    </div>
                    <?php if (isset($object_name) && !!$object_name): ?>
                        <div class="form-group">
                            <label for="" class="control-label">Объект </label>
                            <input type="text" class="form-control disabled" value="<?= $object_name ?>"  disabled="disabled"/>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="anons" class="control-label">Анонс <?php if (isset($is_require_anons) && $is_require_anons): ?><span class="text-danger">*</span><?php endif; ?></label>
                        <textarea id="anons" name="anons" class="form-control" rows="3"><?= element('anons', $post, '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="" class="control-label">Теги</label>
                        <ul id="methodTags"></ul>
                        <input type="hidden" name="tags" id="mySingleFieldNode" value="<?= element('tags', $post, '') ?>">
                        <?php if ($tags): ?>
                            <!--set tags-->
                            <script>FlRegister.set('tags', <?= $tags ?>);</script>
                            <!--/set tags-->
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="content" class="control-label">Описание</label>
                        <textarea id="content" class="ckeditor" name="content" cols="50" rows="15"><?= element('content', $post, '') ?></textarea>
                    </div>


                    <div role="tab-post" class="_tab-pane" id="post-meta">
                        <div class="form-group">
                            <label for="name" class="control-label">Заголовок (title)</label>
                            <input type="text" class="form-control" id="name" placeholder="" name="title" value="<?= element('title', $post, '') ?>" />
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Описание (description)</label>
                            <textarea class="form-control" name="description" rows="3"><?= element('description', $post, '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Ключевые слова (keywords)</label>
                            <textarea class="form-control" name="keywords" rows="3"><?= element('keywords', $post, '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="" class="control-label">Фото для анонса</label>
                            <?= $image_simple_upload ?>
                        </div>
                        <div class="col-md-9"></div>
                    </div>

                    <?php if (array_get($post, 'alias') && isset($is_access_preview) && !!$is_access_preview && false): ?>
                        <div class="form-group">
                            <a href="/<?= element('prefix', $category); ?>/preview/<?= $post['alias'] ?>" target="_blank">Предварительный просмотр</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </form>
        <?= $albums ?>
    </div>
</div>
<?= $widget_storage ?>
<script>
    $('#myTabs a').click(function (e) {
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
</script>





