<div class="hpanel">
    <div class="panel-body">
        <form id="page-form" action="" method="POST" >
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
            <div class="form-group">
                <label for="name" class="control-label">Название <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= element('name', $post, '') ?>" />
            </div>
            <?php if ($organization_types): ?>
                <div class="form-group">
                    <div>
                        <?php foreach ($organization_types as $type): ?>
                            <div class="checkbox checkbox-inline checkbox-normal">
                                <label>
                                    <input type="checkbox" name="organization_type_id[]" <?php if (in_array(element('organization_type_id', $type), element('organization_type_id', $post, array()))) echo 'checked="checked"'; ?> value="<?= element('organization_type_id', $type) ?>"> <?= element('name', $type) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="address" class="control-label">Адрес</label>
                <input type="text" name="params[address]" class="form-control" value="<?= element('address', element('params', $post, array()), '') ?>" />
            </div>
            <div class="form-group">
                <label for="site" class="control-label">Веб-сайт</label>
                <input type="url" name="params[site]" class="form-control" value="<?= element('site', element('params', $post, array()), '') ?>" />
            </div>
            <div class="form-group">
                <label for="phone" class="control-label">Телефон</label>
                <div>
                    <div class="input-group">
                        <input type="text" name="params[phone][]" class="form-control" value="" />
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default add_item"><span class="glyphicon glyphicon-plus"></span></button>
                        </div>
                    </div>
                </div>
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
                <label for="description" class="control-label">Описание</label>
                <textarea id="description" class="ckeditor" name="description" cols="50" rows="15"><?= element('description', $post, '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="name" class="control-label">Заголовок (title)</label>
                <input type="text" class="form-control" placeholder="" name="meta_title" value="<?= element('meta_title', $post, '') ?>" />
            </div>
            <div class="form-group">
                <label for="" class="control-label">Описание (description)</label>
                <textarea class="form-control" name="meta_description" rows="3"><?= element('meta_descriptio', $post, '') ?></textarea>
            </div>
            <div class="form-group">
                <label for="" class="control-label">Ключевые слова (keywords)</label>
                <textarea class="form-control" name="meta_keywords" rows="3"><?= element('meta_keywords', $post, '') ?></textarea>
            </div>

            <div class="form-group row">
                <div class="col-md-3">
                    <label for="" class="control-label">Лого</label>
                    <?= $image_simple_upload ?>
                </div>
            </div>
        </form>
        <?= $albums ?>
    </div>
</div>
<?= $widget_storage ?>

<script>
    (function () {
        var app = {
            phones: <?= json_encode(element('phone', element('params', $post, array()), array())) ?>,
            init: function () {

                var phoneInputs = $('[name="params[phone][]"]');

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

                if (this.phones.length)
                    for (var k in this.phones) {

                        if (!phoneInputs[k])
                            this.addItem.call(phoneInputs[0]);

                        $('[name="params[phone][]"]').eq(k).val(this.phones[k]);
                    }


                app.setUpListeners();
            },
            setUpListeners: function () {
                $('.add_item').off('click').on('click', app.addItem);
            },
            addItem: function (e) {
                var el = $(this).parents('.form-group').clone();
                el.find('input').val('');
                $(this).parents('.form-group').after(el);
                app.setUpListeners();
            },
            publ: {
            }
        };
        app.init();
        return app.publ;
    }());
</script>







