<div class="tab-pane active" id="tab6">
    <br />
    <div class="row">
            <form class="form-horizontal" role="form" action="/admin/objects/documents/<?=$object_id?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="object_id" value="<?=$object_id?>" />
                <div style="padding: 5px;">
                    <?if(!empty($documents)){?>
                        <div id="checkboxes">
                            <?foreach($documents as $value){?>
                                    <div id="check_<?=$value->id?>" style="float: left; margin: 10px;">
                                        <div style="float: left;margin: 3px;">
                                            <input type="checkbox" value="<?=$value->id?>" />
                                        </div>
                                        <div style="float: left;text-align: center;">
                                            <a href="<?=$value->file?>"><img height="100px" src="/css/images/file_icons/<?=$value->icon?>.png" /></a>
                                            <input style="margin-top:5px" id="<?=$value->id?>" class="form-control" name="comment" value="<?=$value->name?>" />
                                        </div>
                                    </div>
                            <?}?>
                            <div style="clear: both;"></div>
                        </div>
                    
                        <a href="#" style="padding: 15px;" onclick="delete_images('files');return false;">Удалить выбранные</a>
                        <a href="#" style="padding: 15px;" id="comment_link" onclick="update_comments('files');return false;">Сохранить имена файлов</a>
                        <br /><br />
                    <?}?>
                    <div id="uploader_1">
                        <div id="upl_1_1" class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Файл</label>
                            <div class="col-sm-5">
                                <input type="text" id="comment_1_1" name="comment_1_1" class="form-control" placeholder="Имя файла"/>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="file" name="file_1_1" class="form-control" id="file_1_1" placeholder=""/>
                                    <span class="input-group-btn">
                                        <button onclick="add_gallery_upload(1,1)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
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
</div>