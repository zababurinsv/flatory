<script>
    $(document).ready(function () {
        $("#datepicker").datepicker();
    });
</script>
<script>
    $(function () {
        var availablebuilding = [
<?php foreach ($state_building as $val) { ?>
                "<?= $val->name ?>",
<?php } ?>
        ];
        $("#state_building").autocomplete({
            source: availablebuilding
        });
    });

    $(function () {
        availabletype_of_building = [
<?php foreach ($type_of_building as $val) { ?>
                "<?= $val->name ?>",
<?php } ?>
        ];
    });

    $(function () {
        availablebuilding_lot = [
<?php foreach ($building_lot_arr as $val) { ?>
                "<?= $val->name ?>",
<?php } ?>
        ];
    });
</script>
<div class="tab-pane active" id="tab3">
    <br>
    <div class="row">
        <form class="form-horizontal" role="form" method="POST">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Тип здания</label>
                <div class="col-sm-3" id="building_all">
                    <?php if (isset($building_name) && !empty($building_name)) { ?>
                        <?php foreach ($building_name as $key => $val) { ?>
                            <div class="input-group" id="building_<?= $key ?>">
                                <select name="building[]" class="form-control" id="building_<?= $key ?>">
                                    <option value="">Не выбрано</option>
                                    <?php foreach ($type_of_building as $type) { ?>
                                        <option <?php if ($type->name === $val->name) echo 'selected'; ?> value="<?= $type->name ?>"><?= $type->name ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-btn">
                                    <?php if ($key == count($building_name) - 1) { ?>
                                        <button onclick="add_building(<?= $key ?>);" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                                        <?}else{?>
                                        <button onclick="$('#building_' +<?= $key ?>).remove()" class="btn btn-default" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                                    <?php } ?>
                                </span>
                            </div>
                        <?php } ?>
                        <?}else{?>
                        <div class="input-group" id="building_1">
                            <select name="building[]" class="form-control" id="building_1">
                                <option value="">Не выбрано</option>
                                <?php foreach ($type_of_building as $type) { ?>
                                    <option value="<?= $type->name ?>"><?= $type->name ?></option>
                                <?php } ?>
                            </select>
                            <span class="input-group-btn">
                                <button onclick="add_building(1);" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                            </span>
                        </div>
                    <?php } ?>
                </div>
                <label for="inputEmail3" class="col-sm-2 control-label">Серия дома</label>
                <div class="col-sm-3" id="building_lot_all">
                    <?php if (isset($building_lot) && !empty($building_lot)) { ?>
                        <?php foreach ($building_lot as $key => $val) { ?>
                            <div class="input-group" id="building_lot_<?= $key ?>">
                                <select name="building_lot[]" class="form-control" id="building_lot_<?= $key ?>">
                                    <option value="">Не выбрано</option>
                                    <?php foreach ($building_lot_arr as $type) { ?>
                                        <option <?php if ($type->name === $val->name) echo 'selected'; ?> value="<?= $type->name ?>"><?= $type->name ?></option>
                                    <?php } ?>
                                </select>
                                <span class="input-group-btn">
                                    <?php if ($key == count($building_lot) - 1) { ?>
                                        <button onclick="add_building_lot(<?= $key ?>)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                                        <?}else{?>
                                        <button onclick="$('#building_lot_' +<?= $key ?>).remove()" class="btn btn-default" type="button"><span class="glyphicon glyphicon-minus"></span></button>
                                    <?php } ?>
                                </span>
                            </div>
                        <?php } ?>
                        <?}else{?>
                        <div class="input-group" id="building_lot_1">
                            <select name="building_lot[]" class="form-control" id="building_lot_1">
                                <option value="">Не выбрано</option>
                                <?php foreach ($building_lot_arr as $type) { ?>
                                    <option value="<?= $type->name ?>"><?= $type->name ?></option>
                                <?php } ?>
                            </select>
                            <span class="input-group-btn">
                                <button onclick="add_building_lot(1)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                            </span>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Этажность</label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input name="floor_begin" style="width: 50%;" type="number" value="<?= isset($floor_begin) ? $floor_begin : ''; ?>" min="0" class="form-control" placeholder="от"/>
                        <input name="floor_end" style="width: 50%;" type="number" value="<?= isset($floor_end) ? $floor_end : ''; ?>" min="0" class="form-control" placeholder="до"/>
                    </div>
                </div>
                <label class="col-sm-2 control-label">Высота потолка (м)</label>
                <div class="col-sm-3">
                    <input name="ceiling_height" type="number" step="0.01" value="<?= isset($ceiling_height) ? $ceiling_height : ''; ?>" min="0" class="form-control"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Количество корпусов</label>
                <div class="col-sm-3">
                    <input name="number_of_sec" type="number" value="<?= isset($number_of_sec) ? $number_of_sec : ''; ?>" min="0" class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label for="room[]" class="col-sm-2 control-label">Количество комнат в квартирах</label>
                <div class="col-sm-3">
                    <label><input type="checkbox" name="room[]" value="1" /> 1 комнатная</label>
                    <label><input type="checkbox" name="room[]" value="2" /> 2 комнатная</label>
                    <label><input type="checkbox" name="room[]" value="3" /> 3 комнатная</label>
                    <label><input type="checkbox" name="room[]" value="4" /> 4 комнатная</label>
                    <label><input type="checkbox" name="room[]" value="5" /> 5+ комнатная</label>
                    <label><input type="checkbox" name="room[]" value="11" /> студия</label>
                    <label><input type="checkbox" name="room[]" value="12" /> свободная планировка</label>
                </div>
                <?php if (isset($number_of_rooms)): ?>
                    <script>
                        // checked current rooms
                        (function () {
                            var room = <?= $number_of_rooms ?>;
                            for (var k in room) {
                                $('[name="room[]"][value="' + room[k] + '"]').attr('checked', 'checked');
                            }
                            // event click: if is limited & unchecked - warning msg
                            $('[name="room[]"]').on('click', function () {
                                var status = $(this).is(':checked'),
                                        limited = $.inArray($(this).val(), room) !== -1;
                                if (status === false && limited === true)
                                    if (!confirm('Внимание! Вы уверены, что хотите удалить этот атрибут? Связанные данные в разделе "Стоимость" будут потеряны безвозвратно!'))
                                        $(this).attr('checked', 'checked');
                            });
                        }());
                    </script>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Отделка</label>
                <div class="col-sm-4">
                    <select name="furnish" class="form-control">
                        <option value="">Не выбрано</option>
                        <option <?php if (isset($furnish) && $furnish == 1) echo 'selected'; ?> value="1">Без отделки</option>
                        <option <?php if (isset($furnish) && $furnish == 2) echo 'selected'; ?> value="2">С отделкой</option>
                        <option <?php if (isset($furnish) && $furnish == 3) echo 'selected'; ?> value="3">С отделкой / Без отделки</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Гараж/Парковка</label>
                <div class="col-sm-3">
                    <select name="garage" class="form-control">
                        <option value="">Не выбрано</option>
                        <?php foreach ($garage as $val) { ?>
                            <option <?php if (isset($ids) && isset($ids['id_garage'])) if ($ids['id_garage'] == $val->id) echo 'selected'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-5">
                    <input type="text" name="garage_comment" class="form-control" value="<?= array_get($tech_comments, 'garage_comment', ''); ?>" placeholder="Комментарий"/>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Охрана</label>
                <div class="col-sm-3">
                    <select name="protection" class="form-control">
                        <option value="">Не выбрано</option>
                        <?php foreach ($protection as $val) { ?>
                            <option <?php if (isset($ids) && isset($ids['id_protection'])) if ($ids['id_protection'] == $val->id) echo 'selected'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-5">
                    <input type="text" name="protection_comment" class="form-control" value="<?= array_get($tech_comments, 'protection_comment', ''); ?>" placeholder="Комментарий"/>
                </div>
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Состояние строительства</label>
                <div class="col-sm-8">
                    <input type="text" id="state_building" name="state_building" class="form-control" value="<?= (isset($state)) ? $state : '' ?>" id="inputText3"/>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Сроки ввода (начало)</label>
                <div class="col-sm-8">
                    <div style="width: 50%;" class="input-group">
                        <?php if (isset($delivery) && !empty($delivery)) { ?>
                            <select style="width: 50%;" name="quarter_start" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 1; $i <= 4; $i++) { ?>
                                    <option value="<?= $i ?>" <?php if ($delivery['quarter_start'] == $i) echo 'selected' ?>><?= $i . 'й квартал' ?></option>
                                <?php } ?>
                            </select>
                            <select style="width: 50%;" name="year_start" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 2000; $i <= 2050; $i++) { ?>
                                    <option value="<?= $i ?>" <?php if ($delivery['year_start'] == $i) echo 'selected' ?>><?= $i . " год" ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <select style="width: 50%;" name="quarter_start" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 1; $i <= 4; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i ?>й квартал</option>
                                <?php } ?>
                            </select>
                            <select style="width: 50%;" name="year_start" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 2000; $i <= 2050; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i . " год" ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Сроки ввода (окончание)</label>
                <div class="col-sm-8">
                    <div style="width: 50%;" class="input-group">
                        <?php if (isset($delivery) && !empty($delivery)) { ?>
                            <select style="width: 50%;" name="quarter" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 1; $i <= 4; $i++) { ?>
                                    <option value="<?= $i ?>" <?php if ($delivery['quarter'] == $i) echo 'selected' ?>><?= $i . 'й квартал' ?></option>
                                <?php } ?>
                            </select>
                            <select style="width: 50%;" name="year" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 2000; $i <= 2050; $i++) { ?>
                                    <option value="<?= $i ?>" <?php if ($delivery['year'] == $i) echo 'selected' ?>><?= $i . " год" ?></option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <select style="width: 50%;" name="quarter" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 1; $i <= 4; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i ?>й квартал</option>
                                <?php } ?>
                            </select>
                            <select style="width: 50%;" name="year" class="form-control">
                                <option>Не выбрано</option>
                                <?php for ($i = 2000; $i <= 2050; $i++) { ?>
                                    <option value="<?= $i ?>"><?= $i . " год" ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php foreach ($registry_handbks as $hb): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?= array_get($hb, 'name') ?></label>
                    <div class="col-sm-8">
                        <div class="input-group">
                            <select name="registry_id[]" class="form-control">
                                <option value="">Не выбрано</option>
                                <?php if (array_get($hb, 'list')): foreach ($hb['list'] as $it): ?>
                                        <option value="<?= array_get($it, 'registry_id') ?>"><?= array_get($it, 'name') ?></option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" data-action="rm_parent" data-parent=".input-group" style="display: none;"><span class="glyphicon glyphicon-minus"></span></button>
                                <button type="button" class="btn btn-default" data-action="copy_parent" data-parent=".input-group"><span class="glyphicon glyphicon-plus"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!--registry_ids-->
            <?php if (isset($registry_ids) && $registry_ids && is_array($registry_ids)): ?>
                <script>
                    (function () {
                        var r = <?= json_encode($registry_ids) ?>, s, o, el, cp;
                        for (var k in r) {
                            o = $('[name="registry_id[]"] [value="' + r[k] + '"]');
                            s = $(o).parent('select');
                            if (!s.val()){   
                                o.attr('selected', 'selected');
                            } else {
                                // copy last el
                                el = $(s).last().parents('.input-group');                                
                                cp = $(el).clone();
                                // turn buttons
                                el.find('[data-action="copy_parent"]').hide();
                                el.find('[data-action="rm_parent"]').show();
                                // set copy value
                                cp.find('[value="'+ r[k] +'"]').attr('selected', 'selected');
                                el.after(cp);
                            }
                        }
                    }());
                </script>
            <?php endif; ?>
            <!--\registry_ids-->
            <div class="form-group">
                <label for="characteristics_anons" class="col-sm-2 control-label">Описание</label>
                <div class="col-sm-8">
                    <textarea class="form-control ckeditor" name="characteristics_anons" rows="3" placeholder="Описание для раздела Характеристики"><?= isset($characteristics_anons) ? $characteristics_anons : '' ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-12 col-sm-10">
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </form>
    </div>
</div>
