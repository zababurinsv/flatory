<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal space_top" action="" method="POST" >

            <!-- Nav tabs -->
            <ul class="nav nav-tabs space_bottom" id="myTabs" role="tablist">
                <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Запись</a></li>
                <li role="presentation"><a href="#meta" aria-controls="meta" role="tab" data-toggle="tab">Мета-теги</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="main">

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Название <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" value="<?= element('name', $post, '') ?>" />
                        </div>
                    </div>
                    <?php if ($organization_types): ?>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
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
                        <label for="address" class="col-sm-2 control-label">Адрес</label>
                        <div class="col-sm-10">
                            <input type="text" name="params[address]" class="form-control" value="<?= element('address', element('params', $post, array()), '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="site" class="col-sm-2 control-label">Веб-сайт</label>
                        <div class="col-sm-10">
                            <input type="url" name="params[site]" class="form-control" value="<?= element('site', element('params', $post, array()), '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="col-sm-2 control-label">Телефон</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" name="params[phone][]" class="form-control" value="" />
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-default add_item"><span class="glyphicon glyphicon-plus"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">Описание</label>
                        <div class="col-sm-10">
                            <textarea id="description" class="ckeditor" name="description" cols="50" rows="15"><?= element('description', $post, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group space_bottom_xs">
                        <label for="" class="col-sm-2 control-label">Лого</label>
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

                </div>
                <div role="tabpanel" class="tab-pane" id="meta">
                    <div class="form-group">
                        <label for="meta_title" class="col-sm-2 control-label">Заголовок<br/>(title)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="meta_title" value="<?= element('meta_title', $post, '') ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meta_description" class="col-sm-2 control-label">Описание<br/>(description)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="meta_description" rows="3"><?= element('meta_description', $post, '') ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="meta_keywords" class="col-sm-2 control-label">Ключевые слова<br/>(keywords)</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="meta_keywords" rows="3"><?= element('meta_keywords', $post, '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-12 col-sm-10">
                    <button type="submit" class="btn btn-sm btn-success">Сохранить</button>
                    <button type="button" class="btn btn-sm btn-default sv_cl">Сохранить и закрыть</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        var app = {
            phones: <?= json_encode(element('phone', element('params', $post, array()), array())) ?>,
            init: function() {
                
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
            setUpListeners: function() {
                $('.add_item').off('click').on('click', app.addItem);
            },
            addItem: function(e) {
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







