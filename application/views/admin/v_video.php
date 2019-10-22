<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<div class="tab-pane active" id="tab7">
    <br/>
    <form class="form-horizontal" method="POST" action="/admin/objects/video/<?=$object_id?>" enctype="multipart/form-data">
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