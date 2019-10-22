<script src="/js/ckeditor/ckeditor.js" type="text/javascript"></script>
<script src="/js/ckeditor/config.js" type="text/javascript"></script>
<script src="/js/ckeditor/styles.js" type="text/javascript"></script>
<div class="tab-pane active" id="tab7">
    <br/>
    <form class="form-horizontal" method="POST" action="/admin/objects/seo/<?=$object_id?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Заголовок<br/>(title)</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" placeholder="" name="title" value="<?=(isset($title)) ? htmlspecialchars($title,ENT_QUOTES):''?>" />
            </div>
        </div>
        <div class="form-group">
            <label for="inputText3" class="col-sm-2 control-label">Описание<br/>(description)</label>
            <div class="col-sm-10">
            <textarea class="form-control" name="description" rows="3"><?=(isset($description))?$description:''?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputText3" class="col-sm-2 control-label">Ключевые слова<br/>(keywords)</label>
            <div class="col-sm-10">
            <textarea class="form-control" name="keywords" rows="3"><?=(isset($keywords))?$keywords:''?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-12 col-sm-10">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </div>
    </form>
</div>