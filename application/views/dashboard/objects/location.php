<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <script>
                FlRegister.set('fields', <?= json_encode($object) ?>);
            </script>
            <div class="form-group">
                <label for="adres" class="control-label">Адрес *</label>
                <input type="text" name="adres" class="form-control" value="">                
            </div>
            <div class="form-group">
                <label for="zone_id" class="control-label">Регион *</label>
                <select name="zone_id" id="zone_id" class="form-control">
                    <option value="">Не выбрано</option>
                    <?php foreach ($zone as $key => $item): ?>
                        <option value="<?= $item->zone_id; ?>" data-zone="<?= $key ?>"><?= $item->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!--msk-->
            <div id="moscow" style="display: none;" data-zone-content="MOW">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label">Округ</label>
                        <select id="district" name="district_id" data-zone="MOW" data-relation="square" class="form-control js-to-select2">
                            <option value="0">Не выбрано</option>
                            <?php foreach ($district as $item): ?>
                                <option value="<?= $item->district_id ?>"><?= $item->short_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class=" control-label">Район</label>
                        <select id="square" name="square_id" class="form-control js-to-select2">
                            <option value="0">Не выбрано</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--\msk-->

            <!--mo-->
            <div id="moscow_region" style="display: none;" data-zone-content="MOS">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label">Направление</label>
                        <select name="geo_direction_id" id="geo_direction_id" class="form-control js-to-select2">
                            <?php foreach ($direction as $val): ?>
                                <option <?php if (isset($ids)) if ($val->id == $ids['id_direction']) echo 'selected=""'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label">Город</label>
                        <select name="populated_locality_id" id="populated_locality_id" class="form-control js-to-select2">
                            <option value="0">Не выбрано</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label">Район / Округ</label>
                        <select name="geo_area_id" id="geo_area_id" class="form-control js-to-select2">
                            <option value="0">Не выбрано</option>
                            <?php foreach ($geo_area as $item): ?>
                                <option value="<?= $item['geo_area_id'] ?>"><?= $item['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label">Расстояние от МКАД</label>
                        <select id="distance_to_mkad" class="form-control" name="id_distance_to_mkad">
                            <?php foreach ($distance_to_mkad as $val): ?>
                                <option <?php if (isset($ids)) if ($val->id == $ids['id_distance_to_mkad']) echo 'selected=""'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <!--\mo-->

            <!--metro-->
            <div id="metro">
                <div class="row" data-fg="metro">
                    <div class="col-sm-6">
                        <label class="control-label">Метро <a class="text-info space_left_xs" data-copy-fg="#tpl__metro">добавить</a></label>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label">Расстояние от метро</label>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label">Время пешком</label>
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label">Время на авто</label>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <select name="meta_metro[0][metro_id]" class="form-control js-select2">
                            <option value="">Не выбрано</option>
                            <?php foreach ($metro as $it): ?>
                                <option value="<?= $it['metro_station_id'] ?>"><?= $it['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a class="rm-form-group" data-type="metro"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                    <div class="col-sm-2">                        
                        <input type="number" name="meta_metro[0][distance]" class="form-control" step="0.1" min="0" placeholder="км">
                    </div>
                    <div class="col-sm-2">
                        <input type="number" name="meta_metro[0][walking_time]" class="form-control" step="1" min="0" placeholder="мин">
                    </div>
                    <div class="col-sm-2">                        
                        <input type="number" name="meta_metro[0][drive_time]" class="form-control" step="1" min="0" placeholder="мин">
                    </div>
                </div>
                <script type="text/template" id="tpl__metro">
                    <div class="row form-group">
                    <div class="col-sm-6">
                    <select name="meta_metro[{{=it.index}}][metro_id]" class="form-control js-select2">
                    <option value="">Не выбрано</option>
                    <?php foreach ($metro as $it): ?>
                        <option value="<?= $it['metro_station_id'] ?>"><?= $it['name'] ?></option>
                    <?php endforeach; ?>
                    </select>
                    <a class="rm-form-group" data-type="metro"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                    <div class="col-sm-2">
                    <input type="number" name="meta_metro[{{=it.index}}][distance]" class="form-control" step="0.1" min="0" placeholder="км" value="{{=it.distance || ''}}">
                    </div>
                    <div class="col-sm-2">
                    <input type="number" name="meta_metro[{{=it.index}}][walking_time]" class="form-control" step="1" min="0" placeholder="мин" value="{{=it.walking_time || ''}}">
                    </div>
                    <div class="col-sm-2">                            
                    <input type="number" name="meta_metro[{{=it.index}}][drive_time]" class="form-control" step="1" min="0" placeholder="мин" value="{{=it.drive_time || ''}}">
                    </div>
                    </div>
                </script>
                <?php if (isset($meta_metro) && $meta_metro): ?>
                    <script>
                        (function () {
                            var m = <?= json_encode($meta_metro) ?>, fg, l, i = 0, t, tmp;

                            for (var k in m) {
                                l = $('[name*="metro_id"]').last();
                                fg = l.parents('.form-group');

                                m[k].distance = FlHelper.num.numberFormat(m[k].distance || 0, 1, '');

                                // add 
                                if (l.val()) {
                                    t = doT.template($('#tpl__metro').html());
                                    m[k].index = i;
                                    fg.after(t(m[k]));
                                    fg = $('[name*="metro_id"]').last().parents('.form-group');
                                }

                                fg.find('[name*="[metro_id]"]').val(m[k].metro_id ).change();
                                fg.find('[name*="[distance]"]').val(m[k].distance || '');
                                fg.find('[name*="[walking_time]"]').val(m[k].walking_time || '');
                                fg.find('[name*="[drive_time]"]').val(m[k].drive_time || '');

                                i++;
                            }

                        }());
                    </script>
                <?php endif; ?>
            </div>
            <!--\metro-->
            <!--registry-->
            <?php foreach ($registry_handbks as $hb): ?>
                <div id="<?= $_alias = array_get($hb, 'alias') ?>">
                    <div class="row" data-fg="<?= $_alias ?>">
                        <div class="col-sm-12">
                            <label class="control-label"><?= array_get($hb, 'name') ?> 
                                <!--<a class="text-info space_left_xs" data-copy-fg="#tpl__<?= $_alias ?>">добавить</a>-->
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <select name="registry_id[]" class="form-control js-select2" multiple="multiple">
                            <?php if (array_get($hb, 'list')): foreach ($hb['list'] as $it): ?>
                                    <option value="<?= array_get($it, 'registry_id') ?>"><?= array_get($it, 'name') ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                        <!--<a class="rm-form-group" data-type="<?= $_alias ?>"><span class="glyphicon glyphicon-trash"></span></a>-->
                    </div>
                    <script type="text/template" id="tpl__<?= $_alias ?>">
                        <div class="form-group">
                        <select name="registry_id[]" class="form-control js-select2">
                        <option value="">Не выбрано</option>
                        <?php if (array_get($hb, 'list')): foreach ($hb['list'] as $it): ?>
                                <option value="<?= array_get($it, 'registry_id') ?>"><?= array_get($it, 'name') ?></option>
                                <?php
                            endforeach;
                        endif;
                        ?>
                        </select>
                        <a class="rm-form-group" data-type="metro"><span class="glyphicon glyphicon-trash"></span></a>
                        </div>
                    </script>
                </div>
            <?php endforeach; ?>
            <?php if (isset($registry_ids) && $registry_ids && is_array($registry_ids)): ?>
                <script>
                    (function () {
                        var r = <?= json_encode($registry_ids) ?>;
                        for (var k in r) {
                           $('[name="registry_id[]"] [value="' + r[k] + '"]').attr('selected', 'selected');;
                        }
                    }());
                </script>
            <?php endif; ?>
            <!--\registry-->
            <!--map-->
            <label class="control-label">Расположение на карте</label>
            <div class="row form-group">
                <div class="col-sm-8">
                    <div id="ya-map"></div>
                </div>
                <div class="col-sm-4">
                    <ul class="list-unstyled" style="display: none;">
                        <li><b>Широта:</b> <span data-map-y></span><input type="hidden" name="y"></li>
                        <li><b>Долгота:</b> <span data-map-x></span><input type="hidden" name="x"></li>
                    </ul>
                </div>
            </div>
            <!--\map-->
            <!--panorama-->
            <?php foreach ($panoram_types as $field => $name): ?>
                <div class="form-group">
                    <label for="<?= $field ?>" class="control-label">Код панорамы <?= $name ?> (584x432) <a data-target="#<?= $field ?>" class="text-info space_left_xs js-panorama-open" data-name="<?= $name ?>">предварительный просмотр</a> (доступно после сохранения)</label>
                    <textarea class="form-control" name="<?= $field ?>" rows="3" placeholder="Код панорамы <?= $name ?>"><?= array_get($object, $field, ''); ?></textarea>
                </div>
            <?php endforeach; ?>
            <?php foreach ($panoram_types as $field => $name): ?>
                <?php if(array_get($object, $field)): ?>
                <div id="<?= $field ?>" style="display: none;">
                    <label class="control-label">Панорма <?= $name ?></label>
                    <div>
                        <?= $object[$field] ?>
                    </div>
                    
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </form>
    </div>
</div>