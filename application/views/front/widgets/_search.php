<?php $small_search = TRUE;
// ($url != 'catalog') && ($url != 'objectcard'); vdump($url, 1)
?>
<!-- ПОИСК НА СТРАНИЦЕ -->
<div class="search"  <?php if ($small_search): ?>style="height: 79px;"<?php endif; ?>>
    <form method="get" action="/catalog/search" id="search_fiters">        

        <!-- ЛЕВЫЙ БЛОК -->
        <div class="first_column">
            <!-- КНОПКИ -->
            <div class="space_bottom">
                <ul class="list_inline geo_nav">
                    <?php if (!empty($zone)): ?>
                        <?php
                        foreach ($zone as $item):
//                            $active = $item->code === 'MOW' ? 'mark-active' : '';
//                            $checked = $item->code === 'MOW' ? 'checked="checked"' : '';
                            ?>
                            <li>
                                <a class="mark" data-geo="<?= $item->code ?>" href="javascript:void(0)"><?= $item->name ?><i class="icon_close" style="display: none"></i></a>
                                <input type="checkbox" class="hidden" name="code[]" id="code_<?= $item->code ?>" value="<?= $item->code ?>">
                            </li>
                        <?php endforeach; ?>
<?php endif; ?>

                </ul>
            </div>
            <!-- END -->
            <div class="clearfix"></div>
            <!-- ПО МОСКВЕ -->
            <div class="grid_row view_msk hidden" <?php if ($small_search): ?>style="display: none;"<?php endif; ?>>
                <div class="column_1 padding_l_r_none">
                    <div class="layer_1">
                        <span class="title_1">Метро</span>
                        <input id="metro_page" type="text" name="metro" class="get_metro_station" placeholder="Введите станцию метро"  autocomplete="false"/>
                        <input type="hidden" name="metro_staition_id" value="">
                    </div>

                    <!-- Типовой ползунок -->
                    <div class="layer_2" >
                        <div class="remoteness-slider ">
                            <div class="form-section-header">Удаленность от метро <span style="font-size: 11px;">(км)</span></div>
                            <div class="apt-space-slider">
                                <div class="range-slider">
                                    <div class="range-slider-val">
                                        <div class="range-min"><input type="text" name="remoteness_min"></div>
                                        <div class="range-max"><input type="text" name="remoteness_max"></div>
                                    </div>
                                    <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 32.58426966292135%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 65.1685393258427%;"></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END -->
                    <!-- Типовые радио -->
                    <div class="layer_3" >
                        <div id="transport_page" class="radio_block">
                            <label for="on_foot" class="for-radio">                    
                                <input type="radio" name="transport_type" id="on_foot"  value="1">
                                <span>Пешком</span>
                            </label>
                            <label for="by_transport" class="for-radio">
                                <input type="radio" name="transport_type" id="by_transport" value="2">
                                <span>На транспорте</span>
                            </label>
                            <label for="not_matter" class="for-radio">
                                <input type="radio" name="transport_type" id="not_matter" value="0">
                                <span>Не важно</span>
                            </label>
                        </div>
                    </div>
                    <!-- END -->
                </div>
            </div>
            <!-- ПО МОСКВЕ -->
            <!-- ПО MO -->
            <div class="grid_row view_mo hidden" <?php if ($small_search): ?>style="display: none;"<?php endif; ?>>
                <div class="column_1 padding_l_r_none">
                    <div class="form-section-header">Удаленность от МКАД <span style="font-size: 11px;">(км)</span></div>
                    <div id="distance_to_mkad" class="radio_block">
                        <?php if (!empty($distance_to_mkad)): ?>
    <?php foreach ($distance_to_mkad as $item): ?>
                                <label for="distance_to_mkad_<?= $item->id ?>" class="for-radio" style="padding: 5px;float: none;">                    
                                    <input type="radio" name="distance_to_mkad" id="distance_to_mkad_<?= $item->id ?>" value="<?= $item->id ?>">
                                    <span style="padding-left: 18px;"><?= $item->name ?></span>
                                </label>
                            <?php endforeach; ?>
<?php endif; ?>
                        <label for="distance_to_mkad_0" class="for-radio">
                            <input type="radio" name="distance_to_mkad" id="distance_to_mkad_0" value="0" checked="checked">
                            <span>Не важно</span>
                        </label>
                    </div>
                </div>
            </div>
            <!-- ПО MO -->
        </div>
        <!-- END -->

        <!-- БЛОК ПО ЦЕНТРУ -->
        <div class="second_column">
            <input id="search_center_page" class="search_input" type="text" name="name" placeholder="Поиск по названию" />
            <div class="filter_content hidden">
                <div class="layer_1"  <?php if ($small_search): ?>style="display: none;"<?php endif; ?>>
                    <div class="grid_row view_general hidden" style="min-height:77px;"></div>
                    <!-- ПО МОСКВЕ -->
                    <div class="grid_row view_msk hidden">
                        <div class="column_5">
                            <span class="title_1">Округ</span>
                            <select id="select_district" class="select-styling select-district" name="district" data-zone="MOW" data-relation="square">
                                <option value="0">Не важно</option>
                                <?php foreach ($district as $item): ?>
                                    <option value="<?= $item->district_id ?>"><?= $item->short_name ?></option>
<?php endforeach; ?>
                            </select>
                        </div>
                        <div class="column_7">
                            <span class="title_1">Район</span>
                            <select id="select_square" class="select-styling select-region" name="square" size="10">
                                <option value="0" selected>Не важно</option>
                            </select>
                        </div>
                    </div>
                    <!-- ПО МОСКВЕ -->
                    <!-- ПО ОБЛАСТИ -->
                    <div class="grid_row view_mo hidden">
                        <div class="column_5">
                            <span class="title_1">Направление</span>
                            <select id="select_geo_direction" class="select-styling select-district" name="geo_direction" data-zone="MOS" data-relation="populated_locality">
                                <option value="0" selected>Не важно</option>
                                <?php
                                if (!empty($geo_direction)):
                                    foreach ($geo_direction as $item):
                                        ?>
                                        <option value="<?= $item->geo_direction_id ?>"><?= $item->name ?></option>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                        <div class="column_7">
                            <span class="title_1">Район</span>
                            <select id="select_geo_area" class="select-styling select-region" name="geo_area" size="10">
                                <option value="0" selected>Не важно</option>
                            </select>
                        </div>
                    </div>
                    <!-- ПО ОБЛАСТИ -->


                    <div class="grid_row">
                        <div class="column_1 padding_top_none" style="padding-bottom: 10px">
                            <span class="title_1">Количество комнат</span>
                            <ul class="checkbox-styling checkbox-styling-inline">                
                                <li><input id="rooms1" type="checkbox" name="rooms[]" value="1" hidden /><label for="rooms1">1</label></li>
                                <li><input id="rooms2" type="checkbox" name="rooms[]" value="2" hidden /><label for="rooms2">2</label></li>
                                <li><input id="rooms3" type="checkbox" name="rooms[]" value="3" hidden /><label for="rooms3">3</label></li>
                                <li><input id="rooms4" type="checkbox" name="rooms[]" value="4" hidden /><label for="rooms4">4</label></li>
                                <li><input id="rooms5" type="checkbox" name="rooms[]" value="5" hidden /><label for="rooms5">5+</label></li>
                                <li><input id="rooms11" type="checkbox" name="rooms[]" value="11" hidden /><label for="rooms11">Студия</label></li>
                                <li><input id="rooms12" type="checkbox" name="rooms[]" value="12" hidden /><label for="rooms12">СП</label></li>
                            </ul>
                        </div>
                    </div>

                    <div class="sub_layer_1_2 grid_row">
                        <div class="column_6 padding_top_none padding_bottom_none">
                            <div class="floor-slider ">
                                <div class="form-section-header">Этажность</div>
                                <div class="apt-space-slider">
                                    <div class="range-slider">
                                        <div class="range-slider-val">
                                            <div class="range-min"><input type="text" name="floor_min"></div>
                                            <div class="range-max"><input type="text" name="floor_max"></div>
                                        </div>
                                        <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 32.58426966292135%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 65.1685393258427%;"></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="column_6 padding_top_none padding_bottom_none" style="padding-left: 15px;">
                            <div class="space-slider ">
                                <div class="form-section-header">Площадь <span style="font-family: OpenSans_Light; font-size: 10px;">(м²)</span></div>
                                <div class="apt-space-slider">
                                    <div class="range-slider">
                                        <div class="range-slider-val">
                                            <div class="range-min"><input type="text" name="space_min"></div>
                                            <div class="range-max"><input type="text" name="space_max"></div>
                                        </div>
                                        <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 32.58426966292135%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 65.1685393258427%;"></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layer_3 grid_row"  <?php if ($small_search): ?>style="display: none;"<?php endif; ?> >
                    <div class="column_4">
                        <span class="title_1">Отделка</span>
                        <select id="select_facing" class="select-styling select-facing" name="furnish">
                            <option value="0">Не важно</option>
                            <option value="1">Без отделки</option>
                            <option value="2">С отделкой</option>
                        </select>
                    </div>
                    <div class="column_8" style="padding-left: 30px;">
                        <div class="grid_row">
                            <div class="complite-slider ">
                                <div class="form-section-header" style="margin-left: -17px;">Срок ввода</div>
                                <div class="apt-space-slider">
                                    <div class="range-slider">
                                        <div class="range-slider-val rsv-complite">
                                            <div class="range-min">раньше<br><span class="current_year">2014</span><input type="text" name="complite_min" style="display: none"></div>
                                            <div class="range-max" style="text-align: right">позже<br><span class="next_year">2015</span><input type="text" name="complite_max" style="display: none"></div>
                                        </div>
                                        <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all rsb-complite">
                                            <div class="pipe_grid_values">
                                                <div class="min_label"></div>
                                                <div class="max_label"></div>
                                            </div>
                                            <table class="pipe_grid"><tbody>
                                                    <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                                                </tbody></table>
                                            <div class="ui-slider-range ui-widget-header"></div>
                                            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 0%;"></a>
                                            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 100%;"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- END -->

        <!-- ПРАВЫЙ БЛОК -->
        <div class="third_column">
            <div class="default">
                <a href="/catalog/map/" class="def_on_off">
                    <img src="/images/new/wtf.png" />
                    <img src="/images/new/dot_dot.png" />
                    <span id="map_search">Поиск по карте</span>
                </a>
                <div class="extends_search">
                    <img <?php if ($small_search): ?>src="/images/new/search_down.png"<?php else: ?>src="/images/new/search_up.png"<?php endif; ?> />
                </div>
            </div>
            <div class="filter_content hidden">
                <div class="layer_1 grid_row"  <?php if ($small_search): ?>style="display: none;"<?php endif; ?> >
                    <div class="column_1">
                        <div class="cost-slider ">
                            <div class="form-section-header">Стоимость жилья<span style="font-size: 10px;">&nbsp;(млн.руб.)</span></div>
                            <div class="apt-space-slider">
                                <div class="range-slider">
                                    <div class="range-slider-val">
                                        <div class="range-min"><input type="text" name="cost_min"></div>
                                        <div class="range-max"><input type="text" name="cost_max"></div>
                                    </div>
                                    <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 32.58426966292135%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 65.1685393258427%;"></a></div>
                                </div>
                            </div>
                        </div>
                        <div class="space_row"></div>
                        <div class="cost-m-slider ">
                            <div class="form-section-header">Цена за м²<span style="font-size: 10px;">&nbsp;(тыс.руб.)</span></div>
                            <div class="apt-space-slider">
                                <div class="range-slider">
                                    <div class="range-slider-val">
                                        <div class="range-min"><input type="text" name="cost_m_min"></div>
                                        <div class="range-max"><input type="text" name="cost_m_max"></div>
                                    </div>
                                    <div class="range-slider-bar ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all"><div class="ui-slider-range ui-widget-header"></div><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 32.58426966292135%;"></a><a class="ui-slider-handle ui-state-default ui-corner-all" href="#" style="left: 65.1685393258427%;"></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="button_section <?php if ($small_search): ?>button_section_small<?php endif; ?>">
                    <div id="page_search_start" class="button_bg"  <?php if ($small_search): ?>style="bottom: 16px;"<?php endif; ?>>
                        <div class="button_face">
                            <span>Искать</span>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="clear_filter">Очистить фильтр</a>
                </div>
            </div>
        </div>
        <!-- END -->
    </form>
</div>
<?php if (isset($max_filters)): ?>
    <script>
        FlRegister.set('maxFilters', <?= $max_filters ?>);
    </script>
<?php endif; ?>
<script type="text/javascript" src="/js/search.js"></script>
<!-- END -->
<!--select current el-->
<?php
$get = xss_clean($_GET);
$fields = !empty($get) ? json_encode($get) : '{}';
?>
<script>
        (function() {
            var fields = <?= $fields; ?>;

//            console.log(fields);

            // check radio transport_type before  form fill
            if (fields.transport_type !== undefined) {
                $('#transport_page input[type="radio"]' + '[value="' + fields.transport_type + '"]').attr('checked', 'checked');
                FlSearch.transportChange(fields.transport_type);
                delete fields.transport_type;
            }

            for (var k in fields) {
                var targrt = '[name^="' + k + '"]';
                // checkbox
                if ($.isArray(fields[k])) {
                    for (var i in fields[k]) {
                        $(targrt + '[value="' + fields[k][i] + '"]').attr('checked', 'checked');
                    }
                } else {
                    var tag = $(targrt).prop("tagName")
                    if (tag !== undefined) {
                        tag = tag.toLowerCase();
                        switch (tag) {
                            case 'select':
                                $(targrt + ' option[value="' + fields[k] + '"]').attr('selected', 'selected');
                                // если есть связанные селекты
                                var zone = $(targrt).data('zone');
                                if (zone !== undefined) {
                                    FlSearch.getSelectData(zone, fields[k]);
                                }
                                break;
                            case 'input':
                                var type = $(targrt).attr('type');
                                if (type !== undefined) {
                                    type = type.toLowerCase();
                                    if (type === 'checkbox' || type === 'radio') {
                                        $(targrt + '[value="' + fields[k] + '"]').attr('checked', 'checked');
//                                    $(targrt + '[value="' + fields[k] + '"]').trigger('change');
                                    } else {
                                        // значения всех полей кроме указанных в массиве приводим к целому
                                        var value = $.inArray(k, ['name', 'metro', 'remoteness_min', 'remoteness_max', 'metro_staition_id']) === -1 ? parseInt(fields[k]) : fields[k];
                                        $(targrt).val(value);
                                    }
                                }
                                break;
                        }
                    }
                }
            }
            // set Range Sliders!
            FlSearch.RS.replaceAll();
            $('.select-styling').selectmenu("refresh");
        }());
</script>

