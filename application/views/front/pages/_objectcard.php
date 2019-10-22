<div class="card content_space">
    <div class="page-name"><?= $object['name'] ?></div>
    <div style="margin:30px 0;"><?= $gallery; ?></div>
    <div style="clear: both"></div>
    <div class="block1">
        <h4 style="cursor: pointer; color: #74c84a; font-size: 14px; font-weight: 700;margin: 15px auto;" onclick="$('html,body').scrollTop($('#map').offset().top);"><span class="fb-icon-xs fb-icon-address"></span><?= $object['adres'] ?></h4>
        <div class="info">
            <div class="area" style="float: none; width: 100%; height: 35px;">
                <img class="img1" src="/images/rub.png"/>
                <b>Цена: </b>
                <?php if (!$object['cost']['cost_min']): ?>
                    <span>−</span>
                <?php else: ?>
                    от <span class="sum1"><?php echo $object['cost']['cost_min'] ?> </span><span class="rub">руб.</span>
                <?php endif; ?>
            </div>
            <div class="area clearfix" style="float: none; width: 100%">
                <img class="img1" src="/images/area.png"/>
                <b>Площадь: </b>
                <?php if ((int) $object['space']['space_min'] && (int) $object['space']['space_max']): ?>
                    от <span class="sum2"><?= $object['space']['space_min'] ?></span> до <span class="sum2"><?= $object['space']['space_max'] ?> </span><span>м²</span>
                <?php elseif ((int) $object['space']['space_min']): ?>
                    от <span class="sum2"><?= $object['space']['space_min'] ?></span><span>м²</span>
                <?php elseif ((int) $object['space']['space_max']): ?>
                    до <span class="sum2"><?= $object['space']['space_max'] ?> </span><span>м²</span>
                <?php else: ?>
                    <span>−</span>
                <?php endif; ?>
            </div>
        </div>
        <?php if(isset($adv1) && $adv1): ?>
        <div>
            <?= $adv1 ?>
        </div>
        <?php endif ?>
        <div class="content_text" id="gen_info">
            <?= $object['description'] ?>
        </div>
    </div>
    <div class="block2">
        <div class="cart_title">Местоположение
            <img class="toggle" src="/images/toggle.png"/>
        </div>
        <div class="tog">
            <div class="area">
                <img class="mesto_img" src="/images/mesto.png"/>
                <div class="mesto_text"><?= $object['zone'] ?></div>
                <div class="mesto_text2">
                    <?php
                    $geo = array();
                    if (element('geo_area', $object, FALSE))
                        $geo[] = $object['geo_area'];

                    if (element('district', $object, FALSE))
                        $geo[] = $object['district'];

                    if (element('populated_locality', $object, FALSE))
                        $geo[] = $object['populated_locality'];

                    if (element('square', $object, FALSE))
                        $geo[] = $object['square'];
                    ?>
                    <?= implode(', ', $geo); ?>
                </div>
            </div>
            <div class="area">
                <?php if (!empty($object['distance_to_metro'])): ?>
                    <ul class="metro">
                        <?php foreach ($object['distance_to_metro'] as $metro): ?>
                            <li>
                                <?php if(($metro_lines = array_get($metro, 'metro_lines'))): ?>
                                <?php foreach ($metro_lines as $line): ?>
                                    <img src="/images/metro/metro.png" style="background: <?= array_get($line, 'color', '#fff') ?>">
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="metro_name"><?= $metro['metro_station'] ?>
                                    <?php if ($metro['min'] || $metro['car']): ?>
                                        <span><b> − </b></span>
                                    <?php endif; ?>
                                    <span><b><?php
                                            if ($metro['min'])
                                                echo $metro['min'] . ' мин. пешком';
                                            if ($metro['car'])
                                                echo ( $metro['min'] ? '<br/>' : '' ) . $metro['car'] . ' мин. транспортом';
                                            ?></b></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="way">
        <div class="cart_title">Как добраться
            <img class="toggle" src="/images/toggle.png"/>
        </div>
        <div style="margin-top: 32px;">
            <div class="cont">
                <div class="auto">
                    <img class="img1" src="/images/public_transport.png"/>
                    <span class="mesto_text">Общественным транспортом</span>
                    <div style="clear: both"></div>
                </div>
                <div class="public_transport">
                    <?= $object['bus']; ?>
                </div>
            </div>
            <div class="cont">
                <div class="auto2">
                    <img class="img1" src="/images/auto_transport.png"/>
                    <span style="line-height: 44px;" class="mesto_text">На автомобиле</span>
                    <div style="clear: both"></div>
                </div>
                <div class="auto_transport">
                    <?= $object['auto']; ?>
                </div>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
    <div style="border-bottom: 1px solid #e0e0e0;">
        <div class="cart_title space_bottom">На карте
            <img class="toggle" src="/images/toggle.png"/>
        </div>
        <div id="y-map"></div>
        <script>
            (function() {
                var o = <?= isset($map) ? json_encode($map) : false; ?>, mc = <?= isset($map_center) && $map_center ? json_encode($map_center) : false; ?>;

                $('#y-map').width('100%');
                $('#y-map').height($('#y-map').width());
                // set center map
                if ($.isArray(mc) && mc.length)
                    configFlMap = {center: mc, controls: ['zoomControl'], is_open_baloon: false};

                $.subscribe('fl_map_ready', function(e) {
                    if (o)
                        FlMap.setObjects(o);
                });
            }());
        </script>
    </div>
    <div class="technical">
        <div class="cart_title">Технические характеристики
            <img class="toggle" src="/images/toggle_right.png"/>
        </div>
        <div class="technical_block" style="display: none;">
            <div class="tr">
                <div class="td">
                    <img src="/images/build_type.jpg"/>
                    <span class="build">
                        Тип здания:<br />
                    </span>
                    <div class="text">
                        <ul>
                            <?php if (!empty($object['type_of_building'])): ?>
                                <?php foreach ($object['type_of_building'] as $key => $val): ?>
                                    <?php if (element('alias', $val) && $glossary_link_tpl): ?>
                                        <li><a href="<?= str_replace('{alias}', $val['alias'], $glossary_link_tpl) ?>"><?= element('name', $val) ?></a></li>
                                    <?php else: ?>
                                        <li><?= element('name', $val) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_serial.jpg"/>
                    <span class="build">
                        Серия здания:<br />
                    </span>
                    <div class="text">
                        <ul>
                            <?php if (!empty($object['building_lot'])): ?>
                                <?php foreach ($object['building_lot'] as $key => $val): ?>
                                    <?php if (element('alias', $val) && $glossary_link_tpl): ?>
                                        <li><a href="<?= str_replace('{alias}', $val['alias'], $glossary_link_tpl) ?>"><?= element('name', $val) ?></a></li>
                                    <?php else: ?>
                                        <li><?= element('name', $val) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tr">
                <div class="td">
                    <img src="/images/build_floor.jpg"/>
                    <span class="build">
                        Этажность:<br />
                    </span>
                    <div class="text">
                        <?php
                        if (array_get($object, 'floor_end') && array_get($object, 'floor_begin'))
                            echo 'от ' . $object['floor_begin'] . ' ';
                        if ($object['floor_end'])
                            echo 'до ' . $object['floor_end'];
                        ?>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_height.jpg"/>
                    <span class="build">
                        Высота потолка:<br />
                    </span>
                    <div class="text">
                        <?php if (element('ceiling_height', $object, '')): ?>
                            <?= $object['ceiling_height'] ?> м
                        <?php endif; ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tr">
                <div class="td">
                    <img src="/images/build_housing.jpg"/>
                    <span class="build">
                        Кол-во корпусов:<br />
                    </span>
                    <div class="text">
                        <?= (int) element('number_of_sec', $object) ?>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_room.jpg"/>
                    <span class="build">
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
                <div class="clear"></div>
            </div>
            <div class="tr">
                <div class="td">
                    <img src="/images/build_wight.jpg"/>
                    <span class="build">
                        Площадь квартир (м²):<br />
                    </span>
                    <div class="text">
                        <?php
                        if ($object['space']['space_min'])
                            echo 'от ' . $object['space']['space_min'] . ' ';
                        if ($object['space']['space_max'] > 0)
                            echo 'до ' . $object['space']['space_max'];
                        ?>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_otdelka.jpg"/>
                    <span class="build">
                        Отделка:<br />
                    </span>
                    <div class="text">
                        <?php
                        if (!empty($object['id_furnish'])) {
                            if ($object['id_furnish'] == 2)
                                echo 'С отделкой';
                            elseif ($object['id_furnish'] != 3)
                                echo 'Без отделки';
                            if ($object['id_furnish'] == 3)
                                echo 'С отделкой/Без отделки';
                        } else {
                            echo '-';
                        }
                        ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tr">
                <div class="td">
                    <img src="/images/build_garage.jpg"/>
                    <span class="build">
                        Гараж/Парковка:<br />
                    </span>
                    <div class="text">
                        <?= !!array_get($object, 'garage') ? $object['garage'] . '.' : '' ?><?= array_get($object, 'garage_comment', ''); ?>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_security.jpg"/>
                    <span class="build">
                        Охрана:<br />
                    </span>
                    <div class="text">
                        <?= !!array_get($object, 'protection') ? $object['protection'] . '.' : '' ?><?= array_get($object, 'protection_comment', ''); ?>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tr">
                <div class="td">
                    <img src="/images/build_state.jpg"/>
                    <span class="build">
                        Состояние строительства:<br />
                    </span>
                    <div class="text">
                        <?= $object['state_building'] ?>
                    </div>
                </div>
                <div class="td">
                    <img src="/images/build_time.jpg"/>
                    <span class="build">
                        Срок ввода:<br />
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
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="price">
        <div class="cart_title">Стоимость квартир
            <img class="toggle" src="/images/toggle_right.png"/>
        </div>
        <div class="price_table" style="display: none;">
            <table class="price_table_1">
                <tr>
                    <td>Квартиры</td>
                    <td>Метраж <span class="price_rub">(м²)</span></td>
                    <td>Цена за м² <span class="price_rub">(руб.)</span></td>
                    <td>Цена за квартиру <span class="price_rub">(руб.)</span></td>
                </tr>
                <?php
                foreach ($object['flats'] as $room):
                    $space_min = element('space_min', $room, 0);
                    $space_max = element('space_max', $room, 0);
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
                                print number_format($space_min, 0, ',', ' ') . ' — ' . number_format($space_max, 0, ',', ' ');
                            else
                                print $space_min == 0 ? 'до ' . number_format($space_max, 0, ',', ' ') : 'от ' . number_format($space_min, 0, ',', ' ');
                            ?>
                        <td>
                            <?php
                            if ($cost_m_min == 0 && $cost_m_max == 0)
                                print '—';
                            elseif ($cost_m_min != 0 && $cost_m_max != 0)
                                print number_format($cost_m_min, 0, ',', ' ') . ' — ' . number_format($cost_m_max, 0, ',', ' ');
                            else
                                print $cost_m_min == 0 ? 'до ' . number_format($cost_m_max, 0, ',', ' ') : 'от ' . number_format($cost_m_min, 0, ',', ' ');
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($cost_min == 0 && $cost_max == 0)
                                print '—';
                            elseif ($cost_min != 0 && $cost_max != 0)
                                print number_format($cost_min, 0, ',', ' ') . ' — ' . number_format($cost_max, 0, ',', ' ');
                            else
                                print $cost_min == 0 ? 'до ' . number_format($cost_max, 0, ',', ' ') : 'от ' . number_format($cost_min, 0, ',', ' ');
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <!--Планировки-->
    <div class="planning">
        <div class="cart_title"><a id="plans"></a>Планировки
            <img class="toggle" src="/images/toggle_right.png"/>
        </div>
        <?= $pluns ?>
    </div>
    <div style="clear:both"></div>
    <!--Фото строительства-->
    <div class="gallery">
        <div class="cart_title"><a id="photo_construction"></a>Фото строительства
            <img class="toggle" src="/images/toggle_right.png"/>
        </div>
        <?= $photo_construction ?>
    </div>
    <div style="clear:both"></div>
    <?php if (!empty($object['documents']) || !empty($object['documents_links'])): ?>
        <div class="documents">
            <div class="cart_title">Документы
                <img class="toggle" src="/images/toggle_right.png"/>
            </div>
            <div style="margin-top: 30px;margin-bottom: -10px;display: none;">
                <?php foreach ($object['documents'] as $file): ?>
                    <div class="img-text" style="height: 42px">
                        <img src="/images/<?= $ext = element('ext', $file, 'doc') ?>.png">
                        <div>
                            <a style="font-size: 14px;" <?php if (in_array($ext, array('jpg', 'png', 'gif'))) echo 'class="fancybox-thumbs"' ?> <?php if ($ext === 'pdf') echo 'target="_blank"' ?> href="<?= element('path', $file, '') . element('file_name', $file, 'doc') ?>"><?= element('original_name', $file, '') ?></a>
                            <span style="font-family: OpenSans_Regular; font-weight: normal;"> (.<?= $ext ?>)</span>
                        </div>
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
        </div>
    <?php endif; ?>
    <div class="infrastructure">
        <div class="cart_title">Инфраструктура
            <img class="toggle" src="/images/toggle_right.png"/>
        </div>
        <div class="ficha" style="display: none">
            <div class="content_text" id="inf_info">
                <?= $object['infrastructure'] ?>
            </div>
            <?php if(!empty($object['infrastructure_image'])){?>
            <div style="margin:30px 0;">
                <div class="connected-carousels">
                    <div class="stage">
                        <div class="carousel carousel-stage car-s-in">
                            <ul>
                                <?php foreach ($object['infrastructure_image'] as $val) { ?>
                                    <li>
                                        <a class="fancybox-thumbs" title="<?= $val['comment'] ?>" rel="thumb_gen_d" href="<?= $val['img'] ?>">
                                            <img src="<?= $val['img'] ?>"/>
                                        </a>
                                    </li>
                                    <?}?>
                                </ul>
                            </div>
                            <a href="javascript:void(0)" class="prev prev-stage"><img src="/images/arrows/opacity_l.png" /></a>
                            <a href="javascript:void(0)" class="next next-stage"><img src="/images/arrows/opacity_r.png" /></a>
                        </div>
                        <div class="navigation">
                            <div class="carousel carousel-navigation car-n-in">
                                <ul>
                                    <?php $infrastructure_image_count = count($object['infrastructure_image']) - 1; ?>
                                    <?php foreach ($object['infrastructure_image'] as $key => $val) { ?>
                                        <li class="<?= ($key == 0) ? 'first_li' : (($infrastructure_image_count == $key) ? 'last_li' : 'preview_li') ?>">
                                            <img src="<?= $val['img'] ?>"/>
                                        </li>
                                        <?}?>
                                    </ul>
                                </div>
                                <p style="margin-top:16px" class="jcarousel-pagination"></p>
                            </div>
                            <div style="clear:both"></div>
                        </div>
                    </div>
                    <?}?>
                </div>
            </div>
            <?php if ($object['builders']): ?>
                <div class="sellers">
                    <div class="cart_title">Застройщики объекта
                        <img class="toggle" src="/images/toggle_right.png"/>
                    </div>
                    <div style="margin-top: 30px;margin-bottom: -10px;display: none;">
                        <?php foreach ($object['builders'] as $val): $_params = element('params', $val, array()); ?>
                            <div class="cont">
                                <div style="width: 100%;height: 102px;text-align: center;">
                                    <img style="max-width: 100%;max-height: 100%;" src="<?= element('image', $val, '/images/nologo.jpg') ?>">
                                </div>
                                <div class="text"><?= element('name', $val, '') ?></div>
                                <div class="all_text">
                                    <?= implode(', ', element('phone', $_params, array())) ?><br/>
                                    <?= element('address', $_params, '') ?><br/>
                                    <a href="/kartoteka/organizations/<?= element('alias', $val, '') ?>">Подробнее</a><br/>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="clear"></div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($object['sellers']): ?>
                <div class="sellers">
                    <div class="cart_title">Продавцы объекта
                        <img class="toggle" src="/images/toggle_right.png"/>
                    </div>
                    <div style="margin-top: 30px;margin-bottom: -10px;display: none;">
                        <?php foreach ($object['sellers'] as $val): $_params = element('params', $val, array()); ?>
                            <div class="cont">
                                <div style="width: 100%;height: 102px;text-align: center;">
                                    <img style="max-width: 100%;max-height: 100%;" src="<?= element('image', $val, '/images/nologo.jpg') ?>">
                                </div>
                                <div class="text"><?= element('name', $val, '') ?></div>
                                <div class="all_text">
                                    <?= implode(', ', element('phone', $_params, array())) ?><br/>
                                    <?= element('address', $_params, '') ?><br/>
                                    <a href="/kartoteka/organizations/<?= element('alias', $val, '') ?>">Подробнее</a><br/>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="clear"></div>
                    </div>
                </div>
            <?php endif; ?>
</div>