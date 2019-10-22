<h1 class="page-name"><?= array_get($object, 'name', '') ?></h1>
<?php if (array_get($object, 'anons')): ?>
    <p><?= $object['anons'] ?></p>
<?php endif; ?>
    <nav class="objectcard-nav">
        <!--navigation-->
        <ul class="list-inline">
            <?php
            $nav_list = [
                ['title' => 'О новостройке', 'url' => '#about', 'is_show' => TRUE],
                ['title' => 'Цены', 'url' => '#cost', 'is_show' => !!array_get($object, 'flats') && in_array('cost', $sections)],
                ['title' => 'Планировки', 'url' => '#plan', 'is_show' => isset($pluns) && $pluns && in_array('plan', $sections)],
                ['title' => 'Инфраструктура', 'url' => '#infrastructure', 'is_show' => !!array_get($object, 'infrastructure') && in_array('infrastructure', $sections)],
                ['title' => 'План застройки', 'url' => '#layout_plan', 'is_show' => (!!array_get($object, 'layout_plan') || !!array_get($object, 'layout_plan_map')) && in_array('layout_plan', $sections)],
                ['title' => 'Видео', 'url' => '#video', 'is_show' => !!array_get($object, 'video') && in_array('video', $sections)],
                ['title' => 'Характеристики', 'url' => '#technical_characteristics', 'is_show' => TRUE],
                ['title' => 'Расположение на карте', 'url' => '#map', 'is_show' => array_get($object, 'x') && array_get($object, 'y')],
                ['title' => 'Панорама', 'url' => '#panorama', 'is_show' => (isset($panoram_types) && !!$panoram_types) && (array_get($object, 'panorama_ya') || array_get($object, 'panorama_ggl'))],
                ['title' => 'Документы', 'url' => '#docs', 'is_show' => !!array_get($object, 'documents') && in_array('documents', $sections)],
                ['title' => 'Где купить', 'url' => '#sellers', 'is_show' => !!array_get($object, 'sellers') && in_array('builders_sellers', $sections)],
                ['title' => 'Застройщик', 'url' => '#builders', 'is_show' => !!array_get($object, 'builders') && in_array('builders_sellers', $sections)],
                ['title' => 'Новости и статьи', 'url' => '#posts', 'is_show' => !!array_get($object, 'posts')],
            ];

            foreach ($nav_list as $it): if (array_get($it, 'is_show')) :
                    ?>
                    <li><a href="<?= array_get($it, 'url') ?>"><?= array_get($it, 'title') ?></a></li>
                    <?php
                endif;
            endforeach;
            ?>
        </ul>
    </nav>
<!--\navigation-->
<?php if (isset($gallery) && $gallery): ?>
    <article class="gallery-place"><?= $gallery ?></article>
<?php endif ?>
<article id="about">
    <table class="objectcard">
        <?php if (array_get($object, 'adres')): ?>

            <tr>
                <td>Адрес</td>
                <td><span class="objectcard-value"><?= $object['adres'] ?></span> 
                    <?php if (array_get($object, 'x') && array_get($object, 'y')): ?>
                        <a href="#map" class="objectcard-anchor">Посмотреть на карте</a></td>
                <?php endif; ?>
            </tr>
        <?php endif ?>
        <?php if (($builders = array_get($object, 'builders')) && is_array($builders)): ?>
            <tr>
                <td>Застройщик</td>
                <td>
                    <?php foreach ($builders as $builder): ?>
                        <span class="objectcard-value"><?= array_get($builder, 'name') ?></span><br>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endif; ?>
        <!--format-jilya-->
        <?php if (($hb = array_get($registry_handbks, 'format-jilya')) && is_array($hb) && ($hb_list = array_get($hb, 'list'))): ?>
            <tr>
                <td><?= array_get($hb, 'name') ?></td>
                <td><span class="objectcard-value"><?= is_array($hb_list) ? implode_by_field(', ', $hb_list, 'name') : '' ?></span></td>
            </tr>
        <?php endif; ?>
        <!--\format-jilya-->        
        <?php if (($flats = array_get($object, 'flats')) && is_array($flats)): $_flats = []; ?>
            <tr>
                <td>Квартиры</td>
                <td>
                    <?php
                    foreach ($flats as $flat) {
                        if (($_flat_name = array_get($flat, 'name')))
                            $_flats[] = $_flat_name;
                    }
                    ?>
                    <span class="objectcard-value"><?= implode(', ', $_flats) ?></span> 
                    <?php if (isset($pluns) && $pluns): ?>
                        <a href="#plan" class="objectcard-anchor">Посмотреть планировки</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        <!--format-kvartiryi-->
        <?php if (($hb = array_get($registry_handbks, 'format-kvartiryi')) && is_array($hb) && ($hb_list = array_get($hb, 'list'))): ?>
            <tr>
                <td><?= array_get($hb, 'name') ?></td>
                <td><span class="objectcard-value"><?= is_array($hb_list) ? implode_by_field(', ', $hb_list, 'name') : '' ?></span></td>
            </tr>
        <?php endif; ?>
        <!--\format-kvartiryi-->   
        <?php if (isset($object['cost']['cost_min']) && $object['cost']['cost_min']): ?>
            <tr>
                <td>Цены</td>
                <td>
                    <span class="objectcard-value">от <?= big_ru_money_format($object['cost']['cost_min']) ?></span><br>
                </td>
            </tr>
        <?php endif; ?>
        <?php
        $space_min = isset($object['space']['space_min']) ? str_replace('.', ',', round($object['space']['space_min'], 1)) : 0;
        $space_max = isset($object['space']['space_max']) ? str_replace('.', ',', round($object['space']['space_max'], 1)) : 0;
        if ($space_min || $space_max):
            ?>
            <tr>
                <td>Площадь</td>
                <td>
                    <span class="objectcard-value">
                        <?php if ($space_min && $space_max): ?>
                            от <?= $space_min ?> до <?= $space_max ?> <span>м²</span>
                        <?php elseif ($space_min): ?>
                            от <?= $space_min ?> <span>м²</span>
                        <?php else: ?>
                            до <?= $space_max ?> <span>м²</span>
                        <?php endif; ?>
                    </span>
                </td>
            </tr>
        <?php endif; ?>
        <?php if (is_numeric(array_get($object, 'id_furnish'))): ?>
            <tr>
                <td>Отделка</td>
                <td>
                    <span class="objectcard-value"><?= (int) $object['id_furnish'] === 2 ? 'с отделкой' : ((int) $object['id_furnish'] === 1 ? 'без отделки' : 'с отделкой / без отделки') ?></span>
                </td>
            </tr>
        <?php endif; ?>
        <?php
        $dq = (int) array_get($object, 'delivery.quarter');
        $dqy = (int) array_get($object, 'delivery.year');
        $dqs = (int) array_get($object, 'delivery.quarter_start');
        $dqys = (int) array_get($object, 'delivery.year_start');
        $delivery_str = '';
        if (($dq !== 0 && $dqy !== 0) && ($dqs !== 0 && $dqys !== 0)) {
            $delivery_str = 'с ' . $dqs . ' кв ' . $dqys . ' г.  по ' . $dq . ' кв ' . $dqy . ' г.';
        } elseif ($dq !== 0 && $dqy !== 0) {
            $delivery_str = $dq . '-й квартал ' . $dqy . ' г.';
        }
        if ($delivery_str):
            ?>
            <tr>
                <td>Срок сдачи</td>
                <td>
                    <span class="objectcard-value"><?= $delivery_str ?></span>
                </td>
            </tr>
        <?php endif; ?>

        <?php
        $tmp_attrs = ['forma-prodaji', 'variantyi-pokupki'];
        foreach ($tmp_attrs as $_attr):
            ?>
            <!--<?= $_attr ?>-->
            <?php if (($hb = array_get($registry_handbks, $_attr)) && is_array($hb) && ($hb_list = array_get($hb, 'list'))): ?>
                <tr>
                    <td><?= array_get($hb, 'name') ?></td>
                    <td><span class="objectcard-value"><?= is_array($hb_list) ? implode_by_field(', ', $hb_list, 'name') : '' ?></span></td>
                </tr>
            <?php endif; ?>
            <!--\<?= $_attr ?>-->   
        <?php endforeach; ?>
        <!--metro-->
        <?php if (is_array($metro = array_get($object, 'metro')) && $metro): ?>
            <tr>
                <td>Метро</td>
                <td>
                    <?php foreach ($metro as $it): ?>
                        <table class="objectcard-metro-station">
                            <tr>
                                <?php if (($metro_lines = array_get($it, 'metro_lines'))): ?>
                                    <td>
                                        <?php foreach ($metro_lines as $line): ?>
                                            <img src="/images/metro/metro.png" style="background: <?= array_get($line, 'color', '#fff') ?>">
                                        <?php endforeach; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <span class="objectcard-value"><?= array_get($it, 'metro_station', '') ?></span>
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>

                </td>
            </tr>
        <?php endif; ?>
        <?php
        $tmp_attrs = ['jd-stancii-', 'shosse'];
        foreach ($tmp_attrs as $_attr):
            ?>       
            <!--<?= $_attr ?>-->
            <?php if (($hb = array_get($registry_handbks, $_attr)) && is_array($hb) && ($hb_list = array_get($hb, 'list'))): ?>
                <tr>
                    <td><?= array_get($hb, 'name') ?></td>
                    <td><span class="objectcard-value"><?= is_array($hb_list) ? implode_by_field(', ', $hb_list, 'name') : '' ?></span></td>
                </tr>
            <?php endif; ?>
            <!--\<?= $_attr ?>-->   
        <?php endforeach; ?>
        <!--registry-->
        <?php if (false && isset($registry_handbks) && $registry_handbks): ?>
            <?php foreach ($registry_handbks as $hb): ?>
                <tr>
                    <td><?= array_get($hb, 'name') ?></td>
                    <td><span class="objectcard-value"><?= !!($hb_list = array_get($hb, 'list')) && is_array($hb_list) ? implode_by_field(', ', $hb_list, 'name') : '' ?></span></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <?php if (array_get($object, 'description')): ?>
        <div><?= $object['description'] ?></div>
    <?php endif ?>
</article>
<?php if (($price_flats = array_get($object, 'price_flats')) && in_array('cost', $sections)): ?>
    <article id="cost">
        <div class="line-subtitle">
            <center>
                <h4>Цены</h4>
            </center>
            <hr>
        </div>
        <?php if (array_get($object, 'cost_anons')): ?>
            <div class="space_bottom"><?= $object['cost_anons'] ?></div>
        <?php endif ?>
        <table class="objectcard-prices">
            <thead>
                <tr>
                    <th>Квартиры</th>
                    <th>Площадь м²</th>
                    <th>Цена за м²</th>
                    <th>Цена за квартиру</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($price_flats as $room):
                    $space_min = str_replace('.', ',', round(element('space_min', $room, 0), 1));
                    $space_max = str_replace('.', ',', round(element('space_max', $room, 0), 1));
                    $cost_m_min = element('cost_m_min', $room, 0);
                    $cost_m_max = element('cost_m_max', $room, 0);
                    $cost_min = element('cost_min', $room, 0);
                    $cost_max = element('cost_max', $room, 0);
                    ?>
                    <tr>
                        <td><?php echo element('name', $room, '') === 'Свободной планировки' ? 'Свободная планировка' : element('name', $room, ''); ?></td>
                        <td>
                            <?php
                            if ($space_min == 0 && $space_max == 0)
                                print '—';
                            elseif ($space_min != 0 && $space_max != 0)
                                print $space_min . ' — ' . $space_max;
                            else
                                print $space_min == 0 ? 'до ' . $space_max : 'от ' . $space_min;
                            ?>
                        <td>
                            <?php
                            if ($cost_m_min == 0 && $cost_m_max == 0)
                                print '—';
                            elseif ($cost_m_min != 0 && $cost_m_max != 0)
                                print big_ru_money_format($cost_m_min, TRUE) . ' — ' . big_ru_money_format($cost_m_max);
                            else
                                print $cost_m_min == 0 ? 'до ' . big_ru_money_format($cost_m_max) : 'от ' . big_ru_money_format($cost_m_min);
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($cost_min == 0 && $cost_max == 0)
                                print '—';
                            elseif ($cost_min != 0 && $cost_max != 0)
                                print big_ru_money_format($cost_min, TRUE) . ' — ' . big_ru_money_format($cost_max);
                            else
                                print $cost_min == 0 ? 'до ' . big_ru_money_format($cost_max) : 'от ' . big_ru_money_format($cost_min);
                            ?>
                        </td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
    </article>
<?php endif ?>
<?php if (isset($pluns) && $pluns && in_array('plan', $sections)): ?>
    <article id="plan">
    <div class="line-subtitle">
        <center>
            <h4>Планировки</h4>
        </center>
        <hr>
    </div>
    <?= $pluns ?>
    </article>
<?php endif ?>
<?php if ((array_get($object, 'infrastructure') || (isset($infrastructure) && $infrastructure)) && in_array('infrastructure', $sections)): ?>
    <article id="infrastructure">
        <div class="line-subtitle">
            <center>
                <h4>Инфраструктура</h4>
            </center>
            <hr>
        </div>
        <?= array_get($object, 'infrastructure') ?>
        <?php if ($infrastructure && isset($infrastructure_groups)): ?>
        <?php foreach ($infrastructure as $group_id => $category): ?>
            <h4 class="subtitle text-center space_bottom space_top_l"><?= array_get($infrastructure_groups, $group_id) ?></h4>
            <div>
                <?php foreach ($category as $item): ?>
                    <div class="content-to-columns-<?= $group_id ?>">
                        <center class="center-block"><span class="<?= array_get($item, 'params.icon_class') ?>"></span></center>
                        <?php if (($items = array_get($item, 'items', []))): ?>
                            <?php foreach ($items as $it): ?>
                                <div class="text-center"><?= $it ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="space_bottom"></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="clearfix"></div> 
        <?php endforeach; ?>
        <div class="clearfix"></div>   
        <script>
            $('.content-to-columns-0').byColumns(3);
            $('.content-to-columns-1').byColumns(3);
        </script>
    <?php endif ?> 
    </article>
<?php endif ?> 
<?php if ((array_get($object, 'layout_plan') || array_get($object, 'layout_plan_map')) && in_array('layout_plan', $sections)): ?>
    <article id="layout_plan">
        <div class="line-subtitle">
            <center>
                <h4>План застройки</h4>
            </center>
            <hr>
        </div>
        <?php if (array_get($object, 'layout_plan')): ?>
            <div><?= $object['layout_plan'] ?></div>
        <?php endif ?> 
        <?php if (array_get($object, 'layout_plan_map')): ?>
            <div><?= $object['layout_plan_map'] ?></div>
        <?php endif ?> 
    </article>
<?php endif ?> 
<?php if (array_get($object, 'video') && in_array('video', $sections)): ?>
    <article id="video">
        <div class="line-subtitle">
            <center>
                <h4>Видео</h4>
            </center>
            <hr>
        </div>
        <div><?= $object['video'] ?></div>
    </article>
<?php endif ?> 
<article id="technical_characteristics">
    <div class="line-subtitle">
        <center>
            <h4>Характеристики</h4>
        </center>
        <hr>
    </div>
    <?php if (array_get($object, 'characteristics_anons')): ?>
        <p><?= $object['characteristics_anons'] ?></p>
    <?php endif; ?>
    <!--tech-->
    <div class="technical_block">
        <div class="col-50">
            <?php if (!empty($object['type_of_building'])): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-layers"></span>
                        Тип здания:<br />
                    </span>
                    <div class="text">
                        <ul>
                            <?php foreach ($object['type_of_building'] as $key => $val): ?>
                                <?php if (element('alias', $val) && $glossary_link_tpl): ?>
                                    <li><a href="<?= str_replace('{alias}', $val['alias'], $glossary_link_tpl) ?>"><?= element('name', $val) ?></a></li>
                                <?php else: ?>
                                    <li><?= element('name', $val) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>			
            <?php if (($floor_end = array_get($object, 'floor_end')) || ($floor_begin = array_get($object, 'floor_begin'))): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-chart-bar"></span>
                        Этажность:<br />
                    </span>
                    <div class="text">
                        <?php
                        if ($floor_begin)
                            echo 'от ' . $floor_begin . ' ';
                        if ($floor_end)
                            echo 'до ' . $floor_end;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'number_of_sec')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-th"></span>
                        Кол-во корпусов:<br />
                    </span>
                    <div class="text"><?= (int) $object['number_of_sec'] ?></div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'space.space_min') || array_get($object, 'space.space_max')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-resize-full-alt"></span>
                        Площадь квартир (м²):<br />
                    </span>
                    <div class="text">
                        <?php
                        if ($object['space']['space_min'])
                            echo 'от ' . str_replace('.', ',', round($object['space']['space_min'], 1)) . ' ';
                        if ($object['space']['space_max'] > 0)
                            echo 'до ' . str_replace('.', ',', round($object['space']['space_max'], 1));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'garage') || array_get($object, 'garage_comment')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-cab"></span>
                        Гараж/Парковка:<br />
                    </span>
                    <div class="text">
                        <?= !!array_get($object, 'garage') ? $object['garage'] . '.' : '' ?><?= array_get($object, 'garage_comment', ''); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'delivery')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-calendar"></span>
                        Срок сдачи:<br />
                    </span>
                    <div class="text">
                        <?php
                        $dq = (int) element('quarter', $object['delivery'], 0);
                        $dqy = (int) element('year', $object['delivery'], 0);
                        $dqs = (int) element('quarter_start', $object['delivery'], 0);
                        $dqys = (int) element('year_start', $object['delivery'], 0);
                        ?>
                        <?php if (($dq !== 0 && $dqy !== 0) && ($dqs !== 0 && $dqys !== 0)): ?>
                            <?= 'с ' . $dqs . ' кв ' . $dqys . ' г.<br>  по ' . $dq . ' кв ' . $dqy . ' г.' ?>
                        <?php elseif ($dq !== 0 && $dqy !== 0): ?>
                            <?= $dq . '-й квартал ' . $dqy . ' г.' ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
        <div class="col-50">
            <?php if (!empty($object['building_lot'])): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-commerical-building"></span>
                        Серия здания:<br />
                    </span>
                    <div class="text">
                        <ul>
                            <?php foreach ($object['building_lot'] as $key => $val): ?>
                                <?php if (element('alias', $val) && $glossary_link_tpl): ?>
                                    <li><a href="<?= str_replace('{alias}', $val['alias'], $glossary_link_tpl) ?>"><?= element('name', $val) ?></a></li>
                                <?php else: ?>
                                    <li><?= element('name', $val) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (($ceiling_height = array_get($object, 'ceiling_height')) && is_array($ceiling_height)): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-resize-vertical"></span>
                        Высота потолка:<br />
                    </span>
                    <div class="text">
                        <?php
                        array_walk($ceiling_height, function (&$it, $k) {
                            $it = str_replace('.', ',', round($it, 2));
                        })
                        ?>
                        <?= implode(' м, ', $ceiling_height) ?> м
                    </div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'flats')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-th-large"></span>
                        Кол-во комнат:<br />
                    </span>
                    <div class="text">
                        <?php
                        $rooms = array();
                        foreach ($object['flats'] as $flat) {
                            if ($flat['room_id'] < 6)
                                $rooms[] = $flat['room_id'];
                            else
                                $rooms[] = $flat['name'] === 'Свободной планировки' ? 'Свободная планировка' : $flat['name'];
                        }
                        $rooms = implode(',', $rooms);
                        ?>
                        <?= $rooms ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (($furnish = (int) array_get($object, 'id_furnish'))): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-check"></span>
                        Отделка:<br />
                    </span>
                    <div class="text">
                        <?php
                        if ($furnish === 2)
                            echo 'С отделкой';
                        elseif ($furnish !== 3)
                            echo 'Без отделки';
                        else
                            echo 'С отделкой/Без отделки';
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (array_get($object, 'protection') || array_get($object, 'protection_comment')): ?>
                <div class="tchb-item">
                    <span class="build">
                        <span class="fontello-icon fontello-icon-lock"></span>
                        Охрана:<br />
                    </span>
                    <div class="text">
                        <?= !!array_get($object, 'protection') ? $object['protection'] . '.' : '' ?><?= array_get($object, 'protection_comment', ''); ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
    <!--/tech-->
</article>
<?php if (array_get($object, 'x') && array_get($object, 'y')): ?>
    <article id="map">
        <div class="line-subtitle">
            <center>
                <h4>Расположение на карте</h4>
            </center>
            <hr>
        </div>
        <div class="space_bottom text-center">Укажите Ваше местоположение на карте, чтобы проложить маршрут до <?= array_get($object, 'name', 'объекта') ?>.</div>
        <div id="y-map"></div>
        <script>
            $(document).on('ready', function () {
                if (typeof FlMapRoute !== 'function')
                    return;

                window.FlDebug = {};

                var x = <?= $object['x']; ?>, y = <?= $object['y']; ?>;
                window.FlDebug.FlMapRoute = new FlMapRoute(x, y, 'y-map', {
                    width: '100%',
                    height: '432px',
                    controls: ['zoomControl']
                });
            });
        </script>
    </article>
<?php endif ?>

<?php if ((isset($panoram_types) && !!$panoram_types) && (array_get($object, 'panorama_ya') || array_get($object, 'panorama_ggl'))): ?>
    <article id="panorama">
        <div class="line-subtitle">
            <center>
                <h4>Панорама</h4>
            </center>
            <hr>
        </div>
        <?php if (array_get($object, 'panorama_ya') && array_get($object, 'panorama_ggl')): ?>
            <div class="clearfix space_bottom">
                <ul class="nav nav-pills js-tabs-nav">
                    <?php foreach ($panoram_types as $field => $name): ?>
                        <li><a href="javascript:void(0)" data-tab-group="panorama" data-tab="<?= $field ?>">Панорама <?= $name ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php foreach ($panoram_types as $field => $name): ?>
                <div class="js-tabs-content" data-tab-group="panorama" data-tab="<?= $field ?>"><?= array_get($object, $field) ?></div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php foreach ($panoram_types as $field => $name): ?>
                <div><?= array_get($object, $field) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="clearfix"></div>
        <script>
            (function () {
                // active first tab
                var t = $('[data-block="panorama"]').find('.js-tabs-nav [data-tab]').first();
                $('[data-block="panorama"]').find('.js-tabs-content[data-tab="' + t.data('tab') + '"]').addClass('active');
                t.parent('li').addClass('active');
            }());
        </script>
    </article>
<?php endif ?> 

<?php if (array_get($object, 'documents') && in_array('documents', $sections)): ?>
    <article id="docs">
        <div class="line-subtitle">
            <center>
                <h4>Документы</h4>
            </center>
            <hr>
        </div>
        <div>
            <?php foreach ($object['documents'] as $file): ?>
                <div class="img-text" style="height: 42px">
                    <img src="/images/<?= $ext = array_get($file, 'ext', 'doc') ?>.png" style="display: inline-block; margin-right: 10px; margin-bottom: -10px;">
                    <b><a <?php if (in_array($ext, array('jpg', 'png', 'gif'))) echo 'class="fancybox-thumbs"' ?> <?php if ($ext === 'pdf') echo 'target="_blank"' ?> href="<?= element('path', $file, '') . element('file_name', $file, 'doc') ?>"><?= array_get($file, 'alt', array_get($file, 'original_name', '')) ?></a></b>
                    <span> (.<?= $ext ?>)</span>
                </div>
            <?php endforeach; ?>
            <?php foreach ($object['documents_links'] as $file): ?>
                <div class="img-text" style="height: 42px">
                    <img src="/images/<?= $ext = element('ext', $file, 'doc') ?>.png">
                    <div>
                        <a style="font-size: 14px;" target="_blank" href="<?= element('link', $file, '') ?>"><?= element('name', $file, '') ?></a>
                        <span style="font-family: OpenSans_Regular; font-weight: normal;"> (.<?= $ext ?>)</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
<?php endif ?> 
<?php if (array_get($object, 'sellers') && in_array('builders_sellers', $sections)): ?>
    <article id="sellers">
        <div class="line-subtitle">
            <center>
                <h4>Где купить</h4>
            </center>
            <hr>
        </div>
        <div class="sellers">
            <?php foreach ($object['sellers'] as $val): $_params = element('params', $val, array()); ?>
                <div class="cont">
                    <div style="width: 100%;height: 102px;">
                        <img style="max-width: 100%;max-height: 100%;" src="<?= element('image', $val, '/images/nologo.jpg') ?>">
                    </div>
                    <div class="text"><?= element('name', $val, '') ?></div>
                    <div class="all_text">
                        <?= implode(', ', element('phone', $_params, array())) ?><br/>
                        <?= element('address', $_params, '') ?><br/>
                    </div>
                    <div class="space_top">
                        <a href="/kartoteka/organizations/<?= element('alias', $val, '') ?>">Подробнее</a><br/>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="clear"></div>
        </div>
    </article>
<?php endif ?> 
<?php if (array_get($object, 'builders') && in_array('builders_sellers', $sections)): ?>
    <article id="builders">
    <div class="line-subtitle">
        <center>
            <h4>Застройщик</h4>
        </center>
        <hr>
    </div>
    <div class="sellers">
        <?php foreach ($object['builders'] as $val): $_params = element('params', $val, array()); ?>
            <div class="cont">
                <div style="width: 100%;height: 102px;">
                    <img style="max-width: 100%;max-height: 100%;" src="<?= element('image', $val, '/images/nologo.jpg') ?>">
                </div>
                <div class="text"><?= element('name', $val, '') ?></div>
                <div class="all_text">
                    <?= implode(', ', element('phone', $_params, array())) ?><br/>
                    <?= element('address', $_params, '') ?><br/>
                </div>
                <div class="space_top">
                    <a href="/kartoteka/organizations/<?= element('alias', $val, '') ?>">Подробнее</a><br/>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="clear"></div>
    </div>
    </article>
<?php endif ?> 
<?php if (array_get($object, 'posts')): ?>
    <article id="posts">
        <div class="line-subtitle">
            <center>
                <h4>Новости и статьи</h4>
            </center>
            <hr>
        </div>
        <div class="clearfix">
            <?php foreach ($object['posts'] as $post): ?>
                <div class="col-50" style="padding: 0 5px;">
                    <b><a href="/<?= array_get($post, 'prefix', '') . '/' . array_get($post, 'alias', '') ?>" title="<?= array_get($post, 'name', '') ?>"><?= array_get($post, 'name', '') ?></a></b>
                    <p><?= array_get($post, 'anons', '') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </article>
<?php endif ?> 

<?php
//vdump($object['pluns'], 1);
//vdump($object, 1);
?>
<div class="space_bottom_xxl"></div>