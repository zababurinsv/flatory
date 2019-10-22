<?php
$object_status = (int) array_get($object, 'status');
$panel_class = $object_status ? ' panel-status-' . array_get(array_get($status_list, $object_status, []), 'alias', '') : "";
?>
<div class="hpanel<?= $panel_class ?>">
    <div class="panel-body">
        <form action="" id="object-form" method="post">
            <div class="form-group">
                <label for="adres" class="control-label">Тип здания</label>      
                <div>
                    <?php foreach ($type_of_building as $it): ?>
                        <label class="checkbox-inline">
                            <div class="icheckbox_square-green">
                                <input type="checkbox" class="i-checks" name="registry_id[]" value="<?= array_get($it, 'registry_id') ?>">
                                <ins class="iCheck-helper"></ins>
                            </div>
                            <?= array_get($it, 'name') ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Серия дома</label>
                <select name="registry_id[]" data-name="building_lot" class="form-control js-select2" multiple="multiple">
                    <?php foreach ($building_lot as $it): ?>
                        <option value="<?= array_get($it, 'registry_id') ?>"><?= array_get($it, 'name') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row form-group">
                <div class="col-sm-3">
                    <label class="control-label">Количество корпусов</label>
                    <input type="number" name="number_of_sec" class="form-control" step="1" min="0" placeholder="">
                </div>
                <div class="col-sm-3">
                    <label class="control-label">Этажность</label>
                    <div class="input-group">
                        <input name="floor_begin" style="width: 50%;" type="number" value="" min="0" class="form-control" placeholder="от">
                        <input name="floor_end" style="width: 50%;" type="number" value="" min="0" class="form-control" placeholder="до">
                    </div>
                </div>
                <div class="col-sm-6"></div>
            </div>
            <div id="ceiling_height">
                <div data-fg="ceiling_height">
                    <label class="control-label">Высота потолка <a class="text-info space_left_xs" data-copy-fg="#tpl__ceiling_height">добавить</a></label>
                </div>
                <div class="row form-group">
                    <div class="col-sm-3">
                        <input type="number" name="ceiling_height[]" class="form-control" step="0.01" min="0" placeholder="м">
                        <a class="rm-form-group" data-type="ceiling_height" style="right: -5px;"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                    <div class="col-sm-9"></div>
                </div>
                <script type="text/template" id="tpl__ceiling_height">
                    <div class="row form-group">
                    <div class="col-sm-3">
                    <input type="number" name="ceiling_height[]" class="form-control" step="0.01" min="0" placeholder="м">
                    <a class="rm-form-group" data-type="ceiling_height" style="right: -5px;"><span class="glyphicon glyphicon-trash"></span></a>
                    </div>
                    <div class="col-sm-9"></div>
                    </div>
                </script>
            </div>
            <div class="row form-group">
                <div class="col-sm-3">
                    <label class="control-label">Сроки ввода (начало)</label>
                    <div class="input-group" style="width: 100%;">
                        <select name="delivery[quarter_start]" class="form-control" style="width: 50%;">
                            <option>Не выбрано</option>
                            <option value="1" selected="">1й квартал</option>
                            <option value="2">2й квартал</option>
                            <option value="3">3й квартал</option>
                            <option value="4">4й квартал</option>
                        </select>
                        <select name="delivery[year_start]" class="form-control" style="width: 50%;">
                            <option>Не выбрано</option>
                            <?php for ($k = date('Y') + 10; $k > 1999; $k--): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label class="control-label">Сроки ввода (конец)</label>
                    <div class="input-group" style="width: 100%;">
                        <select name="delivery[quarter]" class="form-control" style="width: 50%;">
                            <option>Не выбрано</option>
                            <option value="1" selected="">1й квартал</option>
                            <option value="2">2й квартал</option>
                            <option value="3">3й квартал</option>
                            <option value="4">4й квартал</option>
                        </select>
                        <select name="delivery[year]" class="form-control" style="width: 50%;">
                            <option>Не выбрано</option>
                            <?php for ($k = date('Y') + 10; $k > 1999; $k--): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <?php foreach ($registry_handbks as $hb): ?>
                <div class="form-group">
                    <label class="control-label"><?= array_get($hb, 'name') ?></label>
                    <div>
                        <?php if (array_get($hb, 'list')): ?> 
                            <?php foreach ($hb['list'] as $it): ?>
                                <label class="checkbox-inline">
                                    <div class="icheckbox_square-green">
                                        <input type="checkbox" class="i-checks" name="registry_id[]" value="<?= array_get($it, 'registry_id') ?>">
                                        <ins class="iCheck-helper"></ins>
                                    </div>
                                    <?= array_get($it, 'name') ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="form-group">
                <label class="control-label">Отделка</label>
                <div>
                    <label class="checkbox-inline">
                        <div class="icheckbox_square-green">
                            <input type="checkbox" class="i-checks" name="furnish[]" value="yes">
                            <ins class="iCheck-helper"></ins>
                        </div>
                        с отделкой
                    </label>
                    <label class="checkbox-inline">
                        <div class="icheckbox_square-green">
                            <input type="checkbox" class="i-checks" name="furnish[]" value="no">
                            <ins class="iCheck-helper"></ins>
                        </div>
                        без отделки
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Кол-во комнат</label>
                <div>
                    <?php foreach ($rooms as $it) : ?>
                        <label class="checkbox-inline">
                            <div class="icheckbox_square-green">
                                <input type="checkbox" class="i-checks" name="room[]" value="<?= array_get($it, 'room_id') ?>">
                                <ins class="iCheck-helper"></ins>
                            </div>
                            <?= array_get($it, 'name') ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Описание</label>
                <textarea class="form-control ckeditor" name="characteristics_anons" rows="3"><?= array_get($object, 'characteristics_anons') ?></textarea>
            </div>
        </form>
        <script>
            (function () {

                var current = <?= json_encode($object) ?>, i;
                FlRegister.set('fields', current);

                console.log(current);

                FlForm.fillForm(current, $('#object-form'));

                if (typeof current.building_lot_id === 'object' && !$.isEmptyObject(current.building_lot_id)) {
                    for (var k in current.building_lot_id) {
                        $('[data-name="building_lot"] [value="'+ current.building_lot_id[k] +'"]').attr('selected', 'selected');
                    }
                }


                $(document).on('ready', function () {


                    if (typeof current.delivery === 'object') {
                        for (var k in current.delivery) {
                            $('[name="delivery[' + k + ']"]').find('[value="' + current.delivery[k] + '"]').attr('selected', 'selected');
                        }
                    }

                    if (typeof current.ceiling_height === 'object' && !$.isEmptyObject(current.ceiling_height)) {

                        i = 0;

                        for (var k in current.ceiling_height) {
                            if (i === 0) {
                                // set current
                                $('#ceiling_height').find('[name="ceiling_height[]"]').val(current.ceiling_height[k]);
                            } else {
                                // add
                                $('#ceiling_height').find('[data-copy-fg]').trigger('click');
                                $('#ceiling_height').find('[name="ceiling_height[]"]').last().val(current.ceiling_height[k]);
                            }

                            i++;

                        }
                    }
                });
            }());
        </script>
    </div>
</div>