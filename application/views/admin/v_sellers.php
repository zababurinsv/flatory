<div class="tab-pane active" id="tab8">
    <br/>
    <div style="position: absolute; display:none; left: 35%;margin-top:-80px;" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" onclick="$('.modal-dialog').hide();" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Новый продавец</h4>
            </div>
            <form method="POST" action="/admin/objects/add_seller/<?=$object_id?>" enctype="multipart/form-data" class="form-horizontal" role="form">
                <div class="col-sm-12">
                    <br>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Логотип</label>
                        <div class="col-sm-10">
                            <input type="file" name="logo" class="form-control" id="inputEmail3" placeholder="Кликните для загрузки логотипа"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Название компании</label>
                        <div class="col-sm-10">
                            <input type="text" name="company_name" class="form-control" id="inputEmail3" placeholder="Ввести название организации"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Адрес</label>
                        <div class="col-sm-10">
                            <input type="text" name="adres" class="form-control" id="inputEmail3" placeholder="Ввести адрес"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Веб-сайт</label>
                        <div class="col-sm-10">
                            <input type="url" name="sait" class="form-control" id="inputEmail3" placeholder="http://"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Телефоны</label>
                        <div class="col-sm-5">
                            <input type="phone" name="phone1" class="form-control" placeholder="телефон_1"/>
                        </div>
                        <div class="col-sm-5">
                            <input type="phone" name="phone2" class="form-control" placeholder="телефон_2"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Инфо</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="info" rows="3" placeholder="Дополнительная информация о продавце"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="$('.modal-dialog').hide('slow')" class="btn btn-default" data-dismiss="modal">Отменить</button>
                    <input type="submit" class="btn btn-success" value="Сохранить" />
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
<!--    <div class="form-group">-->
<!--        <div class="col-sm-2">-->
<!--            <input type="submit" onclick="$('.modal-dialog').show('slow')" class="btn btn-primary" value="+ Добавить компанию"/>-->
<!--        </div>-->
<!--    </div>-->
<!--    <br /><br /><br />-->
    <form class="form-horizontal" method="POST" action="/admin/objects/sellers/<?=$object_id?>">
        <?if(!empty($ids)){?>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Назначенные застройщики</label>
                <div class="input-group">
                    <div class="col-sm-5">
                        <table>
                            <?foreach($ids as $value){?>
                                <?foreach($sellers as $val){?>
                                    <?if($value->company_id == $val->id){?>
                                        <tr id="td_<?=$val->id?>">
                                            <td nowrap style="padding-right: 30px;"><?=$val->company_name?></td><td><a href="#" onclick="delete_seller(<?=$val->id?>,<?=$object_id?>);return false;">Удалить</a></td>
                                        </tr>
                                    <?}?>
                                <?}?>
                            <?}?>
                        </table>
                    </div>
                </div>
            </div>

            <br />
        <?}?>

        <div id="sellers">
            <div id="sel_1" class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Назначить продавца</label>
                <div class="input-group">
                    <div class="col-sm-5">
                        <select id="seller_1" name="seller_1" class="form-control">
                            <option value="">Не выбрано</option>
                            <?foreach($sellers as $val){?>
                                <option value="<?=$val->id?>"><?=$val->company_name?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <span class="input-group-btn">
                            <button onclick="add_sellers(1)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </form>
</div>