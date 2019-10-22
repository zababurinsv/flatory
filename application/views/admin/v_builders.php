<div class="tab-pane active" id="tab8">
    <br/>
    <form class="form-horizontal" method="POST" action="/admin/objects/builders/<?=$object_id?>">
        <?if(!empty($ids)){?>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Назначенные застройщики</label>
                <div class="input-group">
                    <div class="col-sm-5">
                        <table>
                            <?foreach($ids as $value){?>
                                <?foreach($builders as $val){?>
                                    <?if($value->company_id == $val->id){?>
                                        <tr id="td_<?=$val->id?>">
                                            <td nowrap style="padding-right: 30px;"><?=$val->company_name?></td><td><a href="#" onclick="delete_builder(<?=$val->id?>,<?=$object_id?>);return false;">Удалить</a></td>
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
        <div id="builders">
            <div id="sel_1" class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Назначить застройщика</label>
                <div class="input-group">
                    <div class="col-sm-5">
                        <select id="builder_1" name="builder_1" class="form-control">
                            <option value="">Не выбрано</option>
                            <?foreach($builders as $val){?>
                                <option value="<?=$val->id?>"><?=$val->company_name?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <span class="input-group-btn">
                            <button onclick="add_builders(1)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
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