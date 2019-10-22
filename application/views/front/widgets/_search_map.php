<div class="bg_map_search">
    <div class="bg">
        <div class="left">
            <div class="work_space">
                <!-- LAYER 1 -->
                <div class="layer_1">
                    <div class="grid_row">
                        <div class="column_1 padding_bottom_none">
                            <input id="search_center_map" class="search_input"  type="text" name="name" placeholder="Поиск по названию" />
                        </div>
                    </div>
                    <div class="grid_row">
                        <div class="column_1 padding_bottom_none">

                            <!-- КНОПКИ -->
                            <div class="space_bottom">
                                <!--                                <ul class="list_inline geo_nav">
                                                                    <li><a class="mark mark-active" data-geo="msk" href="javascript:void(0)">Москва</a></li>
                                                                    <li><a class="mark"data-geo="mo" href="javascript:void(0)">Область</a></li>
                                                                </ul>-->
                                <ul class="checkbox-styling checkbox-styling-inline map_position">                
                                    <li><input id="map_position1" type="checkbox" name="map_position[]" value="1" hidden /><label for="map_position1">Москва</label></li>
                                    <li><input id="map_position2" type="checkbox" name="map_position[]" value="2" hidden /><label for="map_position2">Область</label></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- LAYER 1 END -->
                <!-- LAYER 3 -->
                <div class="layer_3">
                    <div class="grid_row">
                        <div class="column_1 padding_bottom_none sub_layer_1_1">
                            <div class="bg_plashka">
                                <div class="grid_row space_bottom_xs">

                                    <span class="title_1">Количество комнат</span>
                                    <ul class="checkbox-styling checkbox-styling-inline">                
                                        <li><input id="map-rooms1" type="checkbox" name="rooms[]" value="1" hidden /><label for="map-rooms1">1</label></li>
                                        <li><input id="map-rooms2" type="checkbox" name="rooms[]" value="2" hidden /><label for="map-rooms2">2</label></li>
                                        <li><input id="map-rooms3" type="checkbox" name="rooms[]" value="3" hidden /><label for="map-rooms3">3</label></li>
                                        <li><input id="map-rooms4" type="checkbox" name="rooms[]" value="4" hidden /><label for="map-rooms4">4</label></li>
                                        <li><input id="map-rooms5" type="checkbox" name="rooms[]" value="5" hidden /><label for="map-rooms5">5+</label></li>
                                        <br><br>
                                        <li><input id="map-rooms11" type="checkbox" name="rooms[]" value="11" hidden /><label for="map-rooms11">Студия</label></li>
                                        <li><input id="map-rooms12" type="checkbox" name="rooms[]" value="12" hidden /><label for="map-rooms12">СП</label></li>
                                    </ul>

                                </div>
                                <div class="floor-slider-map edges-control">
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
                                <div class="space-slider-map edges-control">
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
                        </div>
                    </div>
                </div>
                <!--layer 3 END-->
                <div class="layer_5">
                    <div class="grid_row">
                        <div class="column_1 padding_bottom_none padding_top_none space_top_xs space_bottom_xs">
                            <span class="title_1">Отделка</span>
                            <select id="select_facing" class="select-styling select-facing" name="furnish">
                                <option value="0">Не важно</option>
                                <option value="1">Без отделки</option>
                                <option value="2">С отделкой</option>
                            </select>
                        </div>     
                    </div>
                    <div class="grid_row">
                        <div class="column_1" style="padding: 0 32px;">
                            <div class="complite-slider-map edges-control">
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
                <!--layer 5 END-->
                <!-- LAYER 6 -->
                <div class="layer_6">
                    <div class="grid_row">
                        <div class="column_1 padding_top_none">

                            <div id="map_search_start" class="button_bg">
                                <div class="button_face">
                                    <span>Искать</span>
                                </div>
                            </div>
                            <a href="javascript:void(0)" class="clear_filter">Очистить фильтр</a>

                        </div>
                    </div>
                </div>
                <!-- LAYER 6 END -->
            </div>
        </div>
        <!--left END-->
        <div class="right">
            <div class="bg_exit">
                <img src="/images/new/map_exit.png" />
            </div>
            <div id="YMapsmap" class="map" style="width:700px;height: 669px;"></div>
        </div>
    </div>
</div>