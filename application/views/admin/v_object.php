<div class="page-header">
    <h2>Все объекты</h2>
</div>
<div class="row">
    <div class="col-md-6">
        <a href="/admin/objects/general_info" class="btn btn-primary">Добавить новый объект +</a>
    </div>
    <div class="col-md-6">
        <form id="custom-search-form" class="pull-right">
            <div>
                <div class="input-group">
                    <form method="GET" action="/admin/objects/">
                        <input type="text" name="search" placeholder="Поиск" class="form-control"/>
                        <span class="input-group-btn">
                            <input type="submit" value="Найти" class="btn btn-default" />
                        </span>
                    </form>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </form>
        Сортировать по:
        <a href="/admin/objects/?type=name">названию</a> |
        <a href="/admin/objects/?type=date">дате добавления</a> |
        <a href="/admin/objects/?type=adres">адресу</a> |
    </div>
</div>

    <br/>
    <?$i=0;?>
    <?foreach($object as $val){?>
<?if($i%3 == 0){?>
<div class="row">
<?}?>
    <div id="object_<?=$val->id?>" class="col-sm-6 col-md-4">
        <div class="thumbnail">
            <div style="width: 320px;height: 250px;text-align: center;vertical-align: middle;display: table-cell;">
                <?if(!empty($images[$i])){?>
                    <img style="height:215px;width: 286px;" src="<?=$images[$i]?>" alt="..."/>
                <?} else {?>
                    <a href="/admin/cart/<?=$val->id?>" class="btn btn-primary" role="button">Создать карточку</a>
                <?}?>
            </div>
            <div style="height: 34px;">
                <?if(!empty($images[$i])){?>
                    <a style="margin-left: 80px;" href="/admin/cart/<?=$val->id?>" class="btn btn-primary" role="button">Изменить карточку</a>
                <?}?>
            </div>
            <div class="caption">
                <h3><?=$val->name?></h3>
                <p><?=$val->adres?></p>
                <div class="modal fade" id="myModal<?=$val->id?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel<?=$val->id?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" onclick="$('#myModal<?=$val->id?>').css('display','none')" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel<?=$val->id?>">Подтверждение действия</h4>
                            </div>
                            <div class="modal-body">
                                Вы действительно хотите удалить всю информацию об объекте?
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="$('#myModal<?=$val->id?>').css('display','none')" class="btn btn-default" data-dismiss="modal">Отменить</button>
                                <button type="button" onclick="delete_object(<?=$val->id?>,'myModal<?=$val->id?>')" class="btn btn-danger">Удалить</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
                <p>
                    <?if($val->status!=1){?>
                        <a href="/admin/objects/general_info/<?=$val->id?>" class="btn btn-primary" role="button">Завершить создание</a>
                    <?} else {?>
                        <a href="/admin/objects/general_info/<?=$val->id?>" class="btn btn-primary" role="button">Изменить</a>
                    <?}?>
                    <a href="#" class="btn btn-danger" role="button" data-toggle="modal" onclick="$('#myModal<?=$val->id?>').show().attr('class','modal fade in');return false;">Удалить</a>
                </p>
            </div>
        </div>
    </div>
<?if($i%3==2){?>
</div>
<?}?>
    <?$i++?>
    <?}?>
</div>
</div>
</div>