<img src="/images/loader.gif" id="loader" style="position: absolute; top: 42%; left: 55%; opacity: 0.5" alt="">
<div class="left_column">
    <form method="get" action="/catalog/map" id="search_fiters">
        <a href="/" class="logo"><img src="/images/new/logo.png" alt="" /></a>
        <div class="fiters_bar bg_brand">
            <!-- КНОПКИ -->
            <div class="space_bottom_xs space_top_xs">
                <ul class="list_inline geo_nav">
                    <?php if (!empty($zone)): ?>
                        <?php foreach ($zone as $item): ?>
                            <li>
                                <a class="mark" data-geo="<?= $item->code ?>" href="javascript:void(0)"><?= $item->name ?><i class="icon_close" style="display: none"></i></a>
                                <input type="checkbox" class="hidden" name="code[]" id="code_<?= $item->code ?>" value="<?= $item->code ?>">
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </ul>
            </div>

            <div class="filters_place space_bottom_xs">

                <div class="grid_row space_bottom_xs">
                    <span class="title_1">Количество комнат</span>
                    <ul class="checkbox-styling checkbox-styling-inline">                
                        <li><input id="map-rooms1" type="checkbox" name="rooms[]" value="1" hidden /><label for="map-rooms1">1</label></li>
                        <li><input id="map-rooms2" type="checkbox" name="rooms[]" value="2" hidden /><label for="map-rooms2">2</label></li>
                        <li><input id="map-rooms3" type="checkbox" name="rooms[]" value="3" hidden /><label for="map-rooms3">3</label></li>
                        <li><input id="map-rooms4" type="checkbox" name="rooms[]" value="4" hidden /><label for="map-rooms4">4</label></li>
                        <li><input id="map-rooms5" type="checkbox" name="rooms[]" value="5" hidden /><label for="map-rooms5">5+</label></li>
                        <br>
                        <li><input id="map-rooms11" type="checkbox" name="rooms[]" value="11" hidden /><label for="map-rooms11">Студия</label></li>
                        <li><input id="map-rooms12" type="checkbox" name="rooms[]" value="12" hidden /><label for="map-rooms12">СП</label></li>
                    </ul>
                </div>

                <div class="space-slider-map edges-control space_bottom_xs">
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


                <div class="cost-slider space_bottom_xs">
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

                <div class="cost-m-slider-map edges-control">
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

            <div class="space_bottom_xs furnish_filter">
                <span class="title_1">Отделка</span>
                <select id="select_facing" class="select-styling select-facing" name="furnish">
                    <option value="0">Не важно</option>
                    <option value="1">Без отделки</option>
                    <option value="2">С отделкой</option>
                </select>
            </div>

            <div class="space_bottom_xs">
                <div class="complite-slider ">
                    <div class="form-section-header">Срок ввода</div>
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

            <div id="page_search_start" class="button_bg">
                <div class="button_face">
                    <span>Искать</span>
                </div>
            </div>
            <a href="javascript:void(0)" class="clear_filter">Очистить фильтр</a>

        </div>
    </form>
</div>

<div id="YMapsmap" class="map">

</div>
<?php if (isset($max_filters)): ?>
    <script>
        FlRegister.set('maxFilters', <?= $max_filters ?>);
        FlRegister.set('searchResult', <?= $objects ?>);
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
                                    var value = $.inArray(k, ['name', 'metro', 'remoteness_min', 'remoteness_max']) === -1 ? parseInt(fields[k]) : fields[k];
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
<div class="modal fade" id="modal_empty_search">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h3 class="title-default">Увы, но по Вашему запросу ничего не найдено. Попробуйте поискать по другим параметрам.</h3>
                <center><img src="/images/sad_little_house.png" alt="Я грустный маленький домик"></center>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->