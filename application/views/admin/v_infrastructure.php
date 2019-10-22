<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<div class="tab-pane active" id="tab7">
    <br/>
    <form class="form-horizontal" method="POST" action="/admin/objects/infrastructure/<?=$object_id?>" enctype="multipart/form-data">
        <?if(isset($images)&&!empty($images)){?>       
                <div id="checkboxes">
                    <?foreach($images as $value){?>
                            <div id="check_<?=$value->id?>" style="float: left; margin: 10px;">
                                <div style="float: left;margin: 3px;">
                                    <input type="checkbox" value="<?=$value->id?>" />
                                </div>
                                <div style="float: left;text-align: center;">
                                    <img height="100px" src="<?=$value->img?>" />
                                    <input style="margin-top:5px;width: 150px;" id="<?=$value->id?>" class="form-control" name="comment" value="<?=$value->comment?>" />
                                </div>
                            </div>
                    <?}?>
                    <div style="clear: both;"></div>
                </div>                
            <a href="#" style="padding: 15px;" onclick="delete_images('infrastructure');return false;">Удалить выбранные</a>
            <a href="#" style="padding: 15px;" id="comment_link" onclick="update_comments('infrastructure_images');return false;">Сохранить комментарии</a><br /><br />
        <?}?>
        <div id="uploader">
            <div id="upl_1" class="form-group">
                <label id="label_1" for="file_1" class="col-sm-2 control-label">Фото 1</label>
                <div class="input-group">
                    <div class="col-sm-5">
                            <input type="text" id="comment_1" name="comment_1" class="form-control" placeholder="Комментарий"/>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="file" name="file_1" class="form-control" id="file_1" placeholder=""/>
                            <span class="input-group-btn">
                                <button onclick="add_new_upload(1)" class="btn btn-default" type="button"><span class="glyphicon glyphicon-plus"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="inputText3" class="col-sm-2 control-label">Описание</label>
            <div class="col-sm-10">
            <textarea class="form-control ckeditor" name="text" rows="3"><?=(isset($text))?$text:''?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </form>
</div>