<div class="login" style="height: auto;">
    <div class="auth">
        <div class="page-name">Регистрация</div>
        <div class="reg_form">
            <?=(isset($error)?$error:"")?>
            <form action="/reg/registration" id="reg_form" method="post" class="form reg-form" role="form">
                <div class="row">
                    <input style="float:left" name="lastname" placeholder="Фамилия*" value="<?=set_value('lastname')?>" type="text" required autofocus />
                    <input style="float:right" name="firstname" placeholder="Имя*" value="<?=set_value('firstname')?>" type="text" />
                    <div style="clear:both;"></div>
                </div>
                <div class="row">
                    
                    <input style="float:left" name="email" value="<?=set_value('email')?>" placeholder="E-mail*" type="email" />
                    <div style="clear:both;"></div>
                </div>
                <div class="row">
                    <input style="float:left"  name="password" placeholder="Пароль*" type="password" />
                    <input style="float:right" name="password2" placeholder="Подтвердите пароль*" type="password" />
                    <div style="clear:both;"></div>
                </div>
                <div class="row" style="margin-top: 35px;">
                    <div style="width: 49%;float: left;position: relative">
                        <label style="font-family: 'Open Sans'; font-weight: 400;  font-size: 13px;  color: rgb( 58, 57, 57 );">Возраст</label>
                        <div class="select_face_e select_face">
                            <span><?=set_value('strength_range','16')?></span>
                            <input type="hidden" id="strength_range" value="<?=set_value('strength_range','Москва')?>" name="strength_range" />
                            <img style="width: 19px;height: 11px;" src="/images/new/select_button.png"/>
                        </div>
                        <div class="select_body_e select_body" style="display: none;">
                            <table>
                                <?for($i=16;$i<100;$i++){?>
                                <tr>
                                    <td><?=$i?></td>
                                </tr>
                                <?}?>
                            </table>
                        </div>
                    </div>
                    <div style="width: 49%;float: right;">
                        <div style="width: 49%;float: left;position: relative">
                            <label style="font-family: 'Open Sans'; font-weight: 400;  font-size: 13px;  color: rgb( 58, 57, 57 );">Регион</label>
                            <div class="select_face_g select_face">
                                <span><?=set_value('region','Москва')?></span>
                                <input type="hidden" id="region" value="<?=set_value('region','Москва')?>" name="region" />
                                <img style="width: 19px;height: 11px;" src="/images/new/select_button.png"/>
                            </div>
                            <div class="select_body_g select_body" style="display: none;">
                                <table>
                                        <tr><td>Москва</td></tr>
                                        <tr><td>Московская область</td></tr>
                                        <tr><td>Адыгея</td></tr>
                                        <tr><td>Алтайский край</td></tr>
                                        <tr><td>Амурская область</td></tr>
                                        <tr><td>Архангельская область</td></tr>
                                        <tr><td>Астраханская область</td></tr>
                                        <tr><td>Башкортостан</td></tr>
                                        <tr><td>Белгородская область</td></tr>
                                        <tr><td>Брянская область</td></tr>
                                        <tr><td>Бурятия</td></tr>
                                        <tr><td>Владимирская область</td></tr>
                                        <tr><td>Волгоградская область</td></tr>
                                        <tr><td>Вологодская область</td></tr>
                                        <tr><td>Воронежская область</td></tr>
                                        <tr><td>Дагестан</td></tr>
                                        <tr><td>Еврейская автономная область</td></tr>
                                        <tr><td>Забайкальский край</td></tr>
                                        <tr><td>Ивановская область</td></tr>
                                        <tr><td>Ингушетия</td></tr>
                                        <tr><td>Иркутская область</td></tr>
                                        <tr><td>Кабардино-Балкария</td></tr>
                                        <tr><td>Калининградская область</td></tr>
                                        <tr><td>Калмыкия</td></tr>
                                        <tr><td>Калужская область</td></tr>
                                        <tr><td>Камчатский край</td></tr>
                                        <tr><td>Карачаево-Черкесия</td></tr>
                                        <tr><td>Карелия</td></tr>
                                        <tr><td>Кемеровская область</td></tr>
                                        <tr><td>Кировская область</td></tr>
                                        <tr><td>Костромская область</td></tr>
                                        <tr><td>Краснодарский край</td></tr>
                                        <tr><td>Красноярский край</td></tr>
                                        <tr><td>Курганская область</td></tr>
                                        <tr><td>Курская область</td></tr>
                                        <tr><td>Ленинградская область</td></tr>
                                        <tr><td>Липецкая область</td></tr>
                                        <tr><td>Магаданская область</td></tr>
                                        <tr><td>Марий Эл</td></tr>
                                        <tr><td>Мордовия</td></tr>
                                        <tr><td>Мурманская область</td></tr>
                                        <tr><td>Ненецкий автономный округ</td></tr>
                                        <tr><td>Нижегородская область</td></tr>
                                        <tr><td>Новгородская область</td></tr>
                                        <tr><td>Новосибирская область</td></tr>
                                        <tr><td>Омская область</td></tr>
                                        <tr><td>Оренбургская область</td></tr>
                                        <tr><td>Орловская область</td></tr>
                                        <tr><td>Пензенская область</td></tr>
                                        <tr><td>Пермский край</td></tr>
                                        <tr><td>Приморский край</td></tr>
                                        <tr><td>Псковская область</td></tr>
                                        <tr><td>Республика Алтай</td></tr>
                                        <tr><td>Республика Коми</td></tr>
                                        <tr><td>Республика Саха</td></tr>
                                        <tr><td>Ростовская область</td></tr>
                                        <tr><td>Рязанская область</td></tr>
                                        <tr><td>Самарская область</td></tr>
                                        <tr><td>Санкт-Петербург</td></tr>
                                        <tr><td>Саратовская область</td></tr>
                                        <tr><td>Сахалинская область</td></tr>
                                        <tr><td>Свердловская область</td></tr>
                                        <tr><td>Северная Осетия</td></tr>
                                        <tr><td>Смоленская область</td></tr>
                                        <tr><td>Ставропольский край</td></tr>
                                        <tr><td>Тамбовская область</td></tr>
                                        <tr><td>Татарстан</td></tr>
                                        <tr><td>Тверская область</td></tr>
                                        <tr><td>Томская область</td></tr>
                                        <tr><td>Тульская область</td></tr>
                                        <tr><td>Тыва</td></tr>
                                        <tr><td>Тюменская область</td></tr>
                                        <tr><td>Удмуртия</td></tr>
                                        <tr><td>Ульяновская область</td></tr>
                                        <tr><td>Хабаровский край</td></tr>
                                        <tr><td>Хакасия</td></tr>
                                        <tr><td>Ханты-Мансийский автономный округ</td></tr>
                                        <tr><td>Челябинская область</td></tr>
                                        <tr><td>Чечня</td></tr>
                                        <tr><td>Чувашия</td></tr>
                                        <tr><td>Чукотский автономный округ</td></tr>
                                        <tr><td>Ямало-Ненецкий автономный округ</td></tr>
                                        <tr><td>Ярославская область</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                </div>
                <div class="button_bg" style="width: 266px">
                    <div class="button_face">
                        <span onclick="$('#reg_form').submit()">Зарегистрироваться</span>
                    </div>
                </div>
            </form>
            <div style="clear:both;"></div>
        </div>
    </div>
</div>