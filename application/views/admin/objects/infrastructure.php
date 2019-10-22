<div class="row">
    <div class="col-md-12">
        <div class="tab-pane active" id="tab7">
            <br/>
            <form method="POST" action="/admin/objects/infrastructure/<?= $object_id ?>">
                <?php if ($infrastructure_list): ?>
                    <div class="form-group" data-control="registry">
                        <label class="control-label">Собственная инфраструктура</label>
                        <div class="clearfix">
                            <?php foreach ($infrastructure_list as $group_name => $group_list): ?>
                                <div class="js-by-colomns-item">
                                    <h4 class="space_top space_bottom_none"><?= $group_name ?></h4>
                                    <?php foreach ($group_list as $item): ?>
                                        <div class="chx-w-inp" data-group="0" data-registry_id="<?= $registry_id = array_get($item, 'registry_id') ?>">
                                            <label>
                                                <input type="checkbox" name="registry[0][<?= $registry_id ?>][registry_id]" value="<?= $registry_id ?>"> <?= array_get($item, 'name') ?>
                                            </label>
                                            <input type="text" name="registry[0][<?= $registry_id ?>][description]" class="chx-w-inp-input" disabled="disabled">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                    </div>
                    <div class="form-group" data-control="registry">
                        <label class="control-label">Инфраструктура района</label>
                        <div class="clearfix">
                            <?php foreach ($infrastructure_list as $group_name => $group_list): ?>
                                <div class="js-by-colomns-item-sec">
                                    <h4 class="space_top space_bottom_none"><?= $group_name ?></h4>
                                    <?php foreach ($group_list as $item): ?>
                                        <div class="chx-w-inp" data-group="1" data-registry_id="<?= $registry_id = array_get($item, 'registry_id') ?>">
                                            <label>
                                                <input type="checkbox" name="registry[1][<?= $registry_id ?>][registry_id]" value="<?= $registry_id ?>"> <?= array_get($item, 'name') ?>
                                            </label>
                                            <input type="text" name="registry[1][<?= $registry_id ?>][description]" class="chx-w-inp-input" disabled="disabled">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <script>
                        <?php if(isset($object_registry) && $object_registry): ?>
                        // set current registry
                        (function(){
                            var r = <?= json_encode($object_registry) ?>;

                            function _setActiveRegistry(registry){
                                var c = $('[data-control="registry"]'), el;

                                if(typeof registry !== 'object' || !registry.registry_id)
                                    return;

                                el = c.find('[data-group="'+ registry.group_id +'"][data-registry_id="'+ registry.registry_id +'"]');

                                if(!$(el).length)
                                    return;
                                el.find('[type="checkbox"]').prop('checked', true);
                                el.find('.chx-w-inp-input').removeAttr('disabled').val(registry.description || '');
                            }
                            for(var k in r){
                                _setActiveRegistry(r[k]);
                            }
                        }());
                        <?php endif; ?>
                        (function () {

                            $('.js-by-colomns-item').byColumns(3).find('[data-col]').css({'min-width': '350px'});
                            $('.js-by-colomns-item-sec').byColumns(3).find('[data-col]').css({'min-width': '350px'});

                            $('.chx-w-inp-input').each(function () {
                                var p = $(this).parents('.chx-w-inp');
                                $(this).width(p.width() - p.find('label').width() - 20);
                            });

                            $('.chx-w-inp [type="checkbox"]').on('change', function (e){
                                var p = $(this).parents('.chx-w-inp');

                                if($(this).prop('checked')){
                                    p.find('.chx-w-inp-input').removeAttr('disabled');
                                } else {
                                    p.find('.chx-w-inp-input').attr('disabled', 'disabled');
                                }
                            });
                        }());
                    </script>
                <?php endif; ?>
                <div class="form-group">
                    <label for="inputText3" class="control-label">Описание</label>
                    <textarea class="form-control ckeditor" name="text" rows="3"><?= (isset($text)) ? $text : '' ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>