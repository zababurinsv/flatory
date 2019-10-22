<div class="tab-pane active" id="tab6">
    <br />
    <div class="row">
        <form method="POST" action="/admin/objects/add_album/<?=$object_id?>" enctype="multipart/form-data">
            <div class="form-group">
                <div class="col-md-5">
                    <input  name="name" type="text" name="distance_to_metro" min="0" class="form-control"/>
                </div>
                <div class="col-sm-2">
                    <input type="submit" class="btn btn-primary" value="+ Создать альбом"/>
                </div>
            </div>
        </form>
        <br /><br />
        <?foreach($albums as $val){?>
            <div id="album_<?=$val->id?>" class="col-sm-offset-12 col-sm-10" style="background-color: #e6e6e6;padding: 10px;border-radius: 7px;margin-bottom:10px;">
                <label style="cursor: pointer;border-bottom: 1px dashed #428bca;color:#428bca;margin: 6px;" onclick="$(this).next().next().next().next().toggle();if($(this).next().next().next().next().css('display') == 'none') {$(this).children().text('(Развернуть)')} else {$(this).children().text('(Свернуть)')}"><?=$val->name?> <span>(Развернуть)</span></label><label style="cursor: pointer;float:right;margin: 6px;" onclick="delete_plan_album(<?=$val->object_id?>)">Удалить альбом</label><label style="cursor: pointer;float:right;padding-left: 4px;padding-right: 4px;margin: 6px;border-bottom: 1px dashed #428bca;color:#428bca" onclick="edit_album_name($(this),<?=$val->id?>,'albums')">Переименовать</label><input class="form-control col-sm-5" style="float: right;width: 200px;" name="album_name" value="" placeholder="Новое название альбома" />
                <form style="display:none;" class="form-horizontal" role="form" action="/admin/objects/gallery/<?=$object_id?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="album_id" value="<?=$val->id?>" />
                    <div style="padding: 5px;">
                        <div id="checkboxes">
                            <?foreach($images as $value){?>
                                <?if($val->id == $value->album_id){?>
                                    <div id="check_<?=$value->id?>" style="float: left; margin: 10px;">
                                        <div style="float: left;margin: 3px;">
                                            <input type="checkbox" value="<?=$value->id?>" />
                                        </div>
                                        <div style="float: left;text-align: center;">
                                            <img height="100px" src="<?=$value->img?>" />
                                            <input style="margin-top:5px" id="<?=$value->id?>" class="form-control" name="comment" value="<?=$value->comment?>" />
                                        </div>
                                    </div>
                                <?}?>
                            <?}?>
                            <div style="clear: both;"></div>
                        </div>
                        <?if(!empty($images)){?>
                            <a href="#" style="padding: 15px;" onclick="delete_images('album');return false;">Удалить выбранные</a>
                            <a href="#" style="padding: 15px;" id="comment_link" onclick="update_comments('scrin_full');return false;">Сохранить комментарии</a>
                        <?}?>
                        <br /><br />
                        <div id="uploader_<?=$val->id?>">
                            <div id="upl_<?=$val->id?>_1" class="form-group">
                                <label id="label_<?=$val->id?>_1" for="file_<?=$val->id?>_1" class="col-sm-2 control-label">Фото 1</label>
                                <div class="col-sm-5">
                                        <input type="text" id="comment_<?=$val->id?>_1" name="comment_<?=$val->id?>_1" class="form-control" placeholder="Комментарий"/>
                                </div>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="file" name="file_<?=$val->id?>_1" class="form-control" id="file_<?=$val->id?>_1" placeholder=""/>
                                        <span class="input-group-btn">
                                            <button onclick="add_gallery_upload(1,<?=$val->id?>)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
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
                    </div>
                </form>
            </div>
        <?}?>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">

            </div>
        </div>
    </div>
</div>