<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<script>
    FlRegister.set('fields', <?= json_encode($ids) ?>);
</script>
<div class="tab-pane active" id="tab2">
    <div class="row">
        <br/>
        <form class="form-horizontal" role="form"  method="POST" action="/admin/objects/object_location/<?= $object_id ?>">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Регион</label>
                <div class="col-sm-3">
                    <!--type_region-->
                    <select name="zone_id" id="zone_id" class="form-control">
                        <?php foreach ($zone as $key => $item): ?>
                            <option value="<?= $item->zone_id; ?>" data-zone="<?= $key ?>"><?= $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="display:<?php
            if (isset($ids))
                if ($ids['id_region'] == 1)
                    echo 'block';
                else
                    echo 'none';
            ?>" id="moscow">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Округ</label>
                    <div class="col-sm-4">
                        <select id="district" name="district_id" data-zone="MOW" data-relation="square" class="form-control">
                            <option value="0">Не выбрано</option>
                            <?php foreach ($district as $item): ?>
                                <option value="<?= $item->district_id ?>"><?= $item->short_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="inputEmail3" class="col-sm-2 control-label">Район</label>
                    <div class="col-sm-4">
                        <!--zone-->
                        <select id="square" name="square_id" class="form-control">
                            <option value="0">Не выбрано</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--            ////////////////////////////////////////////////////////////-->
            <div id="moscow_region">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Направление</label>
                    <div class="col-sm-4">
                        <select name="geo_direction_id" id="geo_direction_id" class="form-control" >
                            <?php foreach ($direction as $val): ?>
                                <option <?php if (isset($ids)) if ($val->id == $ids['id_direction']) echo 'selected=""'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="inputEmail3" class="col-sm-2 control-label">Город</label>
                    <div class="col-sm-4">
                        <!--populated_locality-->
                        <select name="populated_locality_id" id="populated_locality_id" class="form-control">
                            <option value="0">Не выбрано</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Район / Гор. округ</label>
                    <div class="col-sm-4">
                        <!--mr_zone-->
                        <select name="geo_area_id" id="geo_area_id" class="form-control">
                            <option value="0">Не выбрано</option>
                            <?php foreach ($geo_area as $item): ?>
                                <option value="<?= $item['geo_area_id'] ?>"><?= $item['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <label for="inputEmail3" class="col-sm-2 control-label">Расстояние до МКАД</label>
                    <div class="col-sm-4">
                        <select id="distance_to_mkad" class="form-control" name="distance_to_mkad">
                            <?php foreach ($distance_to_mkad as $val) { ?>
                                <option <?php if (isset($ids)) if ($val->id == $ids['id_distance_to_mkad']) echo 'selected=""'; ?> value="<?= $val->id ?>"><?= $val->name ?></option>
                                <?} ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="metro_staition_with_distance">
                    <label for="metro" class="col-sm-2 control-label" style="margin-top: 15px">Метро</label>
                    <?php if (isset($metro_data) && !empty($metro_data)): ?>
                        <?php foreach ($metro_data as $key => $item): ?>
                            <div class="row">
                                <?php if ($key !== 0): ?>
                                    <div class="col-sm-2"></div>
                                <?php endif; ?>
                                <!--metro-->
                                <div class="col-sm-3">
                                    <!--<div class="input-group">-->
                                        <!--onkeypress="$(this).autocomplete({source: availableTags});"-->
                                        <input type="text" name="metro_staition[<?= $key ?>][name]" min="0" class="form-control get_metro_station" value="<?= element('name', $item, '') ?>" autocomplete="off">
                                        <input type="hidden" name="metro_staition[<?= $key ?>][id]" value="<?= element('id', $item, '') ?>">
<!--                                        <span class="input-group-addon addon-dashboardicons addon-white">
                                            <img src="/images/metro/metro.png" alt="Color metro line" style="background: <?= element('color', $item, '#fff') ?>">
                                        </span>-->
                                    <!--</div>-->
                                </div>
                                <!--END metro-->
                                <!--metro distance-->
                                <div class="col-sm-7">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <!--mr_distance_to_metro-->
                                            <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-road"></i></span>
                                            <input type="number" step="0.1" min="0" name="metro_staition[<?= $key ?>][distance]" value="<?= element('distance', $item, '') ?>" class="form-control">
                                            <span class="input-group-addon"><i>км</i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!--mr_distance_to_metro_min-->
                                        <div class="input-group">
                                            <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-footprints"></i></span>
                                            <input type="number" step="1" min="0" name="metro_staition[<?= $key ?>][distance_foot]" value="<?= element('distance_foot', $item, '') ?>" class="form-control">
                                            <span class="input-group-addon"><i>мин</i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!--mr_distance_to_metro_car-->
                                        <div class="input-group">
                                            <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-car"></i></span>
                                            <input type="number" step="1" min="0" name="metro_staition[<?= $key ?>][distance_car]" value="<?= element('distance_car', $item, '') ?>" class="form-control">
                                            <span class="input-group-addon"><i>мин</i></span>
                                        </div>
                                    </div>
                                    <!--btn-->
                                    <div class="col-md-3">
                                        <?php if ($key == count($metro_data) - 1): ?>
                                            <a href="javascript:void(0)" class="btn btn-success pull-right metro_station_control metro_station_add" data-index="<?= $key ?>">
                                                <span class="glyphicon glyphicon-plus"></span><label>Добавить</label>
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-danger pull-right metro_station_control metro_station_del" data-index="<?= $key ?>">
                                                <span class="glyphicon glyphicon-minus"></span><label>Удалить</label>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <!--END btn-->
                                </div>
                                <!--END metro distance-->

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <!--onkeypress="$(this).autocomplete({source: availableTags});"-->
                                    <input type="text" name="metro_staition[0][name]" class="form-control get_metro_station" value="" autocomplete="off">
                                    <input type="hidden" name="metro_staition[0][id]" value="">
                                    <span class="input-group-addon addon-dashboardicons addon-white"><img src="/images/metro/metro.png" alt="Color metro line"></span>
                                </div>
                            </div>
                            <!--metro distance-->
                            <div class="col-sm-7">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-road"></i></span>
                                        <input type="number" step="0.1" min="0" name="metro_staition[0][distance]" value="" class="form-control">
                                        <span class="input-group-addon"><i>км</i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-footprints"></i></span>
                                        <input type="number" step="1" min="0" name="metro_staition[0][distance_foot]" value="" class="form-control">
                                        <span class="input-group-addon"><i>мин</i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-addon addon-dashboardicons"><i class="dashboardicons-sprite dashboardicons-car"></i></span>
                                        <input type="number" step="1" min="0" name="metro_staition[0][distance_car]" value="" class="form-control">
                                        <span class="input-group-addon"><i>мин</i></span>
                                    </div>
                                </div>
                                <!--btn-->
                                <div class="col-md-3">
                                    <a href="javascript:void(0)" class="btn btn-success pull-right metro_station_control metro_station_add" data-index="0">
                                        <span class="glyphicon glyphicon-plus"></span><label>Добавить</label>
                                    </a>
                                </div>
                                <!--END btn-->
                            </div>
                            <!--END metro distance-->
                        </div>
                    <?php endif; ?>
                </div>
                <!--END metro form group-->

                <br/>
                <div style="display: none;" class="form-group">
                    <label for="x" class="col-sm-2 control-label">x</label>
                    <div class="col-sm-4">
                        <input name="x" type="text" value="<?php if (isset($x)) echo $x; ?>" class="form-control">
                    </div>
                    <label for="y" class="col-sm-2 control-label">y</label>
                    <div class="col-sm-4">
                        <input name="y" type="text" value="<?php if (isset($y)) echo $y; ?>" class="form-control">
                    </div>
                </div>
                <input type="hidden" name="point" id="point" value="<?php if (isset($point)) echo $point; ?>"/>
                <div class="page-header">
                    <h4>Как добраться:</h4>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Общ. транспорт</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="bus" rows="3"><?php if (isset($route)) echo $route['bus']; ?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Автомобиль</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="auto" rows="3"><?php if (isset($route)) echo $route['auto']; ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="page-header">
                    <h4>На карте:</h4>
                </div>
                <div class="form-group">
                    <div class="col-md-10" style="width: 600px; margin-left: 13.66666666666666%;">
                        <?php if (ENVIRONMENT !== 'development'): ?>
                            <script src="http://api-maps.yandex.ru/1.1/index.xml?key=AEddJVMBAAAA4jTTJAIArjeuka2glAgytdOBVGv2S3UPmyQAAAAAAAAAAABPpTZmAqCTZGZ0FAzoI4PeAkaYng==" type="text/javascript"></script>
                            <link rel="stylesheet" href="http://api-maps.yandex.ru/1.1.21/_YMaps.css"/>
                            <script type="text/javascript" charset="utf-8" src="http://api-maps.yandex.ru/1.1.21/_YMaps.js"></script>
                            <script type="text/javascript" charset="utf-8" src="http://api-maps.yandex.ru/1.1.21/xml/data.xml?v=3.140.04.6.9"></script>
                            <script type="text/javascript">
            window.onload = function() {
                var map = new YMaps.Map(document.getElementById("YMapsID")),
                        flagLoad = 0;

                map.addControl(new YMaps.Zoom());

                map.enableScrollZoom();

                // По умолчанию карта центрируется на Москве
                map.setCenter(new YMaps.GeoPoint(37.64, 55.76), 10);

                // Добавляем метку в центр карты
                var placemark = new YMaps.Placemark(map.getCenter(), {
                    draggable: 1,
                    hasBalloon: 0
                }
                );
                placemark.setIconContent("_Указатель__");
                map.addOverlay(placemark);

                // Динамически формируем урл
                YMaps.Events.observe(map, [map.Events.Update, map.Events.MoveEnd, map.Events.ChangeType], setUrlParams);
                YMaps.Events.observe(placemark, placemark.Events.PositionChange, setUrlParams);

                // Функция для формирования параметров в URL'е
                function setUrlParams(obj) {
                    
                    var p;
                    
                    // Включаем установку параметров после загрузки всего скрипта
                    if (!flagLoad) {
                        return;
                    }
                    $('#point').val('#ll=' + map.getCenter().toString() +
                            '&z=' + map.getZoom() +
                            '&mt=' + map.getType().getLayers().toString() +
                            '&p=' + placemark.getGeoPoint().toString());
                    
                     p = placemark.getGeoPoint().toString().split(',');
                     
                     if(p){
                         $('[name="x"]').val(p[0]);
                         $('[name="y"]').val(p[1]);
                     }
                     
                }

                var hash = $('#point').val();
                if (hash != "") { // Если строка параметров не пуста
                    var hash = hash.substr(1, hash.length - 1).split('&'), // Отрезаем первый символ "#" и 
                            // разбиваем строку на подстроки параметр=значение
                            params = {}; // Объект будущих параметров

                    for (var i = 0, l = hash.length, param; i < l; i++) {
                        param = hash[i].split('='); // Разбиваем параметр на имя и значение
                        if (param[0] && param[1]) {
                            params[param[0]] = param[1];
                        }
                    }

                    // Если в урле заданы необходимые параметры
                    if (params.ll && params.z && params.mt && params.p) {
                        // Определяем тип карты
                        var mapType = YMaps.MapType.MAP;
                        switch (params.mt) {
                            case 'sat':
                                mapType = YMaps.MapType.SATELLITE;
                                break;

                            case 'sat,skl':
                                mapType = YMaps.MapType.HYBRID;
                                break;
                        }
                        // Центрируем карту в нужном месте
                        map.setCenter(YMaps.GeoPoint.fromString(params.ll), params.z, mapType);

                        // Устанавливаем маркер в нужную позицию
                        placemark.setGeoPoint(YMaps.GeoPoint.fromString(params.p));
                    }
                }
                flagLoad = 1;
            }
                            </script>
                        <?php endif; ?>
                        <div id="YMapsID" style="width:600px;height:400px" class="YMaps YMaps-quirks-mode YMaps-cursor-grab"></div>
                    </div>
                    <div style="float: left;width: 16%;margin: 0 30px;">
                        <span>Переместите указатель в необходимую точку карты.</span>
                        <p>Сохраняется:
                        <ul>
                            <li>- Широта</li>
                            <li>- Долгота</li>
                            <li>- Zoom</li>
                        </ul>
                        </p>
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
                    <div class="col-sm-offset-12 col-sm-10">
                        <input type="submit" class="btn btn-success" onclick="if (save_location()) {
                                    return true
                                } else {
                                    return false
                                }
                                ;" value="Сохранить"/>
                    </div>
                </div>
            </form>
        </div>
        <script>
            $(document).ready(function() {
                var toggleZoneControls = function(zone) {
                    if (zone === 'MOS') {
                        $('#moscow').hide();
                        $('#moscow select').attr('disabled', 'disabled');
                        $('#moscow_region').show();
                        $('#moscow_region select').removeAttr('disabled');
                    } else {
                        $('#moscow').show();
                        $('#moscow select').removeAttr('disabled');
                        $('#moscow_region').hide();
                        $('#moscow_region select').attr('disabled', 'disabled');
                    }
                }
                // zone default
                var zone = $('[name="zone_id"] :selected').data('zone');
                toggleZoneControls(zone);
                // on change zone
                $('[name="zone_id"]').change(function() {
                    var zone = $(':selected', this).data('zone');
                    toggleZoneControls(zone)
                });
            });



        </script>
</div>
</div>
</div>
</div>